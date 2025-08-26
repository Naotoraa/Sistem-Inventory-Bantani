<?php require_once '../../settings.php'; ?>
<?php
include '../../config/conn.php';
include '../../config/logic/logic_services.php';
require '../../config/clear_cache.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service - Expenses</title>
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
    <div id="navbar-container"></div>
    <div id="sidebar-placeholder"></div>


    <div class="content">
        <h2 class="table-title">Pencatatan Biaya Service</h2>
        <h4 class="sub-title">Menu Untuk Manajemen & Form data Biaya Service</h4>
        <section>
            <form method="get" class="search" action="services.php">
                <div class="search-wrapper">
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" class="search-bar" name="cari"
                            value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>"
                            placeholder="Cari barang...">
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
                        <th style="text-align: center;">ID Service</th>
                        <th style="text-align: center;">Nama Barang</th>
                        <th style="text-align: center;">Keterangan</th>
                        <th style="text-align: center;">Tanggal Service</th>
                        <th style="text-align: center;">Biaya</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel-data">
                    <?php
                    $cari = $_GET['cari'] ?? '';
                    $bulan = $_GET['bulan'] ?? '';

                    $conditions = [];

                    if (!empty($cari)) {
                        $cari = $conn->real_escape_string($cari);
                        $conditions[] = "(id_service LIKE '%$cari%' OR nama_barang LIKE '%$cari%' OR keterangan LIKE '%$cari%')";
                    }

                    if (!empty($bulan)) {
                        $conditions[] = "DATE_FORMAT(tanggal_service, '%Y-%m') = '$bulan'";
                    }

                    $whereClause = '';
                    if (!empty($conditions)) {
                        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
                    }

                    $sql = "SELECT * FROM service $whereClause ORDER BY id_service DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $no = 1;
                        foreach ($result as $row): ?>
                            <tr>
                                <td style="text-align: center;"><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['id_service']) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_service']) ?></td>
                                <td>Rp <?= number_format($row['biaya_service'], 0, ',', '.') ?></td>
                                <td style="text-align: center;">
                                    <button type="button" data-link="?hapus_data=<?= $row['id_service'] ?>" class="delete-btn">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>

                                    <button type="button" data-link="?update_row=<?= $row['id_service'] ?>#form_edit_insert"
                                        class="edit-btn">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                    <?php endforeach;
                    } else {
                        echo "<tr><td colspan='7' style='text-align: center;'>Tidak ada data.</td></tr>";
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
        </section>

        <section class="form-container" id="form_edit_insert">
            <h2 class="table-title" id="table-title-form">
                Form <?= isset($data_update) ? 'Update' : 'Input'; ?> Biaya Service
            </h2>
            <?php
            if (!isset($data_update)) {
                $random = rand(10, 99);
                $timestamp = time();
                $newID = 'SV' . substr($timestamp, -3) . $random;
            }
            ?>

            <form action="services.php" method="POST">
                <input type="hidden" name="action" value="<?= isset($data_update) ? 'update' : 'insert' ?>">
                <input type="hidden" name="id" value="<?= isset($data_update) ? $data_update['id_service'] : '0' ?>">

                <div class="form-group-card">
                    <label for="id_service">ID Service</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-barcode"></i>
                        <input type="text" name="id_service" id="id_service" required readonly
                            value="<?= isset($data_update) ? $data_update['id_service'] : '' ?>"
                            placeholder="Klik Generate setelah isi tanggal & nama" style="flex: 1;">
                        <button type="button" id="generateIDBtn" class="generateBtn"
                            style="padding: 10px 10px; border-radius: 6px; cursor: pointer;"
                            <?= isset($data_update) ? 'disabled' : '' ?>>
                            Generate
                        </button>
                    </div>
                </div>

                <div class="form-group-card" onclick="document.getElementById('tanggal_service').showPicker?.()"
                    style="cursor: pointer;">
                    <label for="tanggal_service">Tanggal Service</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-calendar-alt" style="pointer-events: none;"></i>
                        <input type="date" name="tanggal_service"
                            value="<?= isset($data_update) ? $data_update['tanggal_service'] : ''; ?>"
                            id="tanggal_service" required style="flex: 1; pointer-events: auto;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label for="nama_barang">Nama Barang</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-box"></i>
                        <input type="text" name="nama_barang" id="nama_barang"
                            value="<?= isset($data_update) ? $data_update['nama_barang'] : ''; ?>"
                            placeholder="Contoh: Motor, Printer" required style="flex: 1;">
                    </div>
                </div>

                <div class="form-group-card">
                    <label for="keterangan">Keterangan</label>
                    <i class="fas fa-file-alt"></i>
                    <textarea name="keterangan" id="keterangan" rows="3" required
                        placeholder="Contoh: Ganti oli, bersihkan AC, instal ulang, dll"><?= isset($data_update) ? $data_update['keterangan'] : ''; ?></textarea>
                </div>

                <div class="form-group-card">
                    <label for="biaya_service">Biaya Service (Rp)</label>
                    <i class="fas fa-money-bill-wave"></i>
                    <input type="text" name="biaya_service" id="biaya_service" required placeholder="Contoh: 250.000"
                        oninput="formatRupiah(this)"
                        value="<?= isset($data_update) ? number_format($data_update['biaya_service'], 0, ',', '.') : ''; ?>">
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" data-redirect="services.php">
                        <i class="fas fa-times"></i> Cancel
                    </button>

                    <button type="submit" class="save-btn">
                        <i class="fas fa-save"></i> Save
                    </button>
                </div>
            </form>
        </section>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'error_idservice_kosong'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'ID Kosong!',
                text: 'Silakan klik tombol Generate terlebih dahulu.',
                confirmButtonColor: '#d33'
            });
        </script>
    <?php endif; ?>
    <div id="navbar-container">
        <?php include __DIR__ . '/../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>
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
    <script src="../../assets/js/Eksport/eksport_services.js"></script>
    <script src="../../assets/js/Generate/generate_IDservice.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <audio id="deleteSound" src="../../assets/sound/delete.mp3" preload="auto"></audio>
    <audio id="saveSound" src="../../assets/sound/save.mp3" preload="auto"></audio>

</body>

</html>