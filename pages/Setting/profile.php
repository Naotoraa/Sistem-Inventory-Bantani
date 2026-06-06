<?php
session_start();
require '../../config/conn.php';

// Pastikan ngambil ID dari session yang benar
$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if (!$user_id) {
    echo "Session tidak valid!";
    exit;
}

if (isset($_POST['update_profile'])) {

    $id   = intval($user_id);
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $telp = trim($_POST['telp']);

    // ==========================================
    // UPDATE: Tabel 'akun' diganti jadi 'users' + Keamanan Prepared Statement
    // ==========================================
    $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, telp=? WHERE id=?");
    $stmt->bind_param("sssi", $nama, $email, $telp, $id);
    $stmt->execute();
    $stmt->close();

    // Langsung update session nama biar navbar langsung berubah tanpa perlu relogin
    $_SESSION['nama'] = $nama;

    /* CEK JIKA ADA FILE FOTO */
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {

        $namaFile = $_FILES['foto']['name'];
        $tmp      = $_FILES['foto']['tmp_name'];

        $ext = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {

            $namaBaru = "profile_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $uploadPath = __DIR__ . "/../uploads/profile/";

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            if (move_uploaded_file($tmp, $uploadPath . $namaBaru)) {
                // ==========================================
                // UPDATE: Tabel 'akun' diganti jadi 'users'
                // ==========================================
                $stmtFoto = $conn->prepare("UPDATE users SET foto=? WHERE id=?");
                $stmtFoto->bind_param("si", $namaBaru, $id);
                $stmtFoto->execute();
                $stmtFoto->close();

                // Langsung update session foto biar foto di navbar langsung ganti
                $_SESSION['foto'] = $namaBaru;
            } else {
                echo "Upload gagal";
                exit;
            }
        }
    }

    header("Location: ../Menu/setting.php?status=updated");
    exit;
}
