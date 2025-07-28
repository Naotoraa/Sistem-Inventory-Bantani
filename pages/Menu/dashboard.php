<?php require_once '../../settings.php'; ?>
<?php
include '../../config/logic/logic_dashboard.php';

$masuk  = $conn->query("SELECT SUM(qty) AS total FROM barang_masuk")->fetch_assoc()['total'] ?? 0;
$keluar = $conn->query("SELECT SUM(qty) AS total FROM barang_keluar")->fetch_assoc()['total'] ?? 0;
$migrasi = $conn->query("SELECT SUM(qty) AS total FROM barang_migrasi")->fetch_assoc()['total'] ?? 0;
$eror   = $conn->query("SELECT SUM(qty) AS total FROM barang_eror")->fetch_assoc()['total'] ?? 0;

$total_stok = $masuk - $keluar + $migrasi - $eror;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantani Inventory</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/Menu/dashboard.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">
    <link rel="stylesheet" href="../../assets/css/Opsional/diagram.css">
    <link rel="stylesheet" href="../../assets/css/Opsional/pagination.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Playfair+Display:wght@700&family=Lobster&display=swap"
        rel="stylesheet">
</head>

<body class="flex flex-col min-h-screen">
    <div id="navbar-container">
        <?php include __DIR__ . '../../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '../../../partials/sidebar.php'; ?>
    </div>

    <h2 class="table-title">Dashboard</h2>
    <h4 class="sub-title">Halaman utama di Bantani Inventory</h4>

    <section class="content">
        <div class="card-container">
            <a href="../../pages/Inventory/manage_stok.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="orange"><?= number_format($total_stok) ?></h3>
                        <p>Total Stok</p>
                    </div>
                    <i class="fas fa-box icon orange"></i>
                    <div class="card-footer orange-bg">Total</div>
                </div>
            </a>

            <a href="../../pages/Inventory/barang_masuk.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="green"><?php echo $qty_masuk; ?></h3>
                        <p>Barang Masuk</p>
                    </div>
                    <i class="fas fa-arrow-down icon green"></i>
                    <div class="card-footer green-bg">Jumlah</div>
                </div>
            </a>

            <a href="../../pages/Inventory/barang_keluar.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="red"><?php echo $qty_keluar; ?></h3>
                        <p>Barang Keluar</p>
                    </div>
                    <i class="fas fa-arrow-up icon red"></i>
                    <div class="card-footer red-bg">Jumlah</div>
                </div>
            </a>

            <a href="../../pages/Inventory/barang_migrasi.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="blue"><?php echo $qty_migrasi; ?></h3>
                        <p>Barang Migrasi</p>
                    </div>
                    <i class="fas fa-exchange-alt icon blue"></i>
                    <div class="card-footer blue-bg">Jumlah</div>
                </div>
            </a>

            <a href="../../pages/Inventory/barang_eror.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <div class="card-header">
                        <h3 class="purple"><?php echo $qty_eror; ?></h3>
                        <p>Barang Eror</p>
                    </div>
                    <i class="fas fa-tools icon purple"></i>
                    <div class="card-footer purple-bg">Jumlah</div>
                </div>
            </a>
        </div>
    </section>

    <section class="grafik">
        <h2 class="judul" id="judul_statistik">Statistik Grafik</h2>
        <div class="infographic-square">
            <div class="quadrant top-left">
                <div>
                    <div class="label">Barang Masuk</div>
                    <span><?= number_format($qty_masuk) ?></span>
                </div>
            </div>
            <div class="quadrant top-right">
                <div>
                    <div class="label">Barang Keluar</div>
                    <span><?= number_format($qty_keluar) ?></span>
                </div>
            </div>
            <div class="quadrant bottom-right">
                <div>
                    <div class="label">Barang Migrasi</div>
                    <span><?= number_format($qty_migrasi) ?></span>
                </div>
            </div>
            <div class="quadrant bottom-left">
                <div>
                    <div class="label">Barang Error</div>
                    <span><?= number_format($qty_eror) ?></span>
                </div>
            </div>

            <div class="center-circle">
                <h3>Stok Akhir</h3>
                <p><?= number_format($total_stok) ?></p>
            </div>
        </div>


    </section>

    <section class="table_aktifitas">
        <h2 class="judul">Aktivitas Terkini</h2>
        <table>
            <thead>
                <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">Tanggal & Waktu</th>
                    <th style="text-align: center;">Nama Aktivitas</th>
                    <th style="text-align: center;">Deskripsi / Detail</th>
                </tr>
            </thead>
            <tbody id="tabel-data">
                <?php
                $no = 1;
                foreach ($log as $row) {
                    $aktivitas = $row['aktivitas'];
                    $url_aktivitas = '#'; // default link

                    switch ($aktivitas) {
                        case 'Barang Masuk':
                            $url_aktivitas = '../Inventory/barang_masuk.php';
                            break;
                        case 'Barang Keluar':
                            $url_aktivitas = '../Inventory/barang_keluar.php';
                            break;
                        case 'Barang Migrasi':
                            $url_aktivitas = '../Inventory/barang_migrasi.php';
                            break;
                        case 'Barang Error':
                            $url_aktivitas = '../Inventory/barang_eror.php';
                            break;
                        case 'Operasional':
                            $url_aktivitas = '../Expenses/operasional.php';
                            break;
                        case 'Service':
                            $url_aktivitas = '../Expenses/services.php';
                            break;
                        case 'Cicilan':
                            $url_aktivitas = '../Expenses/cicilan.php';
                            break;
                    }

                    echo "<tr>
        <td style='text-align:center;'>$no</td>
        <td>" . date("Y-m-d H:i", strtotime($row['waktu'])) . "</td>
        <td><a href='$url_aktivitas' class='aktivitas-link'>{$aktivitas}</a></td>
        <td>{$row['detail']}</td>
    </tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
        <div class="pagination" id="pagination-barang"></div>
    </section>

    <footer class="footer-container py-20 px-4 text-white">
        <div id="footer-placeholder">
            <?php include __DIR__ . '../../../partials/footer.html'; ?>
        </div>
    </footer>

    <script src="../../assets/js/Menu/dashboard.js"></script>
    <script src="../../assets/js/Opsional/pagination.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>

</html>