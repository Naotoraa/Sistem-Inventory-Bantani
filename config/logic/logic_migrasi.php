<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Menu/login.php?expired=1");
    exit();
}

require '../../config/conn.php';

$barangList = [];
$satuanSet = [];
$satuanList = [];

$result = $conn->query("SELECT * FROM barang");
while ($row = $result->fetch_assoc()) {
    $barangList[] = $row;

    $satuan = trim($row['satuan']);
    if ($satuan !== '' && !isset($satuanSet[$satuan])) {
        $satuanSet[$satuan] = true;
        $satuanList[] = $satuan;
    }
}

// ========== FILTER / SEARCH ==========
$cari = $_GET['cari'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$conditions = [];
if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $conditions[] = "(mg.id_barang LIKE '%$cari%' 
                     OR b.nama_barang LIKE '%$cari%' 
                     OR b.kategori LIKE '%$cari%')";
}

if (!empty($bulan)) {
    $conditions[] = "DATE_FORMAT(mg.tanggal, '%Y-%m') = '$bulan'";
}

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "
    SELECT mg.id, mg.id_barang, b.nama_barang, mg.kategori, mg.qty, b.satuan, mg.keterangan, mg.tanggal
    FROM barang_migrasi mg
    JOIN barang b ON mg.id_barang = b.id_barang
    $whereClause
    ORDER BY mg.id DESC
";
$result = $conn->query($sql);


// ========== SAVE ACTION ==========
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    include '../../config/conn.php';

    $id_barang  = $conn->real_escape_string($_POST['id_barang'] ?? '');
    $kategori   = $conn->real_escape_string($_POST['category'] ?? '');
    $qty        = (int) ($_POST['qty'] ?? 0);
    $tanggal    = $conn->real_escape_string($_POST['date'] ?? '');
    $keterangan = $conn->real_escape_string($_POST['keterangan'] ?? '');

    if ($_POST['action'] == 'update') {
        $id = $conn->real_escape_string($_POST['id'] ?? '');

        // UPDATE
        $sql = "UPDATE barang_migrasi 
                SET id_barang = ?, kategori = ?, qty = ?, tanggal = ?, keterangan = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssissi", $id_barang, $kategori, $qty, $tanggal, $keterangan, $id);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_migrasi.php?status=updated';</script>";
            } else {
                echo "<script>alert('Gagal Update: " . addslashes($stmt->error) . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_migrasi.php?status=error';</script>";
        }
    } elseif ($_POST['action'] == 'insert') {
        // INSERT
        $sql = "INSERT INTO barang_migrasi (id_barang, kategori, qty, tanggal, keterangan) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssiss", $id_barang, $kategori, $qty, $tanggal, $keterangan);
            if ($stmt->execute()) {
                echo "<script>window.location.href='../../pages/Inventory/barang_migrasi.php?status=inserted';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan data: " . addslashes($stmt->error) . "'); window.history.back();</script>";
            }
            $stmt->close();
        } else {
            echo "<script>window.location.href='../../pages/Inventory/barang_migrasi.php?status=error';</script>";
        }
    }

    $conn->close();
    exit;
}

// ========== DELETE ==========
if (isset($_GET['hapus_data'])) {
    $id = $_GET['hapus_data'];

    $sql = "DELETE FROM barang_migrasi WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>window.location.href='barang_migrasi.php?status=deleted';</script>";
    } else {
        echo "<script>window.location.href='barang_migrasi.php?status=error';</script>";
    }

    $stmt->close();
    $conn->close();
    exit;
}

// ========== LOAD DATA UNTUK UPDATE ==========
if (isset($_GET['update_row'])) {
    $id = $_GET['update_row'];

    $data_update = $conn->query("
    SELECT bm.id, bm.id_barang, b.nama_barang, bm.kategori, bm.qty, b.satuan, bm.tanggal, bm.keterangan
    FROM barang_migrasi bm
    JOIN barang b ON bm.id_barang = b.id_barang
    WHERE bm.id = '$id'
    LIMIT 1
");

    if ($data_update->num_rows < 1) {
        echo "<script>alert('Data sudah dihapus atau tidak ada'); location.href='../../pages/Inventory/barang_migrasi.php'</script>";
        exit();
    }

    $data_update = $data_update->fetch_assoc();
}
