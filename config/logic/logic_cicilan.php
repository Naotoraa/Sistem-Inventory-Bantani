<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Menu/login.php?expired=1");
    exit();
}
require '../../config/conn.php';

$status = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action          = $_POST['action'] ?? '';
    $no_cicilan      = $_POST['no_cicilan'] ?? ''; // Juga sebagai plat nomor
    $nama_barang     = $_POST['nama_barang'] ?? '';
    $tanggal_cicilan = $_POST['tanggal_cicilan'] ?? '';
    $pokok_cicilan   = str_replace('.', '', $_POST['pokok_cicilan'] ?? '0');
    $bunga_cicilan   = str_replace('.', '', $_POST['bunga_cicilan'] ?? '0');
    $total_cicilan   = str_replace('.', '', $_POST['total_cicilan'] ?? '0');
    $keterangan      = $_POST['keterangan'] ?? '';

    if ($action === 'insert') {
        $stmt = $conn->prepare("
            INSERT INTO cicilan 
            (no_cicilan, nama_barang, tanggal_cicilan, pokok_cicilan, bunga_cicilan, total_cicilan, keterangan) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssss", $no_cicilan, $nama_barang, $tanggal_cicilan, $pokok_cicilan, $bunga_cicilan, $total_cicilan, $keterangan);
        if ($stmt->execute()) {
            $status = 'inserted';
        }
        $stmt->close();
    }

    if ($action === 'update') {
        $stmt = $conn->prepare("
            UPDATE cicilan 
            SET nama_barang = ?, tanggal_cicilan = ?, pokok_cicilan = ?, bunga_cicilan = ?, total_cicilan = ?, keterangan = ? 
            WHERE no_cicilan = ?
        ");
        $stmt->bind_param("sssssss", $nama_barang, $tanggal_cicilan, $pokok_cicilan, $bunga_cicilan, $total_cicilan, $keterangan, $no_cicilan);
        if ($stmt->execute()) {
            $status = 'updated';
        }
        $stmt->close();
    }

    header("Location: ../../pages/Expenses/cicilan.php?status=$status");
    exit();
}

if (isset($_GET['hapus_data'])) {
    $no_cicilan = $_GET['hapus_data'];
    $stmt = $conn->prepare("DELETE FROM cicilan WHERE no_cicilan = ?");
    $stmt->bind_param("s", $no_cicilan);
    if ($stmt->execute()) {
        $status = 'deleted';
    }
    $stmt->close();
    header("Location: ../../pages/Expenses/cicilan.php?status=$status");
    exit();
}

if (isset($_GET['update_row'])) {
    $no_cicilan = $_GET['update_row'];
    $stmt = $conn->prepare("SELECT * FROM cicilan WHERE no_cicilan = ?");
    $stmt->bind_param("s", $no_cicilan);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $data_update = $result->fetch_assoc();
    }
    $stmt->close();
}
