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

    $satuan = trim($row['satuan']);
    if ($satuan !== '' && !isset($satuanSet[$satuan])) {
        $satuanSet[$satuan] = true;
        $satuanList[] = $satuan;
    }
}

$cari = $_GET['cari'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$conditions = [];

if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $conditions[] = "(bm.id_barang LIKE '%$cari%' 
                     OR b.nama_barang LIKE '%$cari%' 
                     OR b.kategori LIKE '%$cari%')";
}

if (!empty($bulan)) {
    $conditions[] = "DATE_FORMAT(bm.tanggal_masuk, '%Y-%m') = '$bulan'";
}

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "
                    SELECT bm.id, bm.id_barang, b.nama_barang, bm.kategori, bm.qty, b.satuan, bm.tanggal_masuk
                    FROM barang_masuk bm JOIN barang b ON bm.id_barang = b.id_barang $whereClause ORDER BY bm.id DESC
                    ";

$result = $conn->query($sql);

//Save Action
// ========== SAVE ACTION ==========
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    include '../../config/conn.php';

    $id_barang  = $conn->real_escape_string($_POST['id_barang'] ?? '');
    $kategori   = $conn->real_escape_string($_POST['category'] ?? '');
    $qty        = (int) ($_POST['qty'] ?? 0);
    $tanggal    = $conn->real_escape_string($_POST['date'] ?? '');

    if ($_POST['action'] == 'update') {
        $id = $conn->real_escape_string($_POST['id'] ?? '');

        // UPDATE
        $sql = "UPDATE barang_masuk 
                SET id_barang = ?, kategori = ?, qty = ?, tanggal_masuk = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssisi", $id_barang, $kategori, $qty, $tanggal, $id);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_masuk.php?status=updated';</script>";
            } else {
                echo "<script>alert('Gagal Update: " . addslashes($stmt->error) . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_masuk.php?status=error';</script>";
        }
    } elseif ($_POST['action'] == 'insert') {
        // INSERT
        $sql = "INSERT INTO barang_masuk (id_barang, kategori, qty, tanggal_masuk) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssis", $id_barang, $kategori, $qty, $tanggal);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_masuk.php?status=inserted';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan data: " . addslashes($stmt->error) . "'); window.history.back();</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_masuk.php?status=error';</script>";
        }
    }

    $conn->close();
    exit;
}

//Delete Action
if (isset($_GET['hapus_data'])) {
    $id = $_GET['hapus_data'];

    $sql = "DELETE FROM barang_masuk WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='barang_masuk.php?status=deleted';</script>";
    } else {
        echo "<script>window.location.href='barang_masuk.php?status=error';</script>";
    }

    $conn->close();
}

//Update Action
if (isset($_GET['update_row'])) {
    $id = $_GET['update_row'];

    $data_update = $conn->query("
        SELECT bm.id, bm.id_barang, b.nama_barang, bm.kategori, bm.qty, b.satuan, bm.tanggal_masuk FROM barang_masuk bm
        JOIN barang b ON bm.id_barang = b.id_barang
        WHERE bm.id = '$id'
        LIMIT 1
    ");

    if ($data_update->num_rows < 1) {
        echo "<script>alert('Data sudah dihapus atau tidak ada'); location.href='../../pages/Inventory/barang_masuk.php'</script>";
        exit();
    }

    $data_update = $data_update->fetch_assoc();
}
