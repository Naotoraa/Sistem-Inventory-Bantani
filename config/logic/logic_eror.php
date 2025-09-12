<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Menu/login.php?expired=1");
    exit();
}

require '../../config/conn.php';

$barangList = [];
$satuanSet = [];
$satuanList = [];

$result = $conn->query("SELECT * FROM barang");
while ($row = $result->fetch_assoc()) {
    $barangList[] = $row;

    // Ambil satuan unik
    $satuan = trim($row['satuan']);
    if ($satuan !== '' && !isset($satuanSet[$satuan])) {
        $satuanSet[$satuan] = true;
        $satuanList[] = $satuan;
    }
}
//Search Action
$cari = $_GET['cari'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$conditions = [];

if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $conditions[] = "(er.id_barang LIKE '%$cari%' 
                      OR b.nama_barang LIKE '%$cari%' 
                      OR er.kategori LIKE '%$cari%')";
}

if (!empty($bulan)) {
    $conditions[] = "DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'";
}
$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}
$sql = "
    SELECT er.id, er.id_barang, b.nama_barang, er.kategori, er.qty, er.tanggal, er.keterangan
    FROM barang_eror er
    JOIN barang b ON er.id_barang = b.id_barang
    $whereClause
    ORDER BY er.id DESC
";
$result = $conn->query($sql);

// Save Action
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    include '../../config/conn.php'; // koneksi

    // Fungsi stok sekarang
    function getStokSaatIni($conn, $id_barang)
    {
        $masuk   = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_masuk WHERE id_barang = '$id_barang'"))['total'] ?? 0);
        $keluar  = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_keluar WHERE id_barang = '$id_barang'"))['total'] ?? 0);
        $migrasi = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_migrasi WHERE id_barang = '$id_barang'"))['total'] ?? 0);
        $eror    = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_eror WHERE id_barang = '$id_barang'"))['total'] ?? 0);

        return $masuk - $keluar + $migrasi - $eror;
    }

    // ================= UPDATE =================
    if ($_POST['action'] == 'update') {
        $id         = $conn->real_escape_string($_POST['id'] ?? '');
        $id_barang  = $conn->real_escape_string($_POST['id_barang'] ?? '');
        $kategori   = $conn->real_escape_string($_POST['category'] ?? '');
        $tanggal    = $conn->real_escape_string($_POST['date'] ?? '');
        $keterangan = $conn->real_escape_string($_POST['keterangan'] ?? '');
        $satuan = $conn->real_escape_string($_POST['satuan'] ?? '');
        $qty        = (int) ($_POST['qty'] ?? 0);

        $stok_sekarang = getStokSaatIni($conn, $id_barang);

        if ($qty > $stok_sekarang) {
            header("Location: ../../pages/Inventory/barang_eror.php?error=overstock&stok=$stok_sekarang");
            exit;
        }

        $sql = "UPDATE barang_eror 
                SET id_barang = ?, kategori = ?, qty = ?, tanggal = ?, keterangan = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssissi", $id_barang, $kategori, $qty, $tanggal, $keterangan, $id);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_eror.php?status=updated';</script>";
            } else {
                echo "<script>alert('Gagal Update: " . addslashes($stmt->error) . "');</script>";
            }
            $stmt->close();
        }
        $conn->close();
    }

    // ================= INSERT =================
    elseif ($_POST['action'] == 'insert') {
        $id_barang  = $conn->real_escape_string($_POST['id_barang'] ?? '');
        $kategori   = $conn->real_escape_string($_POST['category'] ?? '');
        $tanggal    = $conn->real_escape_string($_POST['date'] ?? '');
        $keterangan = $conn->real_escape_string($_POST['keterangan'] ?? '');
        $qty        = (int) ($_POST['qty'] ?? 0);

        $stok_sekarang = getStokSaatIni($conn, $id_barang);

        if ($qty > $stok_sekarang) {
            header("Location: ../../pages/Inventory/barang_eror.php?error=overstock&stok=$stok_sekarang");
            exit;
        }

        $sql = "INSERT INTO barang_eror (id_barang, kategori, qty, tanggal, keterangan) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssiss", $id_barang, $kategori, $qty, $tanggal, $keterangan);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_eror.php?status=inserted';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan data: " . addslashes($stmt->error) . "'); window.history.back();</script>";
            }
            $stmt->close();
        }
        $conn->close();
    }
}

//Delete Action
if (isset($_GET['hapus_data'])) {
    $id = $_GET['hapus_data'];

    $sql = "DELETE FROM barang_eror WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='barang_eror.php?status=deleted';</script>";
    } else {
        echo "<script>window.location.href='barang_eror.php?status=error';</script>";
    }

    $conn->close();
}

//Update Action
if (isset($_GET['update_row'])) {
    $id = $conn->real_escape_string($_GET['update_row']);

    $data_update = $conn->query("SELECT * FROM barang_eror WHERE id = '$id' LIMIT 1");

    if (!$data_update || $data_update->num_rows < 1) {
        echo "<script>alert('Data sudah dihapus atau tidak ada'); location.href='../../pages/Inventory/barang_eror.php'</script>";
        exit();
    }

    $data_update = $data_update->fetch_assoc();
}
