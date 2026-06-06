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

    // Ambil satuan unik
    $satuan = trim($row['satuan']);
    if ($satuan !== '' && !isset($satuanSet[$satuan])) {
        $satuanSet[$satuan] = true;
        $satuanList[] = $satuan;
    }
}

//Search Action
$cari = $_GET['cari'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$minggu = $_GET['minggu'] ?? '';
$conditions = [];

// Filter pencarian
if (!empty($cari)) {
    $cari = $conn->real_escape_string($cari);
    $conditions[] = "(bk.id_barang LIKE '%$cari%' 
                     OR b.nama_barang LIKE '%$cari%' 
                     OR bk.kategori LIKE '%$cari%')";
}

// Filter bulan dan minggu
if (!empty($bulan)) {
    [$tahun, $bulanNum] = explode('-', $bulan);
    $bulanNum = str_pad($bulanNum, 2, '0', STR_PAD_LEFT); // pastikan dua digit

    // Default: seluruh bulan
    $start = "$tahun-$bulanNum-01";
    $end = date("Y-m-t", strtotime($start)); // akhir bulan

    if (!empty($minggu)) {
        switch ((int) $minggu) {
            case 1:
                $start = "$tahun-$bulanNum-01";
                $end = "$tahun-$bulanNum-07";
                break;
            case 2:
                $start = "$tahun-$bulanNum-08";
                $end = "$tahun-$bulanNum-14";
                break;
            case 3:
                $start = "$tahun-$bulanNum-15";
                $end = "$tahun-$bulanNum-21";
                break;
            case 4:
                $start = "$tahun-$bulanNum-22";
                $end = date("Y-m-t", strtotime("$tahun-$bulanNum-01"));
                break;
        }
    }

    $conditions[] = "tanggal_keluar BETWEEN '$start' AND '$end'";
}

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$sql = "
    SELECT 
        bk.id_barang,
        b.nama_barang,
        bk.kategori,
        SUM(bk.qty) as total_qty,
        b.satuan,
        bk.tanggal_keluar
    FROM barang_keluar bk
    JOIN barang b ON bk.id_barang = b.id_barang
    $whereClause
    GROUP BY bk.id_barang, bk.tanggal_keluar
    ORDER BY bk.tanggal_keluar DESC
