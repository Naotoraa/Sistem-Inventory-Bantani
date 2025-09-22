<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Menu/login.php?expired=1");
    exit();
}
require '../../config/conn.php';


$status = 'error';

// ========== HANDLE INSERT / UPDATE ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action      = $_POST['action'] ?? '';
    $id_cicilan  = $_POST['id_cicilan'] ?? '';

    // Ambil data dari form
    $no_cicilan      = $_POST['no_cicilan'] ?? '';
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
        $stmt->bind_param("sssiiis", $no_cicilan, $nama_barang, $tanggal_cicilan, $pokok_cicilan, $bunga_cicilan, $total_cicilan, $keterangan);

        if ($stmt->execute()) {
            $status = 'inserted';
        }
        $stmt->close();
    } elseif ($action === 'update') {
        // Pastikan id_cicilan ada
        $stmt_check = $conn->prepare("SELECT id_cicilan FROM cicilan WHERE id_cicilan = ?");
        $stmt_check->bind_param("i", $id_cicilan);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $stmt = $conn->prepare("
                UPDATE cicilan 
                SET no_cicilan=?, nama_barang=?, tanggal_cicilan=?, pokok_cicilan=?, bunga_cicilan=?, total_cicilan=?, keterangan=? 
                WHERE id_cicilan=?
            ");
            $stmt->bind_param("sssiiisi", $no_cicilan, $nama_barang, $tanggal_cicilan, $pokok_cicilan, $bunga_cicilan, $total_cicilan, $keterangan, $id_cicilan);

            if ($stmt->execute()) {
                $status = 'updated';
            }
            $stmt->close();
        } else {
            $status = 'id_not_found';
        }
        $stmt_check->close();
    }

    header("Location: ../../pages/Expenses/cicilan.php?status=$status");
    exit();
}

// ========== HANDLE DELETE ==========
if (isset($_GET['hapus_data'])) {
    $id_cicilan = $_GET['hapus_data'];
    $stmt = $conn->prepare("DELETE FROM cicilan WHERE id_cicilan = ?");
    $stmt->bind_param("i", $id_cicilan);
    $stmt->execute();
    $stmt->close();
    header("Location: ../../pages/Expenses/cicilan.php?status=deleted");
    exit();
}

// ========== HANDLE PREVIEW UPDATE ==========
$data_update = null;
if (isset($_GET['update_row'])) {
    $id_cicilan = $_GET['update_row'];
    $stmt = $conn->prepare("SELECT * FROM cicilan WHERE id_cicilan = ?");
    $stmt->bind_param("i", $id_cicilan);
    $stmt->execute();
    $result = $stmt->get_result();
    $data_update = $result->fetch_assoc();
    $stmt->close();
}
