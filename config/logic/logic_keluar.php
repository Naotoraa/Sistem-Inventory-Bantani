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
$minggu = $_GET['minggu'] ?? '';
$conditions = [];

// Filter pencarian
if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $conditions[] = "(id_barang LIKE '%$cari%' OR nama_barang LIKE '%$cari%' OR kategori LIKE '%$cari%')";
}

// Filter bulan dan minggu
if (!empty($bulan)) {
    [$tahun, $bulanNum] = explode('-', $bulan);
    $bulanNum = str_pad($bulanNum, 2, '0', STR_PAD_LEFT); // pastikan dua digit

    // Default: seluruh bulan
    $start = "$tahun-$bulanNum-01";
    $end = date("Y-m-t", strtotime($start)); // akhir bulan

    if (!empty($minggu)) {
        switch ((int) $minggu) {
            case 1:
                $start = "$tahun-$bulanNum-01";
                $end = "$tahun-$bulanNum-07";
                break;
            case 2:
                $start = "$tahun-$bulanNum-08";
                $end = "$tahun-$bulanNum-14";
                break;
            case 3:
                $start = "$tahun-$bulanNum-15";
                $end = "$tahun-$bulanNum-21";
                break;
            case 4:
                $start = "$tahun-$bulanNum-22";
                $end = date("Y-m-t", strtotime("$tahun-$bulanNum-01"));
                break;
        }
    }

    $conditions[] = "tanggal_keluar BETWEEN '$start' AND '$end'";
}

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "SELECT * FROM barang_keluar $whereClause ORDER BY id DESC";
$result = $conn->query($sql);


//Save Action
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    include '../../config/conn.php'; // pastikan koneksi ada

    if ($_POST['action'] == 'update') {
        $id = $conn->real_escape_string($_POST['id'] ?? '');
        $id_barang = $conn->real_escape_string($_POST['id_barang'] ?? '');
        $nama_barang = $conn->real_escape_string($_POST['name'] ?? '');
        $kategori = $conn->real_escape_string($_POST['category'] ?? '');


        $qty = (int) ($_POST['qty'] ?? 0);

        function getStokSaatIni($conn, $id_barang)
        {
            $masuk   = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_masuk WHERE id_barang = '$id_barang'"))['total'] ?? 0;
            $keluar  = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_keluar WHERE id_barang = '$id_barang'"))['total'] ?? 0;
            $migrasi = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_migrasi WHERE id_barang = '$id_barang'"))['total'] ?? 0;
            $eror    = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_eror WHERE id_barang = '$id_barang'"))['total'] ?? 0;

            return $masuk - $keluar + $migrasi - $eror;
        }

        $stok_sekarang = getStokSaatIni($conn, $id_barang);

        if ($qty > $stok_sekarang) {
            header("Location: barang_keluar.php?error=overstock&stok=$stok_sekarang");
            exit;
        }

        $satuan = $conn->real_escape_string($_POST['satuan'] ?? '');
        $tanggal_keluar = $conn->real_escape_string($_POST['date'] ?? '');

        $sql = "UPDATE barang_keluar SET id_barang = ?, nama_barang = ?, kategori = ?, qty = ?, satuan = ?, tanggal_keluar = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssissi", $id_barang, $nama_barang, $kategori, $qty, $satuan, $tanggal_keluar, $id);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_keluar.php?status=updated';</script>";
            } else {
                echo "<script>alert('Gagal Update: " . addslashes($stmt->error) . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_keluar.php?status=error';</script>";
        }

        $conn->close();
    } elseif ($_POST['action'] == 'insert') {
        $id_barang = $conn->real_escape_string($_POST['id_barang'] ?? '');
        $nama_barang = $conn->real_escape_string($_POST['name'] ?? '');
        $kategori = $conn->real_escape_string($_POST['category'] ?? '');

        $qty = (int) ($_POST['qty'] ?? 0);

        function getStokSaatIni($conn, $id_barang)
        {
            $masuk   = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_masuk WHERE id_barang = '$id_barang'"))['total'] ?? 0;
            $keluar  = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_keluar WHERE id_barang = '$id_barang'"))['total'] ?? 0;
            $migrasi = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_migrasi WHERE id_barang = '$id_barang'"))['total'] ?? 0;
            $eror    = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_eror WHERE id_barang = '$id_barang'"))['total'] ?? 0;

            return $masuk - $keluar + $migrasi - $eror;
        }

        $stok_sekarang = getStokSaatIni($conn, $id_barang);

        if ($qty > $stok_sekarang) {
            header("Location: barang_keluar.php?error=overstock&stok=$stok_sekarang");
            exit;
        }

        $satuan = $conn->real_escape_string($_POST['satuan'] ?? '');
        $tanggal_keluar = $conn->real_escape_string($_POST['date'] ?? '');

        $sql = "INSERT INTO barang_keluar (id_barang, nama_barang, kategori, qty, satuan, tanggal_keluar) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssiss", $id_barang, $nama_barang, $kategori, $qty, $satuan, $tanggal_keluar);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_keluar.php?status=inserted';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan data: " . addslashes($stmt->error) . "'); window.history.back();</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_keluar.php?status=error';</script>";
        }

        $conn->close();
    }
}

//Delete Action
if (isset($_GET['hapus_data'])) {
    $id = $_GET['hapus_data'];

    $sql = "DELETE FROM barang_keluar WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='barang_keluar.php?status=deleted';</script>";
    } else {
        echo "<script>window.location.href='barang_keluar.php?status=error';</script>";
    }

    $conn->close();
}

//Update Action
if (isset($_GET['update_row'])) {
    $id = $_GET['update_row'];

    $data_update = $conn->query("SELECT * FROM barang_keluar WHERE id = '$id' LIMIT 1");

    if ($data_update->num_rows < 1) {
        echo "<script>alert('Data sudah dihapus atau tidak ada'); location.href='../../pages/Inventory/barang_keluar.php'</script>";
        exit();
    }

    $data_update = $data_update->fetch_assoc();
}