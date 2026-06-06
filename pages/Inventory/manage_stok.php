<?php require_once '../../settings.php'; ?>
<?php
include '../../config/logic/logic_manage.php';
require '../../config/update_modal.php';
/** @var mysqli_result $result */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stok - Stock</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dark_mode.css">
    <link rel="stylesheet" href="../../assets/css/Menu/manage_stok.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">
    <link rel="stylesheet" href="../../assets/css/Opsional/pagination.css">
    <link rel="stylesheet" href="../../assets/css/Opsional/modal.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="flex flex-col min-h-screen">

    <script src="../../assets/js/init_theme.js"></script>

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

                                <td>
                                    <a href="#" class="link-nama" data-bs-toggle="modal"
                                        data-bs-target="#modal<?= $row['id_barang'] ?>">
                                        <?= $row['nama_barang'] ?>
                                    </a>
                                </td>

                                <td><?= $row['kategori'] ?></td>
                                <td><?= $row['stok_awal'] ?></td>
                                <td><?= $row['barang_masuk'] ?></td>
                                <td><?= $row['barang_keluar'] ?></td>
                                <td><?= $row['barang_migrasi'] ?></td>
                                <td><?= $row['barang_eror'] ?></td>
                                <td><?= $row['stok_akhir'] ?></td>
                                <td><?= $row['satuan'] ?></td>
                            </tr>

                            <div class="modal fade modal-estetik" id="modal<?= $row['id_barang'] ?>">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header header-gradient border-bottom-0 position-relative">
                                            <button type="button"
                                                class="btn-close btn-close-white position-absolute top-0 end-0 m-3 shadow-none"
                                                data-bs-dismiss="modal"></button>

                                            <div class="w-100 pe-5">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge info-badge text-white px-3 py-2 rounded-pill fw-medium"
                                                        style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                        ID: <?= $row['id_barang'] ?>
                                                    </span>
                                                    <span
                                                        class="badge bg-white text-success px-3 py-2 rounded-pill fw-bold shadow-sm">
                                                        <?= $row['kategori'] ?>
                                                    </span>
                                                </div>
                                                <h4 class="modal-title fw-bold mb-0 mt-2 text-truncate"
                                                    title="<?= $row['nama_barang'] ?>">
                                                    <i class="fas fa-box-open me-2 opacity-75"></i><?= $row['nama_barang'] ?>
                                                </h4>
                                            </div>
                                        </div>

                                        <div class="modal-body p-4">
                                            <div class="input-wrapper input-dark p-2 mb-3 position-relative rounded-3">
                                                <div class="form-floating">
                                                    <input type="text"
                                                        class="form-control bg-transparent border-0 shadow-none fw-bold px-2"
                                                        id="nama_barang_<?= $row['id_barang'] ?>"
                                                        value="<?= $row['nama_barang'] ?>" readonly placeholder="Nama Barang">
                                                    <label class="px-2">Nama Barang</label>
                                                </div>
                                                <button type="button"
                                                    class="btn btn-sm text-success btn-edit position-absolute top-50 end-0 translate-middle-y me-2"
                                                    onclick="editData('<?= $row['id_barang'] ?>', 'nama_barang', '<?= htmlspecialchars($row['nama_barang'], ENT_QUOTES) ?>')">
                                                    <i class="fas fa-pen fa-sm"></i>
                                                </button>
                                            </div>

                                            <div class="input-wrapper input-dark p-2 mb-4 position-relative rounded-3">
                                                <div class="form-floating">
                                                    <input type="text"
                                                        class="form-control bg-transparent border-0 shadow-none fw-bold px-2"
                                                        id="satuan_<?= $row['id_barang'] ?>" value="<?= $row['satuan'] ?>"
                                                        readonly placeholder="Satuan">
                                                    <label class="px-2">Satuan</label>
                                                </div>
                                                <button type="button"
                                                    class="btn btn-sm text-success btn-edit position-absolute top-50 end-0 translate-middle-y me-2"
                                                    onclick="editData('<?= $row['id_barang'] ?>', 'satuan', '<?= htmlspecialchars($row['satuan'], ENT_QUOTES) ?>')">
                                                    <i class="fas fa-pen fa-sm"></i>
                                                </button>
                                            </div>

                                            <div class="border rounded-4 p-3 shadow-sm stok-wrapper">
                                                <div class="d-flex align-items-center mb-3 px-1">
                                                    <div class="bg-success rounded-circle p-1 me-2"
                                                        style="width: 8px; height: 8px;"></div>
                                                    <h6 class="fw-bold mb-0 title-stok" style="font-size: 0.95rem;">Laporan Stok
                                                    </h6>
                                                </div>

                                                <div class="row g-2 text-center">
                                                    <div class="col-4">
                                                        <div class="card-stok p-2 rounded-3 stok-dark">
                                                            <span class="d-block mb-1 lbl-stok"
                                                                style="font-size: 0.7rem; text-transform: uppercase;">Awal</span>
                                                            <span
                                                                class="fw-bolder fs-6 val-stok"><?= $row['stok_awal'] ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div
                                                            class="card-stok p-2 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                                                            <span class="d-block text-success mb-1"
                                                                style="font-size: 0.7rem; text-transform: uppercase;">Masuk</span>
                                                            <span
                                                                class="fw-bolder text-success fs-6">+<?= $row['barang_masuk'] ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div
                                                            class="card-stok p-2 rounded-3 bg-info bg-opacity-10 border border-info border-opacity-25">
                                                            <span class="d-block text-info mb-1"
                                                                style="font-size: 0.7rem; text-transform: uppercase;">Migrasi</span>
                                                            <span
                                                                class="fw-bolder text-info fs-6"><?= $row['barang_migrasi'] ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div
                                                            class="card-stok p-2 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25">
                                                            <span class="d-block text-warning mb-1"
                                                                style="font-size: 0.7rem; text-transform: uppercase;">Keluar</span>
                                                            <span
                                                                class="fw-bolder text-warning fs-6">-<?= $row['barang_keluar'] ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div
                                                            class="card-stok p-2 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-25">
                                                            <span class="d-block text-danger mb-1"
                                                                style="font-size: 0.7rem; text-transform: uppercase;">Error</span>
                                                            <span
                                                                class="fw-bolder text-danger fs-6"><?= $row['barang_eror'] ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div
                                                            class="card-stok p-2 rounded-3 bg-success bg-opacity-25 border border-success">
                                                            <span class="d-block text-success fw-bold mb-1"
                                                                style="font-size: 0.7rem; text-transform: uppercase;">Akhir</span>
                                                            <span
                                                                class="fw-black text-success fs-5"><?= $row['stok_akhir'] ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="mt-4 d-flex justify-content-between align-items-center p-3 rounded-4 danger-dark">
                                                <div class="me-3">
                                                    <span class="d-block fw-bold text-danger" style="font-size: 14px;">
                                                        <i class="fas fa-exclamation-triangle me-1"></i> Zona Bahaya
                                                    </span>
                                                    <span class="desc-danger"
                                                        style="font-size: 11px; line-height: 1.2; display: block; margin-top: 2px;">
                                                        Hapus barang ini beserta seluruh riwayat stoknya.
                                                    </span>
                                                </div>

                                                <form action="manage_stok.php" method="POST"
                                                    id="form-hapus-<?= $row['id_barang'] ?>">
                                                    <input type="hidden" name="action_delete" value="1">
                                                    <input type="hidden" name="id_barang_hapus"
                                                        value="<?= $row['id_barang'] ?>">
                                                    <button type="button"
                                                        onclick="konfirmasiHapus('<?= $row['id_barang'] ?>', '<?= htmlspecialchars($row['nama_barang'], ENT_QUOTES) ?>')"
                                                        class="btn btn-sm text-white rounded-3 px-3 fw-bold shadow-sm"
                                                        style="background-color: #e11d48; border: none;">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
                                <option value="Modem">Modem</option>
                                <option value="Router">Router</option>
                                <option value="Access Point">Access Point</option>
                                <option value="Switch Hub">Switch Hub</option>
                                <option value="Repeater">Repeater</option>
                                <option value="Firewall Appliance">Firewall Appliance</option>
                                <option value="Mikrotik">Mikrotik</option>
                                <option value="Bridge Wireless">Bridge Wireless</option>
                                <option value="ONT / OLT">ONT / OLT</option>

                                <option value="Kabel LAN">Kabel LAN</option>
                                <option value="Kabel UTP">Kabel UTP</option>
                                <option value="Kabel STP">Kabel STP</option>
                                <option value="Kabel Fiber Optik">Kabel Fiber Optik</option>
                                <option value="Kabel Power">Kabel Power</option>
                                <option value="Konektor RJ45">Konektor RJ45</option>
                                <option value="Konektor SC/LC">Konektor SC/LC</option>
                                <option value="Pigtail Fiber">Pigtail Fiber</option>
                                <option value="Patch Cord">Patch Cord</option>

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

                                <option value="Tang Crimping">Tang Crimping</option>
                                <option value="Obeng Set">Obeng Set</option>
                                <option value="Cable Tester">Cable Tester</option>
                                <option value="Fiber Splicing Tools">Fiber Splicing Tools</option>
                                <option value="Ladder / Tangga">Ladder / Tangga</option>
                                <option value="Alat Ukur Signal">Alat Ukur Signal</option>
                                <option value="Senter Kepala / Alat Safety">Senter Kepala / Alat Safety</option>

                                <option value="Server">Server</option>
                                <option value="Client Device (CPE)">Client Device (CPE)</option>
                                <option value="Mini PC / NUC">Mini PC / NUC</option>
                                <option value="NAS / Storage">NAS / Storage</option>
                                <option value="Monitoring Device">Monitoring Device</option>
                                <option value="Controller Unifi">Controller Unifi</option>

                                <option value="Fan / Cooling System">Fan / Cooling System</option>
                                <option value="Penanda Kabel / Label">Penanda Kabel / Label</option>
                                <option value="Sambungan / Joint">Sambungan / Joint</option>
                                <option value="Ties / Isolasi Kabel">Ties / Isolasi Kabel</option>
                                <option value="Adaptor Converter">Adaptor Converter</option>
                                <option value="Stiker / Material Branding">Stiker / Material Branding</option>

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

    <form id="form-update" action="manage_stok.php" method="POST" style="display:none;">
        <input type="hidden" name="action_update" value="1">
        <input type="hidden" name="update_id" id="update_id">
        <input type="hidden" name="update_field" id="update_field">
        <input type="hidden" name="update_value" id="update_value">
    </form>

    <footer class="footer-container py-20 px-4 text-white">
        <div id="footer-placeholder">
            <?php include __DIR__ . '/../../partials/footer.html'; ?>
        </div>
    </footer>

    <script src="../../assets/js/Button/button_cancel.js"></script>
    <script src="../../assets/js/Button/button_save.js"></script>
    <script src="../../assets/js/Opsional/foto_option.js"></script>
    <script src="../../assets/js/Opsional/pagination.js"></script>
    <script src="../../assets/js/Opsional/modal.js"></script>
    <script src="../../assets/js/Generate/generate_IDbarang.js"></script>
    <script src="../../assets/js/Eksport/eksport_manage.js"></script>
    <script src="../../assets/js/global_theme.js"></script>
    <script src="../../assets/js/Setting/change_theme.js"></script>

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

        function updateIDBarangIfReady() {
            const namaBarang = namaInput.value.trim();
            const kategori = kategoriSelect.value;
            if (namaBarang && kategori) {
                generateIDBarang();
            }
        }

        namaInput.addEventListener("input", updateIDBarangIfReady);
        kategoriSelect.addEventListener("change", updateIDBarangIfReady);
    </script>

    <script>
        // FUNGSI KONFIRMASI HAPUS
        function konfirmasiHapus(id, nama) {
            Swal.fire({
                title: 'Yakin mau hapus?',
                html: `Data <b>${nama}</b> beserta riwayat stoknya akan terhapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-hapus-' + id).submit();
                }
            });
        }

        // FUNGSI EDIT ESTETIK (SUPER CLEAN & SIMETRIS + FIX WARNA)
        function editData(id, field, currentValue) {
            let fieldLabel = field === 'nama_barang' ? 'Nama Barang' : 'Satuan';
            let inputType = field === 'satuan' ? 'select' : 'text';

            // Pilihan satuan untuk select dropdown SweetAlert
            let inputOptions = field === 'satuan' ? {
                'unit': 'Unit',
                'pcs': 'Pcs',
                'roll': 'Roll',
                'meter': 'Meter',
                'set': 'Set',
                'box': 'Box',
                'rim': 'Rim',
                'pack': 'Pack'
            } : null;

            Swal.fire({
                title: `Ubah ${fieldLabel}`,
                text: `Sesuaikan ${fieldLabel.toLowerCase()} barang ini dengan data yang baru.`,
                input: inputType,
                inputOptions: inputOptions,
                inputValue: currentValue,
                background: '#ffffff', // Paksa background modal putih
                color: '#1e293b', // Paksa teks modal warna gelap
                showCancelButton: true,
                confirmButtonColor: '#689f38', // Hijau andalan Bantani
                cancelButtonColor: '#94a3b8', // Abu-abu soft
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                reverseButtons: true, // Posisi tombol Simpan di kanan, Batal di kiri
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                },
                didOpen: () => {
                    // Tangkap elemen input (baik text maupun dropdown select)
                    const inputEl = Swal.getInput();
                    if (inputEl) {
                        // Paksa background input abu-abu super terang & teks gelap pekat
                        inputEl.style.color = '#1e293b';
                        inputEl.style.backgroundColor = '#f8fafc';
                        inputEl.style.border = '1px solid #cbd5e1';
                        inputEl.style.borderRadius = '8px';
                        inputEl.style.padding = '12px';
                        inputEl.style.fontWeight = '600';

                        // Kalau ini dropdown (select), paksa juga opsi di dalamnya biar teksnya nggak putih
                        if (inputType === 'select') {
                            let options = inputEl.querySelectorAll('option');
                            options.forEach(opt => {
                                opt.style.color = '#1e293b';
                                opt.style.backgroundColor = '#ffffff';
                            });
                        }

                        // Tambahin animasi transisi & efek glow hijau pas diklik (focus)
                        inputEl.style.transition = 'all 0.3s ease';
                        inputEl.addEventListener('focus', () => {
                            inputEl.style.borderColor = '#689f38';
                            inputEl.style.boxShadow = '0 0 0 0.25rem rgba(104, 159, 56, 0.25)';
                            inputEl.style.outline = 'none';
                        });
                        inputEl.addEventListener('blur', () => {
                            inputEl.style.borderColor = '#cbd5e1';
                            inputEl.style.boxShadow = 'none';
                        });
                    }
                },
                inputValidator: (value) => {
                    if (!value || value.trim() === '') {
                        return `${fieldLabel} nggak boleh kosong ya!`;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && result.value !== currentValue) {
                    document.getElementById('update_id').value = id;
                    document.getElementById('update_field').value = field;
                    document.getElementById('update_value').value = result.value;
                    document.getElementById('form-update').submit();
                }
            });
        }

        // GLOBAL NOTIFIKASI SUKSES / GAGAL DARI URL
        const urlParams = new URLSearchParams(window.location.search);
        const statusAction = urlParams.get('status');

        if (statusAction === 'deleted') {
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Data barang berhasil dihapus.',
                showConfirmButton: false,
                timer: 2000
            });
        } else if (statusAction === 'error_delete') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat menghapus data.',
                showConfirmButton: false,
                timer: 2000
            });
        } else if (statusAction === 'updated') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data sukses diperbarui.',
                showConfirmButton: false,
                timer: 2000
            });
        } else if (statusAction === 'error_update') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: 'Terjadi kesalahan saat memperbarui data.',
                showConfirmButton: false,
                timer: 2000
            });
        }
        // ================= HACK BIAR SWEETALERT BISA DIKETIK DI DALAM MODAL =================
        document.addEventListener('focusin', function(e) {
            if (e.target.closest('.swal2-container')) {
                e.stopImmediatePropagation();
            }
        });
    </script>

    <audio id="saveSound" src="assets/sound/save.mp3" preload="auto"></audio>

</body>

</html>