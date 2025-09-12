<?php
require 'conn.php';

if (isset($_POST['id_barang'], $_POST['field'], $_POST['value'])) {
    $id = $_POST['id_barang'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    // keamanan: whitelist field yg boleh diupdate
    $allowed = ['nama_barang', 'satuan'];
    if (!in_array($field, $allowed)) {
        echo "field tidak valid";
        exit;
    }

    $stmt = $conn->prepare("UPDATE barang SET $field = ? WHERE id_barang = ?");
    $stmt->bind_param("ss", $value, $id);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "error";
    }
}