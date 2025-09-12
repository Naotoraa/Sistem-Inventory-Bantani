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
    ["SELECT bm.created_at AS waktu, 'Barang Masuk' AS aktivitas, 
        CONCAT(b.nama_barang, ' (', bm.qty, ' ', b.satuan, ')') AS detail 
      FROM barang_masuk bm
      JOIN barang b ON bm.id_barang = b.id_barang"],

    ["SELECT bk.created_at AS waktu, 'Barang Keluar' AS aktivitas, 
        CONCAT(b.nama_barang, ' (', bk.qty, ' ', b.satuan, ')') AS detail 
      FROM barang_keluar bk
      JOIN barang b ON bk.id_barang = b.id_barang"],

    ["SELECT mg.created_at AS waktu, 'Barang Migrasi' AS aktivitas, 
        CONCAT(b.nama_barang, ' → ', mg.keterangan, ' (', mg.qty, ' ', b.satuan, ')') AS detail 
      FROM barang_migrasi mg
      JOIN barang b ON mg.id_barang = b.id_barang"],

    ["SELECT er.created_at AS waktu, 'Barang Error' AS aktivitas, 
        CONCAT(b.nama_barang, ' - ', er.keterangan, ' (', er.qty, ' ', b.satuan, ')') AS detail 
      FROM barang_eror er
      JOIN barang b ON er.id_barang = b.id_barang"],

    ["SELECT tanggal AS waktu, 'Operasional' AS aktivitas, CONCAT(nama, ' - ', keterangan) AS detail 
      FROM operasional"],

    ["SELECT tanggal_service AS waktu, 'Service' AS aktivitas, CONCAT(nama_barang, ' - ', keterangan) AS detail 
      FROM service"],

    ["SELECT tanggal_cicilan AS waktu, 'Cicilan' AS aktivitas, CONCAT(nama_barang, ' - ', keterangan) AS detail 
      FROM cicilan"],
];

// gabung semua hasil query
foreach ($queries as [$sql]) {
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        $log[] = $row;
    }
}

// urutkan by waktu terbaru
usort($log, function ($a, $b) {
    return strtotime($b['waktu']) <=> strtotime($a['waktu']);
});