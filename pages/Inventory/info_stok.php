<?php require_once '../../settings.php'; ?>
<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Menu/login.php?expired=1");
    exit();
}


require '../../config/conn.php';
include '../../config/logic/logic_info.php';

/** @var mysqli_result $result */ // <-- Tambah komentar ini
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Stok - Stock</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dark_mode.css">
    <link rel="stylesheet" href="../../assets/css/Menu/info_stok.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body class="flex flex-col min-h-screen">

    <script src="../../assets/js/init_theme.js"></script>

    <div id="navbar-container">
        <?php include __DIR__ . '/../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>
    </div>

    <div class="content-container">
        <h2 class="table-title">Info Stok</h2>
        <h4 class="sub-title">Menu Untuk melihat Info Stok</h4>

        <form method="GET" action="" class="search-form">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" class="search-bar" name="cari"
                    value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>"
                    placeholder="Cari barang...">
                <img src="../../assets/img/Bantani 1.png" alt="Logo Bantani">
            </div>
        </form>

        <section class="info-stok-section">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="stok-card">
                    <div class="card-img-wrapper">
                        <img src="<?= htmlspecialchars($row['foto'] ?? '') ?>"
                            alt="<?= htmlspecialchars($row['nama_barang']) ?>"
                            onerror="this.onerror=null;this.src='../../uploads/default.png';">

                        <span class="category-badge"><?php echo htmlspecialchars($row['kategori']); ?></span>
                    </div>

                    <div class="stok-info">
                        <h3><?php echo htmlspecialchars($row['nama_barang']); ?></h3>

                        <div class="stok-footer">
                            <span class="stok-label">Tersedia:</span>
                            <span class="stok-quantity"><?php echo number_format($row['stok_akhir'] ?? 0); ?>
                                <?php echo htmlspecialchars($row['satuan'] ?? 'Unit'); ?></span>
                        </div>
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
    <script src="../../assets/js/global_theme.js"></script>
    <script src="../../assets/js/Setting/change_theme.js"></script>
</body>

</html>