<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['username'])) {
    header("Location: login.php?expired=1");
    exit();
}
require '../../config/conn.php';

$qty_masuk   = $conn->query("SELECT SUM(qty) AS qty_masuk FROM barang_masuk")->fetch_assoc()['qty_masuk'] ?? 0;
$qty_keluar  = $conn->query("SELECT SUM(qty) AS qty_keluar FROM barang_keluar")->fetch_assoc()['qty_keluar'] ?? 0;
$qty_migrasi = $conn->query("SELECT SUM(qty) AS qty_migrasi FROM barang_migrasi")->fetch_assoc()['qty_migrasi'] ?? 0;
$qty_eror = $conn->query("SELECT SUM(qty) AS qty_eror FROM barang_eror")->fetch_assoc()['qty_eror'] ?? 0;

$total_stok = $qty_masuk  - $qty_keluar + $qty_migrasi - $qty_eror;

$log = [];

$queries = [
    ["SELECT created_at AS waktu, 'Barang Masuk' AS aktivitas, CONCAT(nama_barang, ' (', qty, ' ', satuan, ')') AS detail FROM barang_masuk"],
    ["SELECT created_at AS waktu, 'Barang Keluar' AS aktivitas, CONCAT(nama_barang, ' (', qty, ' ', satuan, ')') AS detail FROM barang_keluar"],
    ["SELECT created_at AS waktu, 'Barang Migrasi' AS aktivitas, CONCAT(nama_barang, ' → ', keterangan, ' (', qty, ' ', satuan, ')') AS detail FROM barang_migrasi"],
    ["SELECT created_at AS waktu, 'Barang Error' AS aktivitas, CONCAT(nama_barang, ' - ', keterangan, ' (', qty, ' ', satuan, ')') AS detail FROM barang_eror"],
    ["SELECT tanggal AS waktu, 'Operasional' AS aktivitas, CONCAT(nama, ' - ', keterangan) AS detail FROM operasional"],
    ["SELECT tanggal_service AS waktu, 'Service' AS aktivitas, CONCAT(nama_barang, ' - ', keterangan) AS detail FROM service"],
    ["SELECT tanggal_cicilan AS waktu, 'Cicilan' AS aktivitas, CONCAT(nama_barang, ' - ', keterangan) AS detail FROM cicilan"],
];

foreach ($queries as [$sql]) {
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) $log[] = $row;
}
