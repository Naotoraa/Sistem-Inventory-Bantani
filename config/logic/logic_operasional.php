<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Menu/login.php?expired=1");
    exit();
}

require '../../config/conn.php';

$status = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $sku = $_POST['sku'] ?? '';

    if (empty($sku)) {
        header("Location: ../../pages/Expenses/operasional.php?status=$status");
        exit();
    }

    $tanggal = $_POST['tanggal'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $qty = $_POST['qty'] ?? 0;
    $satuan = $_POST['satuan'] ?? '';
    $harga = str_replace('.', '', $_POST['harga'] ?? '');
    $jumlah = str_replace('.', '', $_POST['jumlah'] ?? '');
    $keterangan = $_POST['keterangan'] ?? '';

    if ($action === 'insert') {
        $stmt_check = $conn->prepare("SELECT sku FROM operasional WHERE sku = ?");
        $stmt_check->bind_param("s", $sku);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO operasional (sku, tanggal, nama, qty, satuan, harga, jumlah, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssissis", $sku, $tanggal, $nama, $qty, $satuan, $harga, $jumlah, $keterangan);
            if ($stmt->execute()) {
                $status = 'inserted';
            }
            $stmt->close();
        }
        $stmt_check->close();
    } elseif ($action === 'update') {
        $stmt_check = $conn->prepare("SELECT sku FROM operasional WHERE sku = ?");
        $stmt_check->bind_param("s", $sku);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE operasional SET tanggal=?, nama=?, qty=?, satuan=?, harga=?, jumlah=?, keterangan=? WHERE sku=?");
            $stmt->bind_param("ssississ", $tanggal, $nama, $qty, $satuan, $harga, $jumlah, $keterangan, $sku);
            if ($stmt->execute()) {
                $status = 'updated';
            }
            $stmt->close();
        }
        $stmt_check->close();
    }

    header("Location: ../../pages/Expenses/operasional.php?status=$status");
    exit();
}

// === DELETE ===
if (isset($_GET['hapus_data'])) {
    $sku = $_GET['hapus_data'];
    $stmt = $conn->prepare("DELETE FROM operasional WHERE sku = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $stmt->close();
    header("Location: ../../pages/Expenses/operasional.php?status=deleted");
    exit();
}

// === PREVIEW UPDATE ===
$data_update = null;
if (isset($_GET['update_row'])) {
    $sku = $_GET['update_row'];
    $stmt = $conn->prepare("SELECT * FROM operasional WHERE sku = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $result = $stmt->get_result();
    $data_update = $result->fetch_assoc();
    $stmt->close();
}
