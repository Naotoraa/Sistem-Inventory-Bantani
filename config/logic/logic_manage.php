<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Menu/login.php?expired=1");
    exit();
}

require '../../config/conn.php';

$cari = $_GET['cari'] ?? '';
$bulan = $_GET['bulan'] ?? date('Y-m');
$conditions = [];

$bulan_sebelumnya = '';
if (!empty($bulan)) {
    $date = DateTime::createFromFormat('Y-m', $bulan);
    $date->modify('-1 month');
    $bulan_sebelumnya = $date->format('Y-m');
}

if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $conditions[] = "(b.id_barang LIKE '%$cari%' OR b.nama_barang LIKE '%$cari%' OR b.kategori LIKE '%$cari%')";
}

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$query = "
SELECT 
    b.id_barang, 
    b.nama_barang, 
    b.kategori, 
    b.satuan,

    -- Stok awal dari bulan sebelumnya
    COALESCE((
        SELECT 
            COALESCE(SUM(bm_prev.qty), 0)
            - COALESCE((SELECT SUM(qty) FROM barang_keluar WHERE id_barang = b.id_barang AND DATE_FORMAT(tanggal_keluar, '%Y-%m') = '$bulan_sebelumnya'), 0)
            + COALESCE((SELECT SUM(qty) FROM barang_migrasi WHERE id_barang = b.id_barang AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_sebelumnya'), 0)
            - COALESCE((SELECT SUM(qty) FROM barang_eror WHERE id_barang = b.id_barang AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_sebelumnya'), 0)
        FROM barang_masuk bm_prev
        WHERE bm_prev.id_barang = b.id_barang AND DATE_FORMAT(bm_prev.tanggal_masuk, '%Y-%m') = '$bulan_sebelumnya'
    ), 0) AS stok_awal,

    -- Data bulan ini
    COALESCE(bm.total_masuk, 0) AS barang_masuk,
    COALESCE(bk.total_keluar, 0) AS barang_keluar,
    COALESCE(bg.total_migrasi, 0) AS barang_migrasi,
    COALESCE(be.total_eror, 0) AS barang_eror,

    -- Stok akhir
    (
        COALESCE((
            SELECT 
                COALESCE(SUM(bm_prev.qty), 0)
                - COALESCE((SELECT SUM(qty) FROM barang_keluar WHERE id_barang = b.id_barang AND DATE_FORMAT(tanggal_keluar, '%Y-%m') = '$bulan_sebelumnya'), 0)
                + COALESCE((SELECT SUM(qty) FROM barang_migrasi WHERE id_barang = b.id_barang AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_sebelumnya'), 0)
                - COALESCE((SELECT SUM(qty) FROM barang_eror WHERE id_barang = b.id_barang AND DATE_FORMAT(tanggal, '%Y-%m') = '$bulan_sebelumnya'), 0)
            FROM barang_masuk bm_prev
            WHERE bm_prev.id_barang = b.id_barang AND DATE_FORMAT(bm_prev.tanggal_masuk, '%Y-%m') = '$bulan_sebelumnya'
        ), 0)
        + COALESCE(bm.total_masuk, 0)
        - COALESCE(bk.total_keluar, 0)
        + COALESCE(bg.total_migrasi, 0)
        - COALESCE(be.total_eror, 0)
    ) AS stok_akhir

FROM barang b
LEFT JOIN (
    SELECT id_barang, SUM(qty) AS total_masuk 
    FROM barang_masuk 
    WHERE DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$bulan'
    GROUP BY id_barang
) bm ON b.id_barang = bm.id_barang

LEFT JOIN (
    SELECT id_barang, SUM(qty) AS total_keluar 
    FROM barang_keluar 
    WHERE DATE_FORMAT(tanggal_keluar, '%Y-%m') = '$bulan'
    GROUP BY id_barang
) bk ON b.id_barang = bk.id_barang

LEFT JOIN (
    SELECT id_barang, SUM(qty) AS total_migrasi 
    FROM barang_migrasi 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'
    GROUP BY id_barang
) bg ON b.id_barang = bg.id_barang

LEFT JOIN (
    SELECT id_barang, SUM(qty) AS total_eror 
    FROM barang_eror 
    WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'
    GROUP BY id_barang
) be ON b.id_barang = be.id_barang
$whereClause
ORDER BY b.id_barang ASC
";

$result = mysqli_query($conn, $query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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