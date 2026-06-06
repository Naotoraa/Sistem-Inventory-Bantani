<?php require_once '../../settings.php'; ?>
<?php
// Semua logika dan query database sekarang ada di file ini
include '../../config/logic/logic_dashboard.php';

$log             = $log ?? [];
$total_stok      = $total_stok ?? 0;
$qty_masuk       = $qty_masuk ?? 0;
$qty_keluar      = $qty_keluar ?? 0;
$qty_migrasi     = $qty_migrasi ?? 0;
$qty_eror        = $qty_eror ?? 0;
$exp_operasional = $exp_operasional ?? 0;
$exp_service     = $exp_service ?? 0;
$exp_cicilan     = $exp_cicilan ?? 0;
$exp_utilitas    = $exp_utilitas ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bantani Inventory</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dark_mode.css">
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

    <script src="../../assets/js/init_theme.js"></script>

    <div id="navbar-container">
        <?php include __DIR__ . '../../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '../../../partials/sidebar.php'; ?>
    </div>

    <h2 class="table-title">Dashboard</h2>
    <h4 class="sub-title">Halaman utama di Bantani Inventory</h4>

    <section class="content">
        <div class="card-container mb-2">
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
        <div class="grafik-container container-fluid p-0 mb-4">
            <div class="row g-4 m-0">
                <div class="col-lg-7 pl-0">
                    <div class="grafik-card">
                        <h5 class="judul-grafik">PENGELOLAAN BARANG</h5>
                        <div id="bar-chart"></div>
                    </div>
                </div>

                <div class="col-lg-5 pr-0">
                    <div class="grafik-card" style="display: flex; flex-direction: column;">
                        <h5 class="judul-grafik">PENGELUARAN[EXPENSES]</h5>
                        <div id="donut-chart"
                            style="flex-grow: 1; display: flex; align-items: center; justify-content: center;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="table_aktifitas" style="margin-top: 25px;">
        <h2 class="judul"
            style="padding-top: 0; margin-left: 107px; color: #1e293b; font-weight: 700; font-size: 20px;">Aktivitas
            Terkini</h2>

        <table>
            <thead>
                <tr>
                    <th style="text-align: center; width: 5%;">No</th>
                    <th style="text-align: center; width: 20%;">Tanggal & Waktu</th>
                    <th style="text-align: center; width: 15%;">Kode Ref</th>
                    <th style="text-align: center; width: 20%;">Nama Aktivitas</th>
                    <th style="text-align: left; width: 40%; padding-left: 20px;">Deskripsi / Detail Aktivitas</th>
                </tr>
            </thead>
            <tbody id="tabel-data">
                <?php
                $no = 1;
                foreach ($log as $row) {
                    $aktivitas = $row['aktivitas'];
                    $url_aktivitas = '#';
                    $badge_class = '';
                    $kode_ref = $row['kode_ref'] ?? '-';

                    switch ($aktivitas) {
                        case 'Barang Masuk':
                            $url_aktivitas = '../Inventory/barang_masuk.php';
                            $badge_class = 'badge-masuk';
                            break;
                        case 'Barang Keluar':
                            $url_aktivitas = '../Inventory/barang_keluar.php';
                            $badge_class = 'badge-keluar';
                            break;
                        case 'Barang Migrasi':
                            $url_aktivitas = '../Inventory/barang_migrasi.php';
                            $badge_class = 'badge-migrasi';
                            break;
                        case 'Barang Error':
                            $url_aktivitas = '../Inventory/barang_eror.php';
                            $badge_class = 'badge-error';
                            break;
                        case 'Operasional':
                            $url_aktivitas = '../Expenses/operasional.php';
                            $badge_class = 'badge-expense';
                            break;
                        case 'Service':
                            $url_aktivitas = '../Expenses/services.php';
                            $badge_class = 'badge-expense';
                            break;
                        case 'Cicilan':
                            $url_aktivitas = '../Expenses/cicilan.php';
                            $badge_class = 'badge-expense';
                            break;
                        case 'Utilitas':
                            $url_aktivitas = '../Expenses/utilitas.php';
                            $badge_class = 'badge-expense';
                            break;
                    }

                    echo "<tr>
                            <td style='font-weight: 600;'>$no</td>
                            <td style='color: #64748b; font-size: 13px;'>" . date("d M Y, H:i", strtotime($row['waktu'])) . "</td>
                            <td>{$kode_ref}</td>
                            <td><a href='$url_aktivitas' class='badge-aktivitas {$badge_class}'>{$aktivitas}</a></td>
                            <td style='color: #475569; text-align: left; padding-left: 20px;'>{$row['detail']}</td>
                          </tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
        <div class="pagination" id="pagination-barang" style="margin-left: 107px; margin-bottom: 30px;"></div>
    </section>


    <footer class="footer-container py-20 px-4 text-white">
        <div id="footer-placeholder">
            <?php include __DIR__ . '../../../partials/footer.html'; ?>
        </div>
    </footer>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/cylinder.js"></script>

    <script>
        const dataMasuk = <?= $qty_masuk ?? 0 ?>;
        const dataKeluar = <?= $qty_keluar ?? 0 ?>;
        const dataMigrasi = <?= $qty_migrasi ?? 0 ?>;
        const dataError = <?= $qty_eror ?? 0 ?>;
        const dataTotalStok = <?= $total_stok ?? 0 ?>;

        const expOps = <?= $exp_operasional ?? 0 ?>;
        const expSvc = <?= $exp_service ?? 0 ?>;
        const expCic = <?= $exp_cicilan ?? 0 ?>;
        const expUtl = <?= $exp_utilitas ?? 0 ?>;
    </script>

    <script src="../../assets/js/Menu/grafik_dashboard.js"></script>

    <script src="../../assets/js/global_theme.js"></script>
    <script src="../../assets/js/Menu/dashboard.js"></script>
    <script src="../../assets/js/Opsional/pagination.js"></script>
    <script src="../../assets/js/Opsional/grafik.js"></script>
    <script src="../../assets/js/Setting/change_theme.js"></script>
</body>

</html>