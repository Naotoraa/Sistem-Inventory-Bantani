<?php
require_once '../../settings.php';
require '../../config/logic/logic_cicilan.php';
require '../../config/clear_cache.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cicilan - Expenses</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/Menu/expenses.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">
    <link rel="stylesheet" href="../../assets/css/Opsional/pagination.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="flex flex-col min-h-screen">
    <div id="navbar-container">
        <?php include __DIR__ . '/../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>
    </div>

    <div class="content">
        <h2 class="table-title">Pencatatan Cicilan</h2>
        <h4 class="sub-title">Form & Data Cicilan Kendaraan atau Aset</h4>
        <section>
            <form method="get" class="search" action="cicilan.php">
                <div class="search-wrapper">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" class="search-bar" name="cari"
                            value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>" placeholder="Cari nama barang...">
                        <img src="../../assets/img/Bantani 1.png" alt="Logo Bantani">
                    </div>
                    <div class="month-input-wrapper" onclick="document.getElementById('bulan').showPicker()">
                        <input type="month" id="bulan" name="bulan"
                            value="<?= htmlspecialchars($_GET['bulan'] ?? '') ?>" onchange="this.form.submit()"
                            class="tanggal-bar">
                    </div>
                </div>
            </form>
            <table id="Table">
                <thead id="tabel-expenses">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">No Cicilan</th>
                        <th style="text-align: center;">Nama Barang</th>
                        <th style="text-align: center;">Tanggal</th>
                        <th style="text-align: center;">Pokok</th>
                        <th style="text-align: center;">Bunga</th>
                        <th style="text-align: center;">Total</th>
                        <th style="text-align: center;">Keterangan</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel-data">
                    <?php
                    $cari  = $_GET['cari'] ?? '';
                    $bulan = $_GET['bulan'] ?? '';
                    $conditions = [];

                    if (!empty($cari)) {
                        $conditions[] = "nama_barang LIKE '%" . $conn->real_escape_string($cari) . "%'";
                    }
                    if (!empty($bulan)) {
                        $conditions[] = "DATE_FORMAT(tanggal_cicilan, '%Y-%m') = '" . $conn->real_escape_string($bulan) . "'";
                    }

                    $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
                    $sql = "SELECT * FROM cicilan $whereClause ORDER BY tanggal_cicilan DESC";
                    $result = $conn->query($sql);

                    $no = 1;
                    if ($result && $result->num_rows > 0):
                        foreach ($result as $row): ?>
                            <tr>
                                <!-- Nomor urut (hanya untuk tampilan) -->
                                <td style="text-align: center;"><?= $no++ ?></td>

                                <!-- Plat nomor / No Cicilan -->
                                <td style="text-align: center;"><?= htmlspecialchars($row['no_cicilan']) ?></td>

                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_cicilan']) ?></td>
                                <td>Rp <?= number_format($row['pokok_cicilan'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['bunga_cicilan'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['total_cicilan'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td style="text-align: center;">
                                    <button class="delete-btn" data-link="?hapus_data=<?= $row['id_cicilan'] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <button class="edit-btn" data-link="?update_row=<?= $row['id_cicilan'] ?>#form_edit_insert">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="9" style="text-align: center;">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>


            <div class="table-bottom-container">
                <div class="under_table">
                    <button type="button" class="pdf">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </button>
                </div>
                <div class="pagination" id="pagination-barang"></div>
            </div>
        </section>

        <section class="form-container">
            <h2 class="table-title" id="table-title-form">
                Form <?= isset($data_update) ? 'Update' : 'Input'; ?> Biaya Cicilan
            </h2>
            <?php
            if (!isset($data_update)) {
                $no_cicilan = 'CIC-' . substr(time(), -4) . chr(rand(65, 90));
            }
            ?>
            <form action="cicilan.php" method="POST">
                <input type="hidden" name="action" value="<?= isset($data_update) ? 'update' : 'insert' ?>">
                <input type="hidden" name="id_cicilan" value="<?= $data_update['id_cicilan'] ?? '' ?>">
                <input type="hidden" name="no_cicilan" value="<?= $data_update['no_cicilan'] ?? $no_cicilan ?>">

                <div class="form-group-card">
                    <label>No Cicilan</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-hashtag"></i>
                        <input type="text" name="no_cicilan" required placeholder="Contoh: A 3892 GH"
                            value="<?= $data_update['no_cicilan'] ?? '' ?>" <?= isset($data_update) ? 'readonly' : '' ?>
                            class="<?= isset($data_update) ? 'readonly-input' : '' ?>" style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Nama Barang</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-box-open"></i>
                        <input type="text" name="nama_barang" required placeholder="Contoh: Mobil Carry, Motor Revo"
                            value="<?= $data_update['nama_barang'] ?? '' ?>" style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Tanggal Cicilan</label>
                    <div style="display: flex; align-items: center; gap: 10px; cursor: pointer;"
                        onclick="this.querySelector('input').showPicker()">
                        <i class="fas fa-calendar-alt" style="margin-top: 4px;"></i>
                        <input type="date" name="tanggal_cicilan" required
                            value="<?= $data_update['tanggal_cicilan'] ?? '' ?>" style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Pokok Cicilan (Rp)</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-coins"></i>
                        <input type="text" name="pokok_cicilan" id="pokok" required placeholder="Contoh: 2.220.000"
                            oninput="formatRupiah(this); hitungTotal()"
                            value="<?= isset($data_update) ? number_format($data_update['pokok_cicilan'], 0, ',', '.') : '' ?>"
                            style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Bunga Cicilan (Rp)</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-divide"></i><i class="fas fa-hand-holding-usd"></i>
                        <input type="text" name="bunga_cicilan" id="bunga" required placeholder="Contoh: 22.500"
                            oninput="formatRupiah(this); hitungTotal()"
                            value="<?= isset($data_update) ? number_format($data_update['bunga_cicilan'], 0, ',', '.') : '' ?>"
                            style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Total Cicilan (Rp)</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-calculator"></i>
                        <input type="text" name="total_cicilan" id="total" readonly
                            value="<?= isset($data_update) ? number_format($data_update['total_cicilan'], 0, ',', '.') : '' ?>"
                            style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Keterangan</label>
                    <div style="display: flex; align-items: flex-start; gap: 10px;">
                        <i class="fas fa-info-circle" style="margin-top: -13px;"></i>
                        <textarea name="keterangan" placeholder="Contoh: Cicilan bulan ke-4 atau leasing ABC"
                            style="flex: 1;"><?= $data_update['keterangan'] ?? '' ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" data-redirect="cicilan.php">
                        <i class="fas fa-times"></i> Cancel</button>
                    <button type="submit" class="save-btn"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
        </section>
    </div>

    <footer class="footer-container py-20 px-4 text-white">
        <div id="footer-placeholder">
            <?php include __DIR__ . '/../../partials/footer.html'; ?>
        </div>
    </footer>

    <script>
        function formatRupiah(input) {
            let angka = input.value.replace(/\D/g, '');
            input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function getNumberFromRupiah(rp) {
            return parseInt(rp.replace(/\./g, '')) || 0;
        }

        function hitungTotal() {
            const pokok = getNumberFromRupiah(document.getElementById('pokok').value);
            const bunga = getNumberFromRupiah(document.getElementById('bunga').value);
            const total = pokok + bunga;
            document.getElementById('total').value = total.toLocaleString('id-ID');
        }
    </script>

    <script src="../../assets/js/Button/button.js"></script>
    <script src="../../assets/js/Button/button_cancel.js"></script>
    <script src="../../assets/js/Button/button_delete.js"></script>
    <script src="../../assets/js/Button/button_save.js"></script>
    <script src="../../assets/js/Opsional/pagination.js"></script>
    <script src="../../assets/js/Eksport//eksport_cicilan.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <audio id="deleteSound" src="../../assets/sound/delete.mp3" preload="auto"></audio>
    <audio id="saveSound" src="../../assets/sound/save.mp3" preload="auto"></audio>

</body>

</html>