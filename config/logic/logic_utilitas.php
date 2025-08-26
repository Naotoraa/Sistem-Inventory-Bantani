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
    $id_utilitas = $_POST['id_utilitas'] ?? '';

    // Ambil data dari form
    $pembayaran = $_POST['pembayaran'] ?? '';
    $tanggal    = $_POST['tanggal'] ?? '';
    $biaya      = str_replace('.', '', $_POST['biaya'] ?? '0'); // hilangkan titik pemisah ribuan
    $keterangan = $_POST['keterangan'] ?? '';

    if ($action === 'insert') {
        // Cek apakah id_utilitas sudah ada
        $stmt_check = $conn->prepare("SELECT id_utilitas FROM utilitas WHERE id_utilitas = ?");
        $stmt_check->bind_param("s", $id_utilitas);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows === 0) {
            $stmt = $conn->prepare("INSERT INTO utilitas (id_utilitas, pembayaran, tanggal, biaya, keterangan) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $id_utilitas, $pembayaran, $tanggal, $biaya, $keterangan);

            if ($stmt->execute()) {
                $status = 'inserted';
            }
            $stmt->close();
        } else {
            $status = 'duplicate_id';
        }
        $stmt_check->close();
    } elseif ($action === 'update') {
        // Cek apakah id_utilitas ada di DB
        $stmt_check = $conn->prepare("SELECT id_utilitas FROM utilitas WHERE id_utilitas = ?");
        $stmt_check->bind_param("s", $id_utilitas);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE utilitas SET pembayaran=?, tanggal=?, biaya=?, keterangan=? WHERE id_utilitas=?");
            $stmt->bind_param("ssiss", $pembayaran, $tanggal, $biaya, $keterangan, $id_utilitas);

            if ($stmt->execute()) {
                $status = 'updated';
            }
            $stmt->close();
        } else {
            $status = 'id_not_found';
        }
        $stmt_check->close();
    }

    header("Location: ../../pages/Expenses/utilitas.php?status=$status");
    exit();
}

// ========== HANDLE DELETE ==========
if (isset($_GET['hapus_data'])) {
    $id_utilitas = $_GET['hapus_data'];
    $stmt = $conn->prepare("DELETE FROM utilitas WHERE id_utilitas = ?");
    $stmt->bind_param("s", $id_utilitas);
    $stmt->execute();
    $stmt->close();
    header("Location: ../../pages/Expenses/utilitas.php?status=deleted");
    exit();
}

// ========== HANDLE PREVIEW UPDATE ==========
$data_update = null;
if (isset($_GET['update_row'])) {
    $id_utilitas = $_GET['update_row'];
    $stmt = $conn->prepare("SELECT * FROM utilitas WHERE id_utilitas = ?");
    $stmt->bind_param("s", $id_utilitas);
    $stmt->execute();
    $result = $stmt->get_result();
    $data_update = $result->fetch_assoc();
    $stmt->close();
}
