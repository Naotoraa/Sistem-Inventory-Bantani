<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Menu/login.php?expired=1");
    exit();
}

require '../../config/conn.php';

$success = false;
$error = "";

if (isset($_POST['change_password'])) {

    $username = $_SESSION['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $query = mysqli_query($conn, "SELECT id, password FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    if (!$data || !password_verify($old_password, $data['password'])) {
        $error = "Password lama salah!";
    } elseif ($new_password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } elseif (strlen($new_password) < 8) {
        $error = "Password minimal 8 karakter!";
    } else {

        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);

        mysqli_query($conn, "UPDATE users SET password='$new_hash' WHERE username='$username'");

        $success = true;
    }
}
