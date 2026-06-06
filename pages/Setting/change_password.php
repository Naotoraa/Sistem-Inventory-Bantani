<?php
session_start();
require '../../config/conn.php';

$user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if (!$user_id) {
    header("Location: ../Menu/login.php");
    exit;
}

function alert($type, $title, $text, $redirect = null)
{
    echo "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Change Password</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
    <script>
        Swal.fire({
            icon: '$type',
            title: '$title',
            text: '$text',
            confirmButtonColor: '#689f38' // Aku sesuaikan pakai hijau Bantani biar senada
        }).then(() => {
            " . ($redirect ? "window.location='$redirect';" : "window.history.back();") . "
        });
    </script>
    </body>
    </html>
    ";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        alert("warning", "Oops", "Semua field wajib diisi");
    }

    if ($newPassword !== $confirmPassword) {
        alert("error", "Gagal", "Konfirmasi password tidak sama");
    }
    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        alert("error", "Error", "User tidak ditemukan");
    }

    $hashedPassword = $data['password'];

    if (!password_verify($oldPassword, $hashedPassword)) {
        alert("error", "Gagal", "Password lama salah");
    }

    if (password_verify($newPassword, $hashedPassword)) {
        alert("warning", "Oops", "Password baru tidak boleh sama dengan password lama");
    }

    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->bind_param("si", $newHashedPassword, $user_id);

    if ($stmt->execute()) {

        session_unset();
        session_destroy();

        alert(
            "success",
            "Berhasil!",
            "Password berhasil diubah. Silakan login kembali.",
            "../Menu/login.php"
        );
    } else {
        alert("error", "Error", "Gagal update password");
    }
}
