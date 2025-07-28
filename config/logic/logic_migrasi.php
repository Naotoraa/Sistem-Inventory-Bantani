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

// ========== FILTER / SEARCH ==========
$cari = $_GET['cari'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$conditions = [];

if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $conditions[] = "(id_barang LIKE '%$cari%' OR nama_barang LIKE '%$cari%' OR kategori LIKE '%$cari%')";
}
if (!empty($bulan)) {
    $conditions[] = "DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'";
}
$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "SELECT * FROM barang_migrasi $whereClause ORDER BY id DESC";
$result = $conn->query($sql);

// ========== SAVE ACTION ==========
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    include '../../config/conn.php';

    $id_barang = $conn->real_escape_string($_POST['id_barang'] ?? '');
    $nama_barang = $conn->real_escape_string($_POST['name'] ?? '');
    $kategori = $conn->real_escape_string($_POST['category'] ?? '');
    $qty = (int) ($_POST['qty'] ?? 0);
    $satuan = $conn->real_escape_string($_POST['satuan'] ?? '');
    $tanggal = $conn->real_escape_string($_POST['date'] ?? '');
    $keterangan = $conn->real_escape_string($_POST['keterangan'] ?? '');

    if ($_POST['action'] == 'update') {
        $id = $conn->real_escape_string($_POST['id'] ?? '');

        $sql = "UPDATE barang_migrasi SET id_barang = ?, nama_barang = ?, kategori = ?, qty = ?, satuan = ?, tanggal = ?, keterangan = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssisssi", $id_barang, $nama_barang, $kategori, $qty, $satuan, $tanggal, $keterangan, $id);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_migrasi.php?status=updated';</script>";
            } else {
                echo "<script>alert('Gagal Update: " . addslashes($stmt->error) . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_migrasi.php?status=error';</script>";
        }
    } elseif ($_POST['action'] == 'insert') {
        $sql = "INSERT INTO barang_migrasi (id_barang, nama_barang, kategori, qty, satuan, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssisss", $id_barang, $nama_barang, $kategori, $qty, $satuan, $tanggal, $keterangan);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_migrasi.php?status=inserted';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan data: " . addslashes($stmt->error) . "'); window.history.back();</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_migrasi.php?status=error';</script>";
        }
    }

    $conn->close();
    exit;
}

// ========== DELETE ==========
if (isset($_GET['hapus_data'])) {
    $id = $_GET['hapus_data'];

    $sql = "DELETE FROM barang_migrasi WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='barang_migrasi.php?status=deleted';</script>";
    } else {
        echo "<script>window.location.href='barang_migrasi.php?status=error';</script>";
    }

    $stmt->close();
    $conn->close();
    exit;
}

// ========== LOAD DATA UNTUK UPDATE ==========
if (isset($_GET['update_row'])) {
    $id = $_GET['update_row'];

    $data_update = $conn->query("SELECT * FROM barang_migrasi WHERE id = '$id' LIMIT 1");

    if ($data_update->num_rows < 1) {
        echo "<script>alert('Data sudah dihapus atau tidak ada'); location.href='../../pages/Inventory/barang_migrasi.php'</script>";
        exit();
    }

    $data_update = $data_update->fetch_assoc();
}
