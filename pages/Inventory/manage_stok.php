<?php require_once '../../settings.php'; ?>
<?php
include '../../config/logic/logic_manage.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stok - Stock</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/Menu/manage_stok.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">
    <link rel="stylesheet" href="../../assets/css/Opsional/pagination.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">



</head>

<body class="flex flex-col min-h-screen">

    <div id="navbar-container">
        <?php include __DIR__ . '/../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>
    </div>

    <div class="content">
        <h2 class="table-title">Manage Stok</h2>
        <h4 class="sub-title">Menu Untuk Manage Stok Gudang</h4>
        <section class="table-section">
            <form method="get" class="search" action="manage_stok.php">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-bar" name="cari"
                        value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>"
                        placeholder="Cari barang...">
                    <img src="../../assets/img/Bantani 1.png" alt="Logo Bantani">
                </div>

                <div class="month-input-wrapper" onclick="document.getElementById('bulan').showPicker()">
                    <input type="month" id="bulan" name="bulan" value="<?= htmlspecialchars($_GET['bulan'] ?? '') ?>"
                        onchange="this.form.submit()" class="tanggal-bar">
                </div>
            </form>

            <table>
                <thead id="tabel-barang">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">ID Barang</th>
                        <th style="text-align: center;">Nama Barang</th>
                        <th style="text-align: center;">Kategori</th>
                        <th style="text-align: center;">Stok Awal</th>
                        <th style="text-align: center;">Barang Masuk</th>
                        <th style="text-align: center;">Barang Keluar</th>
                        <th style="text-align: center;">Barang Migrasi</th>
                        <th style="text-align: center;">Barang Eror</th>
                        <th style="text-align: center;">Stok Akhir</th>
                        <th style="text-align: center;">Satuan</th>
                    </tr>
                </thead>
                <tbody id="tabel-data">
                    <?php
                    $no = 1;
                    if (mysqli_num_rows($result) === 0): ?>
                        <tr>
                            <td colspan="11" style="text-align:center;">
                                Data tidak ditemukan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['id_barang'] ?></td>
                                <td><?= $row['nama_barang'] ?></td>
                                <td><?= $row['kategori'] ?></td>
                                <td><?= $row['stok_awal'] ?></td>
                                <td><?= $row['barang_masuk'] ?></td>
                                <td><?= $row['barang_keluar'] ?></td>
                                <td><?= $row['barang_migrasi'] ?></td>
                                <td><?= $row['barang_eror'] ?></td>
                                <td><?= $row['stok_akhir'] ?></td>
                                <td><?= $row['satuan'] ?></td>
                            </tr>
                        <?php endwhile; ?>
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

        <section class="barang">
            <div class="tambah-stok">
                <h2 class="input-title">Input Stok Baru</h2>
                <h4 class="sub-input-title">Ini Adalah Menu Untuk Tambah Stok Baru</h4>
                <form class="form-tambah-stok" method="POST" action="manage_stok.php" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="id_barang">ID Barang</label>
                            <div class="input-wrapper" style="display: flex; gap: 8px; align-items: center;">
                                <i class="fas fa-barcode icon"></i>
                                <input type="text" id="id_barang" name="id_barang" required readonly>
                                <!-- <button type="button" id="generateBtn">Generate</button> -->
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nama_barang">Nama Barang</label>
                            <div class="input-wrapper">
                                <i class="fas fa-box icon"></i>
                                <input type="text" id="nama_barang" name="nama_barang" placeholder="Contoh: ZTE A77FRT"
                                    required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <div class="input-wrapper">
                            <i class="fas fa-tags icon"></i>
                            <select id="kategori" name="kategori" placeholder="Pilih kategori..." required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="">-- Pilih Kategori --</option>
                                <!-- Perangkat Jaringan -->
                                <option value="Modem">Modem</option>
                                <option value="Router">Router</option>
                                <option value="Access Point">Access Point</option>
                                <option value="Switch Hub">Switch Hub</option>
                                <option value="Repeater">Repeater</option>
                                <option value="Firewall Appliance">Firewall Appliance</option>
                                <option value="Mikrotik">Mikrotik</option>
                                <option value="Bridge Wireless">Bridge Wireless</option>
                                <option value="ONT / OLT">ONT / OLT</option>

                                <!-- Kabel dan Konektor -->
                                <option value="Kabel LAN">Kabel LAN</option>
                                <option value="Kabel UTP">Kabel UTP</option>
                                <option value="Kabel STP">Kabel STP</option>
                                <option value="Kabel Fiber Optik">Kabel Fiber Optik</option>
                                <option value="Kabel Power">Kabel Power</option>
                                <option value="Konektor RJ45">Konektor RJ45</option>
                                <option value="Konektor SC/LC">Konektor SC/LC</option>
                                <option value="Pigtail Fiber">Pigtail Fiber</option>
                                <option value="Patch Cord">Patch Cord</option>

                                <!-- Perangkat Penunjang -->
                                <option value="POE Adapter">POE Adapter</option>
                                <option value="Power Supply">Power Supply</option>
                                <option value="UPS">UPS</option>
                                <option value="Inverter">Inverter</option>
                                <option value="Baterai Cadangan">Baterai Cadangan</option>
                                <option value="Box Outdoor / Indoor">Box Outdoor / Indoor</option>
                                <option value="Tiang / Pole / Bracket">Tiang / Pole / Bracket</option>
                                <option value="Antena Outdoor">Antena Outdoor</option>
                                <option value="Grounding Set">Grounding Set</option>
                                <option value="Perangkat Tower">Perangkat Tower</option>

                                <!-- Alat Instalasi dan Maintenance -->
                                <option value="Tang Crimping">Tang Crimping</option>
                                <option value="Obeng Set">Obeng Set</option>
                                <option value="Cable Tester">Cable Tester</option>
                                <option value="Fiber Splicing Tools">Fiber Splicing Tools</option>
                                <option value="Ladder / Tangga">Ladder / Tangga</option>
                                <option value="Alat Ukur Signal">Alat Ukur Signal</option>
                                <option value="Senter Kepala / Alat Safety">Senter Kepala / Alat Safety
                                </option>

                                <!-- Perangkat Server dan Client -->
                                <option value="Server">Server</option>
                                <option value="Client Device (CPE)">Client Device (CPE)</option>
                                <option value="Mini PC / NUC">Mini PC / NUC</option>
                                <option value="NAS / Storage">NAS / Storage</option>
                                <option value="Monitoring Device">Monitoring Device</option>
                                <option value="Controller Unifi">Controller Unifi</option>

                                <!-- Aksesoris & Komponen Tambahan -->
                                <option value="Fan / Cooling System">Fan / Cooling System</option>
                                <option value="Penanda Kabel / Label">Penanda Kabel / Label</option>
                                <option value="Sambungan / Joint">Sambungan / Joint</option>
                                <option value="Ties / Isolasi Kabel">Ties / Isolasi Kabel</option>
                                <option value="Adaptor Converter">Adaptor Converter</option>
                                <option value="Stiker / Material Branding">Stiker / Material Branding
                                </option>

                                <!-- Lainnya -->
                                <option value="Peralatan Kantor">Peralatan Kantor</option>
                                <option value="Peralatan Lapangan">Peralatan Lapangan</option>
                                <option value="Barang Lainnya">Barang Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="satuan">Satuan</label>
                        <div class="input-wrapper">
                            <i class="fas fa-weight-hanging icon"></i>
                            <select id="satuan" name="satuan" required>
                                <option value="">-- Pilih Satuan --</option>
                                <option value="unit">Unit</option>
                                <option value="pcs">Pcs</option>
                                <option value="roll">Roll</option>
                                <option value="meter">Meter</option>
                                <option value="set">Set</option>
                                <option value="box">Box</option>
                                <option value="rim">Rim</option>
                                <option value="pack">Pack</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="foto">Upload Foto Barang</label>
                            <div class="input-wrapper">
                                <label for="foto" class="custom-file-upload" id="customUploadLabel">
                                    <i class="fas fa-upload"></i> <span id="fileLabelText">Pilih Foto</span>
                                </label>
                                <input type="file" id="foto" name="foto" accept="image/*" required hidden>
                            </div>
                        </div>
                        <button type="button" id="btn-form" class="cancel-btn"
                            data-redirect="manage_stok.php">Batal</button>
                        <button type="button" id="btn-form" class="save-btn"
                            data-redirect="manage_stok.php">Simpan</button>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <footer class="footer-container py-20 px-4 text-white">
        <div id="footer-placeholder">
            <?php include __DIR__ . '/../../partials/footer.html'; ?>
        </div>
    </footer>

    <script src="../../assets/js/Button/button_cancel.js"></script>
    <script src="../../assets/js/Button/button_save.js"></script>
    <script src="../../assets/js/Opsional/foto_option.js"></script>
    <script src="../../assets/js/Opsional/pagination.js"></script>
    <script src="../../assets/js/Generate/generate_IDbarang.js"></script>
    <script src="../../assets/js/Eksport/eksport_manage.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            setupTablePagination("tabel-barang", "pagination-barang", 10);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const select = new TomSelect("#kategori", {});

            const wrapper = document.querySelector('.ts-wrapper');
            const icon = document.createElement('i');
            icon.className = 'fas fa-tags icon';
            wrapper.insertAdjacentElement('afterbegin', icon);
        });

        new TomSelect("#kategori", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "Pilih kategori..."
        });

        new TomSelect("#satuan", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "Pilih satuan..."
        });

        const namaInput = document.getElementById("nama_barang");
        const kategoriSelect = document.getElementById("kategori");
        const idInput = document.getElementById("id_barang");
        const generateBtn = document.getElementById("generateBtn");

        function updateIDBarangIfReady() {
            const namaBarang = namaInput.value.trim();
            const kategori = kategoriSelect.value;

            if (namaBarang && kategori) {
                generateIDBarang();
            }
        }

        namaInput.addEventListener("input", updateIDBarangIfReady);
        kategoriSelect.addEventListener("change", updateIDBarangIfReady);

        // generateBtn.addEventListener("click", function() {
        //     updateIDBarangIfReady();
        // });
    </script>

    <audio id="saveSound" src="assets/sound/save.mp3" preload="auto"></audio>

</body>

</html>