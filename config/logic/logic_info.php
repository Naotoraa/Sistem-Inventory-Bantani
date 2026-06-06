<?php

require '../../config/conn.php';
$cari  = $_GET['cari']  ?? '';
$bulan = date('Y-m');

$first_day = $bulan . '-01';
$next_month_first = date('Y-m-d', strtotime($first_day . ' +1 month'));

// Handling Logic Pencarian Barang
$conditions = [];
if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $conditions[] = "(b.id_barang LIKE '%$cari%' 
                      OR b.nama_barang LIKE '%$cari%' 
                      OR b.kategori LIKE '%$cari%')";
}

$whereClause = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : "";

// Query Utama diintegrasikan dengan $whereClause
$query = "
SELECT 
  b.id_barang, b.nama_barang, b.kategori, b.satuan, b.foto,

  (
    (COALESCE(prev_m.masuk_prev,0) - COALESCE(prev_k.keluar_prev,0) + COALESCE(prev_g.mig_prev,0) - COALESCE(prev_e.err_prev,0))
    + COALESCE(cur_m.masuk,0)
    - COALESCE(cur_k.keluar,0)
    + COALESCE(cur_g.mig,0)
    - COALESCE(cur_e.err,0)
  ) AS stok_akhir

FROM barang b

-- AGGREGATE SEBELUM FIRST_DAY
LEFT JOIN (
  SELECT id_barang, SUM(qty) AS masuk_prev FROM barang_masuk WHERE tanggal_masuk < '$first_day' GROUP BY id_barang
) prev_m ON prev_m.id_barang = b.id_barang

LEFT JOIN (
  SELECT id_barang, SUM(qty) AS keluar_prev FROM barang_keluar WHERE tanggal_keluar < '$first_day' GROUP BY id_barang
) prev_k ON prev_k.id_barang = b.id_barang

LEFT JOIN (
  SELECT id_barang, SUM(qty) AS mig_prev FROM barang_migrasi WHERE tanggal < '$first_day' GROUP BY id_barang
) prev_g ON prev_g.id_barang = b.id_barang

LEFT JOIN (
  SELECT id_barang, SUM(qty) AS err_prev FROM barang_eror WHERE tanggal < '$first_day' GROUP BY id_barang
) prev_e ON prev_e.id_barang = b.id_barang

-- AGGREGATE BULAN INI
LEFT JOIN (
  SELECT id_barang, SUM(qty) AS masuk FROM barang_masuk WHERE tanggal_masuk >= '$first_day' AND tanggal_masuk < '$next_month_first' GROUP BY id_barang
) cur_m ON cur_m.id_barang = b.id_barang

LEFT JOIN (
  SELECT id_barang, SUM(qty) AS keluar FROM barang_keluar WHERE tanggal_keluar >= '$first_day' AND tanggal_keluar < '$next_month_first' GROUP BY id_barang
) cur_k ON cur_k.id_barang = b.id_barang

LEFT JOIN (
  SELECT id_barang, SUM(qty) AS mig FROM barang_migrasi WHERE tanggal >= '$first_day' AND tanggal < '$next_month_first' GROUP BY id_barang
) cur_g ON cur_g.id_barang = b.id_barang

LEFT JOIN (
  SELECT id_barang, SUM(qty) AS err FROM barang_eror WHERE tanggal >= '$first_day' AND tanggal < '$next_month_first' GROUP BY id_barang
) cur_e ON cur_e.id_barang = b.id_barang

$whereClause
ORDER BY b.id_barang ASC
";

$result = mysqli_query($conn, $query);