<?php
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: ../Menu/login.php?expired=1");
  exit();
}

require '../../config/conn.php';
$cari  = $_GET['cari']  ?? '';
$bulan = $_GET['bulan'] ?? date('Y-m');

$first_day = $bulan . '-01';
$next_month_first = date('Y-m-d', strtotime($first_day . ' +1 month'));

$conditions = [];
if (!empty($cari)) {
  $cari = $conn->real_escape_string($cari);
  $conditions[] = "(b.id_barang LIKE '%$cari%' 
                      OR b.nama_barang LIKE '%$cari%' 
                      OR b.kategori LIKE '%$cari%')";
}

$whereClause = !empty($conditions) ? "WHERE " . implode(' AND ', $conditions) : "";

// ====================================================================
// QUERY ASLI PUNYAMU 100% UTUH
// ====================================================================
$query = "
SELECT 
  b.id_barang, b.nama_barang, b.kategori, b.satuan,

  -- stok awal = (masuk_prev - keluar_prev + mig_prev - eror_prev)
  (COALESCE(prev_m.masuk_prev,0) - COALESCE(prev_k.keluar_prev,0) + COALESCE(prev_g.mig_prev,0) - COALESCE(prev_e.err_prev,0)) AS stok_awal,

  COALESCE(cur_m.masuk,0) AS barang_masuk,
  COALESCE(cur_k.keluar,0) AS barang_keluar,
  COALESCE(cur_g.mig,0) AS barang_migrasi,
  COALESCE(cur_e.err,0) AS barang_eror,

  (
    (COALESCE(prev_m.masuk_prev,0) - COALESCE(prev_k.keluar_prev,0) + COALESCE(prev_g.mig_prev,0) - COALESCE(prev_e.err_prev,0))
    + COALESCE(cur_m.masuk,0)
    - COALESCE(cur_k.keluar,0)
    + COALESCE(cur_g.mig,0)
    - COALESCE(cur_e.err,0)
  ) AS stok_akhir

FROM barang b

-- AGGREGATE SEBELUM FIRST_DAY (stok sampai akhir bulan sebelumnya)
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // ====================================================================
  // 1. TANGKAP REQUEST EDIT DARI SWEETALERT (Logic Baru)
  // ====================================================================
  if (isset($_POST['action_update']) && $_POST['action_update'] == '1') {
    $id = trim($_POST['update_id']);
    $field = trim($_POST['update_field']);
    $value = trim($_POST['update_value']);

    // Validasi biar aman, cuma field nama dan satuan yang bisa diubah
    if (in_array($field, ['nama_barang', 'satuan']) && !empty($id) && !empty($value)) {
      $stmt = $conn->prepare("UPDATE barang SET $field = ? WHERE id_barang = ?");
      $stmt->bind_param("ss", $value, $id);
      if ($stmt->execute()) {
        header("Location: manage_stok.php?status=updated");
      } else {
        header("Location: manage_stok.php?status=error_update");
      }
      $stmt->close();
      exit;
    }
  }

  // ====================================================================
  // 2. TANGKAP REQUEST HAPUS DARI ZONA BAHAYA (Logic Baru)
  // ====================================================================
  elseif (isset($_POST['action_delete']) && $_POST['action_delete'] == '1') {
    $id_barang_hapus = trim($_POST['id_barang_hapus']);

    if (!empty($id_barang_hapus)) {
      $conn->begin_transaction();
      try {
        $del_m = $conn->prepare("DELETE FROM barang_masuk WHERE id_barang = ?");
        $del_m->bind_param("s", $id_barang_hapus);
        $del_m->execute();

        $del_k = $conn->prepare("DELETE FROM barang_keluar WHERE id_barang = ?");
        $del_k->bind_param("s", $id_barang_hapus);
        $del_k->execute();

        $del_mig = $conn->prepare("DELETE FROM barang_migrasi WHERE id_barang = ?");
        $del_mig->bind_param("s", $id_barang_hapus);
        $del_mig->execute();

        $del_e = $conn->prepare("DELETE FROM barang_eror WHERE id_barang = ?");
        $del_e->bind_param("s", $id_barang_hapus);
        $del_e->execute();

        $del_b = $conn->prepare("DELETE FROM barang WHERE id_barang = ?");
        $del_b->bind_param("s", $id_barang_hapus);
        $del_b->execute();

        $conn->commit();
        header("Location: manage_stok.php?status=deleted");
        exit;
      } catch (Exception $e) {
        $conn->rollback();
        header("Location: manage_stok.php?status=error_delete");
        exit;
      }
    }
  }

  // ====================================================================
  // 3. LOGIC TAMBAH BARANG (ASLI PUNYAMU 100% UTUH TANPA DIUBAH)
  // ====================================================================
  elseif (isset($_POST['id_barang'])) {
    $id_barang = trim($_POST['id_barang']);
    $nama_barang = trim($_POST['nama_barang']);
    $kategori = trim($_POST['kategori']);
    $satuan = trim($_POST['satuan']);
    $foto = '';

    if (empty($id_barang) || empty($nama_barang) || empty($kategori) || empty($satuan) || !isset($_FILES['foto'])) {
      header("Location: manage_stok.php?status=field_required");
      exit;
    }

    if ($_FILES['foto']['error'] === 0) {
      $filename = time() . "_" . basename($_FILES["foto"]["name"]);
      $target_dir = "uploads/";
      $target_file = $target_dir . $filename;

      // Buat folder jika belum ada
      if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
      }

      if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        $foto = $target_file;
      } else {
        header("Location: manage_stok.php?status=error_upload");
        exit;
      }
    } else {
      header("Location: manage_stok.php?status=error_upload");
      exit;
    }

    $stmt = $conn->prepare("INSERT INTO barang (id_barang, nama_barang, kategori, satuan, foto) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $id_barang, $nama_barang, $kategori, $satuan, $foto);

    if ($stmt->execute()) {
      header("Location: manage_stok.php?status=inserted");
    } else {
      header("Location: manage_stok.php?status=error");
    }

    $stmt->close();
    $conn->close();
    exit;
  }
}