";
$result = $conn->query($sql);
// ========== SAVE ACTION ==========
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {

    // 🔍 FUNCTION HITUNG STOK (cukup 1x, ga usah diulang)
    function getStokSaatIni($conn, $id_barang)
    {
        $masuk = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_masuk WHERE id_barang = '$id_barang'"))['total'] ?? 0;
        $keluar = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_keluar WHERE id_barang = '$id_barang'"))['total'] ?? 0;
        $migrasi = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_migrasi WHERE id_barang = '$id_barang'"))['total'] ?? 0;
        $eror = (int) mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS total FROM barang_eror WHERE id_barang = '$id_barang'"))['total'] ?? 0;

        return $masuk - $keluar + $migrasi - $eror;
    }

    // ✏️ UPDATE (EDIT DATA)
    if ($_POST['action'] == 'update') {

        $id_barang_old = $conn->real_escape_string($_POST['id_barang_old'] ?? '');
        $tanggal_old = $conn->real_escape_string($_POST['tanggal_old'] ?? '');

        $id_barang = $conn->real_escape_string($_POST['id_barang'] ?? '');
        $kategori = $conn->real_escape_string($_POST['category'] ?? '');
        $qty = (int) ($_POST['qty'] ?? 0);
        $tanggal_keluar = $conn->real_escape_string($_POST['date'] ?? '');

        // Cek darurat biar data aman
        if (empty($id_barang) || empty($tanggal_keluar) || $qty <= 0) {
            echo "<script>alert('Gagal: Data tidak boleh kosong dan QTY harus lebih dari 0!'); window.history.back();</script>";
            exit;
        }

        $stok = getStokSaatIni($conn, $id_barang);

        if ($qty > $stok) {

            header("Location: barang_keluar.php?error=overstock&stok=$stok");
            exit;
        }
        $del = $conn->prepare("
            DELETE FROM barang_keluar 
            WHERE id_barang = ? AND tanggal_keluar = ?
        ");
        $del->bind_param("ss", $id_barang_old, $tanggal_old);
        $del->execute();
        $del->close();

        // ✨ INSERT ULANG (HASIL EDIT)
        $insert = $conn->prepare("
            INSERT INTO barang_keluar (id_barang, kategori, qty, tanggal_keluar)
            VALUES (?, ?, ?, ?)
        ");
        $insert->bind_param("ssis", $id_barang, $kategori, $qty, $tanggal_keluar);

        if ($insert->execute()) {
            echo "<script>window.location.href='../../pages/Inventory/barang_keluar.php?status=updated';</script>";
        } else {
            echo "<script>alert('Gagal Update: " . addslashes($insert->error) . "'); window.history.back();</script>";
        }
        $insert->close();
        exit;
    }

    // ➕ INSERT (TAMBAH DATA)
    elseif ($_POST['action'] == 'insert') {

        $id_barang = $conn->real_escape_string($_POST['id_barang'] ?? '');
        $kategori = $conn->real_escape_string($_POST['category'] ?? '');
        $qty = (int) ($_POST['qty'] ?? 0);
        $tanggal_keluar = $conn->real_escape_string($_POST['date'] ?? '');

        // Cek darurat biar data aman
        if (empty($id_barang) || empty($tanggal_keluar) || $qty <= 0) {
            echo "<script>alert('Gagal: Data tidak boleh kosong dan QTY harus lebih dari 0!'); window.history.back();</script>";
            exit;
        }

        $stok = getStokSaatIni($conn, $id_barang);

        if ($qty > $stok) {
            echo "<script>alert('Gagal: Stok tidak mencukupi! Stok saat ini: $stok'); window.history.back();</script>";
            exit;
        }

        $stmt = $conn->prepare("
            INSERT INTO barang_keluar (id_barang, kategori, qty, tanggal_keluar)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("ssis", $id_barang, $kategori, $qty, $tanggal_keluar);

        if ($stmt->execute()) {
            echo "<script>window.location.href='../../pages/Inventory/barang_keluar.php?status=inserted';</script>";
        } else {
            echo "<script>alert('Gagal simpan: " . addslashes($stmt->error) . "'); window.history.back();</script>";
        }
        $stmt->close();
        exit;
    }
}

// ========== DELETE ACTION (versi grouping) ==========
if (isset($_GET['hapus_data']) && isset($_GET['tgl'])) {

    $id_barang = $conn->real_escape_string($_GET['hapus_data']);
    $tanggal = $conn->real_escape_string($_GET['tgl']);

    $sql = "DELETE FROM barang_keluar WHERE id_barang = ? AND tanggal_keluar = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare gagal: " . $conn->error);
    }

    $stmt->bind_param("ss", $id_barang, $tanggal);

    if ($stmt->execute()) {
        echo "<script>window.location.href='barang_keluar.php?status=deleted';</script>";
    } else {
        echo "<script>window.location.href='barang_keluar.php?status=error';</script>";
    }
    $stmt->close();
    exit();
}

// ========== GET DATA UNTUK UPDATE (versi grouping) ==========
$data_update = null;
if (isset($_GET['update_row']) && isset($_GET['tgl'])) {

    $id_barang = $conn->real_escape_string($_GET['update_row']);
    $tanggal = $conn->real_escape_string($_GET['tgl']);

    // Di-JOIN ke tabel barang biar bisa ngirim data 'nama_barang' dan 'satuan' ke form HTML
    $stmt = $conn->prepare("
        SELECT 
            bk.id_barang,
            b.nama_barang,
            bk.kategori,
            b.satuan,
            SUM(bk.qty) as qty,
            bk.tanggal_keluar
        FROM barang_keluar bk
        JOIN barang b ON bk.id_barang = b.id_barang
        WHERE bk.id_barang = ? AND bk.tanggal_keluar = ?
        GROUP BY bk.id_barang, b.nama_barang, bk.kategori, b.satuan, bk.tanggal_keluar
        LIMIT 1
    ");

    if (!$stmt) {
        die("Prepare gagal: " . $conn->error);
    }

    $stmt->bind_param("ss", $id_barang, $tanggal);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        echo "<script>alert('Data tidak ditemukan'); location.href='barang_keluar.php'</script>";
        exit();
    }

    $data_update = $result->fetch_assoc();
    $stmt->close();
}
