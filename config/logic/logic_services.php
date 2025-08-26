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
    $action = $_POST['action'] ?? '';
    $id_service = $_POST['id_service'] ?? '';

    // Validasi khusus untuk update (id_service wajib ada)
    if ($action === 'update' && empty($id_service)) {
        header("Location: ../../pages/Expenses/services.php?status=$status");
        exit();
    }

    // Ambil data inputan
    $tanggal_service = $_POST['tanggal_service'] ?? '';
    $nama_barang     = $_POST['nama_barang'] ?? '';
    $keterangan      = $_POST['keterangan'] ?? '';
    $biaya_service   = intval(str_replace('.', '', $_POST['biaya_service'] ?? '0'));

    // Validasi ID service wajib
    if (empty($id_service)) {
        header("Location: ../../pages/Expenses/services.php?status=error_idservice_kosong");
        exit();
    }

    if ($action === 'insert') {
        // Cek apakah id_service sudah ada
        $stmt_check = $conn->prepare("SELECT id_service FROM service WHERE id_service = ?");
        $stmt_check->bind_param("s", $id_service);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO service (id_service, tanggal_service, nama_barang, keterangan, biaya_service) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $id_service, $tanggal_service, $nama_barang, $keterangan, $biaya_service);
            if ($stmt->execute()) {
                $status = 'inserted';
            }
            $stmt->close();
        }
        $stmt_check->close();
    } elseif ($action === 'update') {
        // Cek apakah id_service ada di DB
        $stmt_check = $conn->prepare("SELECT id_service FROM service WHERE id_service = ?");
        $stmt_check->bind_param("s", $id_service);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE service SET tanggal_service=?, nama_barang=?, keterangan=?, biaya_service=? WHERE id_service=?");
            $stmt->bind_param("sssds", $tanggal_service, $nama_barang, $keterangan, $biaya_service, $id_service);
            if ($stmt->execute()) {
                $status = 'updated';
            }
            $stmt->close();
        }
        $stmt_check->close();
    }

    header("Location: ../../pages/Expenses/services.php?status=$status");
    exit();
}

// ========== HANDLE DELETE ==========
if (isset($_GET['hapus_data'])) {
    $id_service = $_GET['hapus_data'];
    $stmt = $conn->prepare("DELETE FROM service WHERE id_service = ?");
    $stmt->bind_param("s", $id_service);
    $stmt->execute();
    $stmt->close();
    header("Location: ../../pages/Expenses/services.php?status=deleted");
    exit();
}

// ========== HANDLE PREVIEW UPDATE ==========
$data_update = null;
if (isset($_GET['update_row'])) {
    $id_service = $_GET['update_row'];
    $stmt = $conn->prepare("SELECT * FROM service WHERE id_service = ?");
    $stmt->bind_param("s", $id_service);
    $stmt->execute();
    $result = $stmt->get_result();
    $data_update = $result->fetch_assoc();
    $stmt->close();
}