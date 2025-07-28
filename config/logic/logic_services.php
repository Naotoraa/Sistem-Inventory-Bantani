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
    $id = $_POST['id'] ?? '';

    // HANYA validasi id kosong saat update
    if ($action === 'update' && empty($id)) {
        header("Location: ../../pages/Expenses/services.php?status=$status");
        exit();
    }

    $id_service = $_POST['id_service'] ?? '';
    $tanggal_service = $_POST['tanggal_service'] ?? '';
    $nama_barang = $_POST['nama_barang'] ?? '';
    $keterangan = $_POST['keterangan'] ?? ''; // ganti dari $deskripsi
    $biaya_service = intval(str_replace('.', '', $_POST['biaya_service'] ?? '0'));

    // CEK ID SERVICE WAJIB DIISI
    if (empty($id_service)) {
        header("Location: services.php?status=error_idservice_kosong");
        exit();
    }

    if ($action === 'insert') {
        // Validasi ID unik
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
        // Validasi ID exist
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
    $id = $_GET['hapus_data'];
    $stmt = $conn->prepare("DELETE FROM service WHERE id_service = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: ../../pages/Expenses/services.php?status=deleted");
    exit();
}

// ========== HANDLE UPDATE PREVIEW ==========
$data_update = null;
if (isset($_GET['update_row'])) {
    $id = $_GET['update_row'];
    $stmt = $conn->prepare("SELECT * FROM service WHERE id_service = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data_update = $result->fetch_assoc();
    $stmt->close();
}
