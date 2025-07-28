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

//Search Action
$cari = $_GET['cari'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$conditions = [];

if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $conditions[] = "(id_barang LIKE '%$cari%' OR nama_barang LIKE '%$cari%' OR kategori LIKE '%$cari%')";
}
if (!empty($bulan)) {
    $conditions[] = "DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$bulan'";
}
$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}
$sql = "SELECT * FROM barang_masuk $whereClause ORDER BY id DESC";
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
        $satuan = $conn->real_escape_string($_POST['satuan'] ?? '');
        $tanggal_masuk = $conn->real_escape_string($_POST['date'] ?? '');

        $sql = "UPDATE barang_masuk SET id_barang = ?, nama_barang = ?, kategori = ?, qty = ?, satuan = ?, tanggal_masuk = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssissi", $id_barang, $nama_barang, $kategori, $qty, $satuan, $tanggal_masuk, $id);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_masuk.php?status=updated';</script>";
            } else {
                echo "<script>alert('Gagal Update: " . addslashes($stmt->error) . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_masuk.php?status=error';</script>";
        }

        $conn->close();
    } elseif ($_POST['action'] == 'insert') {
        $id_barang = $conn->real_escape_string($_POST['id_barang'] ?? '');
        $nama_barang = $conn->real_escape_string($_POST['name'] ?? '');
        $kategori = $conn->real_escape_string($_POST['category'] ?? '');
        $qty = (int) ($_POST['qty'] ?? 0);
        $satuan = $conn->real_escape_string($_POST['satuan'] ?? '');
        $tanggal_masuk = $conn->real_escape_string($_POST['date'] ?? '');

        $sql = "INSERT INTO barang_masuk (id_barang, nama_barang, kategori, qty, satuan, tanggal_masuk) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssiss", $id_barang, $nama_barang, $kategori, $qty, $satuan, $tanggal_masuk);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_masuk.php?status=inserted';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan data: " . addslashes($stmt->error) . "'); window.history.back();</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_masuk.php?status=error';</script>";
        }

        $conn->close();
    }
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

    $data_update = $conn->query("SELECT * FROM barang_masuk WHERE id = '$id' LIMIT 1 ");

    if ($data_update->num_rows < 1) {
        echo "<script>alert('Data sudah dihapus atau tidak ada'); location.href='../../pages/Inventory/barang_masuk.php'</script>";
        exit();
    }

    $data_update = $data_update->fetch_assoc();
}
