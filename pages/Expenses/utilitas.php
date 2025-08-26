<?php require_once '../../settings.php'; ?>
<?php
include '../../config/conn.php';
include '../../config/logic/logic_utilitas.php';
require '../../config/clear_cache.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilitas - Expenses</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/Menu/expenses.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">
    <link rel="stylesheet" href="../../assets/css/Opsional/pagination.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
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
        <h2 class="table-title">Pencatatan Biaya Utilitas</h2>
        <h4 class="sub-title">Menu Untuk Manajemen Biaya Utilitas</h4>

        <!-- Search -->
        <form method="get" class="search" action="utilitas.php">
            <div class="search-wrapper">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-bar" name="cari"
                        value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>" placeholder="Cari jenis pembayaran...">
                    <img src="../../assets/img/Bantani 1.png" alt="Logo Bantani">
                </div>

                <div class="month-input-wrapper" onclick="document.getElementById('bulan').showPicker()">
                    <input type="month" id="bulan" name="bulan" value="<?= htmlspecialchars($_GET['bulan'] ?? '') ?>"
                        onchange="this.form.submit()" class="tanggal-bar">
                </div>
            </div>
        </form>

        <!-- Tabel -->
        <table id="Table">
            <thead id="tabel-expenses">
                <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">ID Utilitas</th>
                    <th style="text-align: center;">Pembayaran</th>
                    <th style="text-align: center;">Tanggal</th>
                    <th style="text-align: center;">Biaya</th>
                    <th style="text-align: center;">Keterangan</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-data">
                <?php
                $cari = $_GET['cari'] ?? '';
                $bulan = $_GET['bulan'] ?? '';
                $conditions = [];
                if (!empty($cari)) $conditions[] = "pembayaran LIKE '%$cari%'";
                if (!empty($bulan)) $conditions[] = "DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'";
                $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

                $sql = "SELECT * FROM utilitas $whereClause ORDER BY tanggal DESC";
                $result = $conn->query($sql);
                $no = 1;
                if ($result->num_rows > 0) {
                    foreach ($result as $row) {
                        echo "<tr>
                        <td>$no</td>
                        <td>{$row['id_utilitas']}</td>
                        <td>{$row['pembayaran']}</td>
                        <td>{$row['tanggal']}</td>
                        <td>Rp " . number_format($row['biaya'], 0, ',', '.') . "</td>
                        <td>{$row['keterangan']}</td>
                        <td>
                            <button class='delete-btn' data-link='?hapus_data={$row['id_utilitas']}'><i class='fas fa-trash'></i></button>
                            <button class='edit-btn' data-link='?update_row={$row['id_utilitas']}#form_edit_insert'><i class='fas fa-edit'></i></button>
                        </td>
                    </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada data.</td></tr>";
                }
                ?>
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

        <!-- Form Input / Update -->
        <section class="form-container" id="form_edit_insert">
            <h2 class="table-title" id="table-title-form">
                Form <?= isset($data_update) ? 'Update' : 'Input' ?> Biaya Utilitas
            </h2>
            <form action="utilitas.php" method="POST">
                <input type="hidden" name="action" value="<?= isset($data_update) ? 'update' : 'insert' ?>">
                <input type="hidden" name="id_utilitas" value="<?= $data_update['id_utilitas'] ?? '' ?>">

                <div class="form-group-card">
                    <label for="id_utilitas">ID Utilitas</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-key"></i>
                        <input type="text" name="id_utilitas" id="id_utilitas" required readonly
                            value="<?= isset($data_update) ? $data_update['id_utilitas'] : '' ?>"
                            placeholder="Klik Generate setelah isi tanggal & pembayaran" style="flex: 1;">

                        <button type="button" id="generateIdUtilitasBtn" class="generateBtn"
                            style="padding: 8px 12px; border-radius: 6px; cursor: pointer;"
                            <?= isset($data_update) ? 'disabled' : '' ?>>
                            Generate
                        </button>
                    </div>
                </div>

                <div class="form-group-card">
                    <label for="pembayaran">Untuk Pembayaran</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-file-invoice"></i>
                        <select id="pembayaran" name="pembayaran" style="width:100%" required>
                            <option value="" disabled hidden <?= !isset($data_update) ? 'selected' : '' ?>>-- Pilih --
                            </option>
                            <option value="Token Listrik"
                                <?= (isset($data_update) && $data_update['pembayaran'] == 'Token Listrik') ? 'selected' : '' ?>>
                                Token Listrik</option>
                            <option value="Sewa Tempat"
                                <?= (isset($data_update) && $data_update['pembayaran'] == 'Sewa Tempat') ? 'selected' : '' ?>>
                                Sewa Tempat</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-card" onclick="document.getElementById('tanggal').showPicker?.()"
                    style="cursor: pointer;">
                    <label>Tanggal</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="tanggal" id="tanggal" required
                            value="<?= $data_update['tanggal'] ?? '' ?>" style="flex:1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Biaya (Rp)</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-money-bill-wave"></i>
                        <!-- Input tampilan (ada titik ribuan) -->
                        <input type="text" id="biaya_display" placeholder="Contoh: 500.000"
                            value="<?= isset($data_update) ? number_format($data_update['biaya'], 0, ',', '.') : '' ?>"
                            style="flex:1;">
                        <!-- Input hidden (angka polos untuk ke PHP) -->
                        <input type="hidden" name="biaya" id="biaya"
                            value="<?= isset($data_update) ? $data_update['biaya'] : '' ?>">
                    </div>
                </div>

                <div class="form-group-card">
                    <label for="keterangan">Keterangan</label>
                    <div style="display: flex; align-items: start; gap: 10px;">
                        <i class="fas fa-info-circle" style="margin-top:-27px;"></i>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            placeholder="Masukkan keterangan tambahan jika perlu..."
                            style="flex:1; resize: vertical;"><?= $data_update['keterangan'] ?? '' ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" data-redirect="utilitas.php">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="save-btn">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </section>
    </div>


    <footer class="footer-container py-20 px-4 text-white">
        <div id="footer-placeholder">
            <?php include __DIR__ . '/../../partials/footer.html'; ?>
        </div>
    </footer>

    <script src="../../assets/js/Button/button.js"></script>
    <script src="../../assets/js/Button/button_cancel.js"></script>
    <script src="../../assets/js/Button/button_delete.js"></script>
    <script src="../../assets/js/Button/button_save.js"></script>
    <script src="../../assets/js/Opsional/pagination.js"></script>
    <script src="../../assets/js/Eksport/eksport_utilitas.js"></script>
    <script src="../../assets/js/Generate/generate_IDutilitas.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.getElementById('biaya_display').addEventListener('input', function() {
            let angka = this.value.replace(/[^0-9]/g, ""); // buang selain angka
            let formatted = angka.replace(/\B(?=(\d{3})+(?!\d))/g, "."); // kasih titik ribuan
            this.value = formatted;
            document.getElementById('biaya').value = angka;
        });
    </script>

    <audio id="deleteSound" src="../../assets/sound/delete.mp3" preload="auto"></audio>
    <audio id="saveSound" src="../../assets/sound/save.mp3" preload="auto"></audio>

</body>

</html>