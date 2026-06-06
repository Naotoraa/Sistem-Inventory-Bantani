<?php
require '../../config/conn.php';

header('Content-Type: application/json');

// 🔍 FUNCTION HITUNG STOK REAL
function getStokSaatIni($conn, $id_barang)
{
    $masuk   = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_masuk WHERE id_barang = '$id_barang'"))['total'] ?? 0);
    $keluar  = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_keluar WHERE id_barang = '$id_barang'"))['total'] ?? 0);
    $migrasi = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_migrasi WHERE id_barang = '$id_barang'"))['total'] ?? 0);
    $eror    = (int) (mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_eror WHERE id_barang = '$id_barang'"))['total'] ?? 0);

    return $masuk - $keluar + $migrasi - $eror;
}

// 🔥 AMBIL DATA SCAN
$barcode = $_POST['barcode'] ?? '';

if (!$barcode) {
    echo json_encode([
        "status" => "error",
        "msg" => "Barcode kosong"
    ]);
    exit;
}

// 🔥 CLEANING BARCODE (hapus prefix BRG-)
$barcode = trim($barcode);
$barcode = str_replace("BRG-", "", $barcode);

// 🔒 AMANKAN INPUT
$barcode = mysqli_real_escape_string($conn, $barcode);

// 🔍 AMBIL DATA BARANG
$q = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang='$barcode'");
$data = mysqli_fetch_assoc($q);

if ($data) {

    $id_barang = $data['id_barang'];
    $nama      = $data['nama_barang'];
    $kategori  = $data['kategori'];

    // 🔥 HITUNG STOK REAL
    $stok = getStokSaatIni($conn, $id_barang);

    // ❌ CEK STOK
    if ($stok <= 0) {
        echo json_encode([
            "status" => "error",
            "msg" => "Stok habis!",
            "nama_barang" => $nama,
            "stok" => $stok
        ]);
        exit;
    }

    // 🔥 INSERT TRANSAKSI
    $insert = mysqli_query($conn, "
        INSERT INTO barang_keluar
        (id_barang,kategori,qty,tanggal_keluar)
        VALUES
        ('$id_barang','$kategori','1',NOW())
    ");

    if ($insert) {
        echo json_encode([
            "status" => "success",
            "id_barang" => $id_barang,
            "nama_barang" => $nama,
            "kategori" => $kategori,
            "tanggal" => date("Y-m-d H:i:s"),
            "stok" => $stok - 1
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "msg" => "Gagal insert ke database"
        ]);
    }
} else {

    echo json_encode([
        "status" => "error",
        "msg" => "Barang tidak ditemukan"
    ]);
}