<?php require_once '../../settings.php'; ?>
<?php
include '../../config/conn.php';
include '../../config/logic/logic_operasional.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operasional - Expenses</title>
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
        <h2 class="table-title">Pencatatan Biaya Operasional</h2>
        <h4 class="sub-title">Menu Untuk Manajemen Biaya Operasional</h4>

        <form method="get" class="search" action="operasional.php">
            <div class="search-wrapper">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-bar" name="cari"
                        value="<?= htmlspecialchars($_GET['cari'] ?? '') ?>" placeholder="Cari nama...">
                    <img src="../../assets/img/Bantani 1.png" alt="Logo Bantani">
                </div>

                <div class="month-input-wrapper" onclick="document.getElementById('bulan').showPicker()">
                    <input type="month" id="bulan" name="bulan" value="<?= htmlspecialchars($_GET['bulan'] ?? '') ?>"
                        onchange="this.form.submit()" class="tanggal-bar">
                </div>
            </div>
        </form>

        <table id="Table">
            <thead id="tabel-expenses">
                <tr>
                    <th style="text-align: center;">No</th>
                    <th style="text-align: center;">SKU</th>
                    <th style="text-align: center;">Tanggal</th>
                    <th style="text-align: center;">Nama</th>
                    <th style="text-align: center;">QTY</th>
                    <th style="text-align: center;">Satuan</th>
                    <th style="text-align: center;">Harga</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: center;">Keterangan</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-data">
                <?php
                $cari = $_GET['cari'] ?? '';
                $bulan = $_GET['bulan'] ?? '';
                $conditions = [];
                if (!empty($cari))
                    $conditions[] = "nama LIKE '%$cari%'";
                if (!empty($bulan))
                    $conditions[] = "DATE_FORMAT(tanggal, '%Y-%m') = '$bulan'";
                $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

                $sql = "SELECT * FROM operasional $whereClause ORDER BY tanggal DESC";
                $result = $conn->query($sql);
                $no = 1;
                if ($result->num_rows > 0) {
                    foreach ($result as $row) {
                        echo "<tr>
                            <td>$no</td>
                            <td>{$row['sku']}</td>
                            <td>{$row['tanggal']}</td>
                            <td>{$row['nama']}</td>
                            <td>{$row['qty']}</td>
                            <td>{$row['satuan']}</td>
                            <td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
                            <td>Rp " . number_format($row['jumlah'], 0, ',', '.') . "</td>
                           <td>{$row['keterangan']}</td>

                            <td>
                                <button class='delete-btn' data-link='?hapus_data={$row['sku']}'><i class='fas fa-trash'></i></button>
                                  <button class='edit-btn' data-link='?update_row={$row['sku']}#form_edit_insert'><i class='fas fa-edit'></i></button>
                            </td>
                        </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='10'>Tidak ada data.</td></tr>";
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

        <section class="form-container" id="form_edit_insert">
            <h2 class="table-title" id="table-title-form">
                Form <?= isset($data_update) ? 'Update' : 'Input' ?> Biaya Operasional</h2>
            <?php
            if (!isset($data_update)) {
                $sku = 'OP' . substr(time(), -4) . rand(10, 99);
            }
            ?>
            <form action="operasional.php" method="POST">
                <input type="hidden" name="action" value="<?= isset($data_update) ? 'update' : 'insert' ?>">
                <input type="hidden" name="id" value="<?= $data_update['sku'] ?? '' ?>">

                <div class="form-group-card">
                    <label for="sku">SKU</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-cubes"></i>
                        <input type="text" name="sku" id="sku" required readonly
                            value="<?= isset($data_update) ? $data_update['sku'] : '' ?>"
                            placeholder="Klik Generate setelah isi tanggal & nama " style="flex: 1;">

                        <button type="button" id="generateSKUBtn" class="generateBtn"
                            style="padding: 8px 12px; border-radius: 6px; cursor: pointer;"
                            <?= isset($data_update) ? 'disabled' : '' ?>>
                            Generate
                        </button>
                    </div>
                </div>

                <div class="form-group-card" onclick="document.getElementById('tanggal').showPicker?.()"
                    style="cursor: pointer;">
                    <label>Tanggal</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-calendar-alt" style="pointer-events: none;"></i>
                        <input type="date" name="tanggal" id="tanggal" required
                            value="<?= $data_update['tanggal'] ?? '' ?>" style="flex: 1; pointer-events: auto;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Nama</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-box"></i>
                        <input type="text" name="nama" required placeholder="Contoh: Tinta Printer, Kertas A4, dll"
                            value="<?= $data_update['nama'] ?? '' ?>" style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label for="satuan">Satuan</label>
                    <div class="input-wrapper" style="position: relative;">
                        <select id="satuan" name="satuan" required style="padding-left: 42px;">
                            <option value="" disabled selected hidden>-- Pilih Satuan --</option>
                            <?php
                            $satuanList = [
                                "Unit",
                                "Token",
                                "Sewa",
                                "Pcs",
                                "Set",
                                "Roll",
                                "Meter",
                                "Core",
                                "Port",
                                "Slot",
                                "Reel",
                                "Box",
                                "Pack",
                                "Pair",
                                "Panel",
                                "Tower",
                                "Channel",
                                "Device",
                                "Outlet",
                                "Link",
                                "Line",
                                "Drop",
                                "Spool"
                            ];
                            $selectedSatuan = $data_update['satuan'] ?? '';
                            foreach ($satuanList as $satuan) {
                                $selected = ($selectedSatuan === $satuan) ? 'selected' : '';
                                echo "<option value=\"$satuan\" $selected>$satuan</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group-card">
                    <label>QTY</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-sort-numeric-up-alt"></i>
                        <input type="number" name="qty" id="qty" required placeholder="Contoh: 11"
                            value="<?= $data_update['qty'] ?? '' ?>" oninput="hitungJumlah()" style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Harga (Rp)</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-money-bill-wave"></i>
                        <input type="text" name="harga" id="harga" required placeholder="Contoh: 20.000"
                            value="<?= isset($data_update) ? number_format($data_update['harga'], 0, ',', '.') : '' ?>"
                            oninput="formatRupiah(this); hitungJumlah();" style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label>Jumlah (Rp)</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-calculator"></i>
                        <input type="text" name="jumlah" id="jumlah" readonly
                            value="<?= isset($data_update) ? number_format($data_update['jumlah'], 0, ',', '.') : '' ?>"
                            style="flex: 1;">
                    </div>
                </div>
                <div class="form-group-card">
                    <label for="keterangan">Keterangan</label>
                    <div style="display: flex; align-items: start; gap: 10px;">
                        <i class="fas fa-info-circle" style="margin-top: -27px;"></i>
                        <textarea name="keterangan" id="keterangan" rows="3"
                            placeholder="Masukkan keterangan tambahan jika perlu..."
                            style="flex: 1; resize: vertical;"><?= $data_update['keterangan'] ?? '' ?></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" data-redirect="operasional.php">
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
    <script src="../../assets/js/Eksport/eksport_operasional.js"></script>
    <script src="../../assets/js/Generate/generate_SKUoperasional.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script>
    const satuanSelect = new TomSelect("#satuan", {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        },
        placeholder: "-- Pilih Satuan --",
        onInitialize: function() {
            const wrapper = this.wrapper;
            wrapper.classList.add('ts-icon-left'); // ← Tambahkan class ke wrapper

            const control = wrapper.querySelector('.ts-control');

            // Buat ikon
            const icon = document.createElement('i');
            icon.className = 'fas fa-balance-scale ts-icon'; // ← Gunakan class 'ts-icon'

            // Masukkan ikon ke dalam ts-control
            control.insertAdjacentElement('afterbegin', icon);
        }
    });
    </script>

    <audio id="deleteSound" src="../../assets/sound/delete.mp3" preload="auto"></audio>
    <audio id="saveSound" src="../../assets/sound/save.mp3" preload="auto"></audio>

</body>

</html>