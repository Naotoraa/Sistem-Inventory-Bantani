<?php require_once '../../settings.php'; ?>
<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Menu/login.php?expired=1");
    exit();
}

require '../../config/conn.php';

$query = "SELECT nama_barang, kategori, foto FROM barang";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Stok - Stock</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/Menu/info_stok.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="flex flex-col min-h-screen">

    <div id="navbar-container">
        <?php include __DIR__ . '/../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>
    </div>

    <div class="content-container">
        <h2 class="table-title">Info Stok</h2>
        <h4 class="sub-title">Menu Untuk melihat Info Stok</h4>
        <section class="info-stok-section">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="stok-card">
                <img src="<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama_barang']) ?>"
                    onerror="this.onerror=null;this.src='<?= $link ?>/uploads/default.png';">

                <div class="stok-info">
                    <h3><?php echo htmlspecialchars($row['nama_barang']); ?></h3>
                    <p><strong>Kategori:</strong> <?php echo htmlspecialchars($row['kategori']); ?></p>
                </div>
            </div>
            <?php endwhile; ?>
        </section>
    </div>

    <footer class="footer-container py-20 px-4 text-white">
        <div id="footer-placeholder">
            <?php include __DIR__ . '/../../partials/footer.html'; ?>
        </div>
    </footer>

    <script src="../../assets/js/Menu/info_stok.js"></script>
</body>

</html>