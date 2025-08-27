<?php require_once '../../settings.php'; ?>
<?php
include '../../config/logic/logic_keluar.php'
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Keluar - Item</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/Menu/manajemen_barang.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">
    <link rel="stylesheet" href="../../assets/css/Opsional/pagination.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
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
        <section id="dashboard">
            <h2 class="table-title">Barang Keluar</h2>
            <h4 class="sub-title">Menu Untuk Mengatur Barang Keluar</h4>
            <form method="get" action="barang_keluar.php" id="filterForm">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" class="search-bar" name="cari"
                        value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>"
                        placeholder="Cari barang...">
                    <img src="../../assets/img/Bantani 1.png" alt="Logo Bantani">
                </div>

                <div class="filter-bar">
                    <input type="month" name="bulan" id="bulan" class="tanggal-bar" value="<?= $_GET['bulan'] ?? '' ?>"
                        onchange="document.getElementById('filterForm').submit()"
                        onclick="document.getElementById('bulan').showPicker()">
                    <div class=" input-icon-wrapper">
                        <select class="input-with-icon" name="minggu"
                            onchange="document.getElementById('filterForm').submit()">
                            <option value="">-- Pilih Minggu --</option>
                            <option value="1" <?= ($_GET['minggu'] ?? '') == '1' ? 'selected' : '' ?>>Minggu 1 (1–7)
                            </option>
                            <option value="2" <?= ($_GET['minggu'] ?? '') == '2' ? 'selected' : '' ?>>Minggu 2 (8–14)
                            </option>
                            <option value="3" <?= ($_GET['minggu'] ?? '') == '3' ? 'selected' : '' ?>>Minggu 3 (15–21)
                            </option>
                            <option value="4" <?= ($_GET['minggu'] ?? '') == '4' ? 'selected' : '' ?>>Minggu 4
                                (22–akhir)
                            </option>
                        </select>
                        <i class="fas fa-calendar-week calendar-icon"></i>
                    </div>
                </div>
            </form>
            <table id="myTable">
                <thead id="tabel-barang">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">ID Barang</th>
                        <th style="text-align: center;">Nama Barang</th>
                        <th style="text-align: center;">Kategori</th>
                        <th style="text-align: center;">QTY</th>
                        <th style="text-align: center;">Satuan</th>
                        <th style="text-align: center;">Tanggal Keluar</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabel-data">
                    <?php
                    $cari = $_GET['cari'] ?? '';
                    $bulan = $_GET['bulan'] ?? '';
                    $minggu = $_GET['minggu'] ?? '';
                    $conditions = [];

                    if (!empty($cari)) {
                        $cari = $conn->real_escape_string($cari);
                        $conditions[] = "(id_barang LIKE '%$cari%' OR nama_barang LIKE '%$cari%' OR kategori LIKE '%$cari%')";
                    }

                    if (!empty($bulan)) {
                        [$tahun, $bulanNum] = explode('-', $bulan);
                        $bulanNum = str_pad($bulanNum, 2, '0', STR_PAD_LEFT); // pastikan 2 digit

                        // Default range: satu bulan penuh
                        $start = "$tahun-$bulanNum-01";
                        $end = date("Y-m-t", strtotime($start)); // tanggal akhir bulan

                        // Kalau minggu diisi
                        if (!empty($minggu)) {
                            $minggu = (int) $minggu;
                            switch ($minggu) {
                                case 1:
                                    $start = "$tahun-$bulanNum-01";
                                    $end = "$tahun-$bulanNum-07";
                                    break;
                                case 2:
                                    $start = "$tahun-$bulanNum-08";
                                    $end = "$tahun-$bulanNum-14";
                                    break;
                                case 3:
                                    $start = "$tahun-$bulanNum-15";
                                    $end = "$tahun-$bulanNum-21";
                                    break;
                                case 4:
                                    $start = "$tahun-$bulanNum-22";
                                    $end = date("Y-m-t", strtotime($start)); // tetap gunakan akhir bulan
                                    break;
                            }
                        }

                        $conditions[] = "tanggal_keluar BETWEEN '$start' AND '$end'";
                    }

                    $whereClause = '';
                    if (!empty($conditions)) {
                        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
                    }

                    $sql = "SELECT * FROM barang_keluar $whereClause ORDER BY id DESC";
                    $result = $conn->query($sql);



                    if ($result->num_rows > 0) {
                        $no = 1;
                        foreach ($result as $no => $row): ?>
                            <tr>
                                <td><?= $no + 1; ?></td>
                                <td><?= htmlspecialchars($row['id_barang']) ?></td>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= htmlspecialchars($row['kategori']) ?></td>
                                <td><?= htmlspecialchars($row['qty']) ?></td>
                                <td><?= htmlspecialchars($row['satuan']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal_keluar']) ?></td>
                                <td>
                                    <button data-link="?hapus_data=<?= $row['id'] ?>" class="delete-btn">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    <button data-link="?update_row=<?= $row['id'] ?>#form_edit_insert" class="edit-btn">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                    <?php endforeach;
                    } else {
                        echo "<tr><td colspan='8'>Tidak ada data.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            <div class="table-bottom-container">
                <div class="under_table">
                    <button type="button" class="pdf">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </button>
                    <button type="button" class="pdf" id="pemakaian">
                        <i class="fas fa-chart-bar"></i> Export Pemakaian
                    </button>
                </div>
                <div class="pagination" id="pagination-barang"></div>
            </div>
        </section>

        <section class="form-container" id="form_edit_insert">
            <h2 class="table-title" id="table-title-form">Form <?= isset($data_update) ? 'Update' : 'Input'; ?> Barang
                Keluar</h2>
            <form action="barang_keluar.php" method="POST">
                <input type="hidden" name="action" value="<?= isset($data_update) ? 'update' : 'insert' ?>">
                <input type="hidden" name="id" value="<?= isset($data_update) ? $data_update['id'] : '0' ?>">

                <div class="form-group-card">
                    <label for="id_barang">ID Barang</label>
                    <i class="fas fa-barcode"></i>
                    <select name="id_barang" id="id_barang" required
                        <?= isset($data_update) ? 'data-current-id="' . $data_update['id_barang'] . '"' : '' ?>>
                        <option value="">-- Pilih ID Barang --</option>
                        <?php foreach ($barangList as $barang): ?>
                            <option value="<?= $barang['id_barang'] ?>" data-name="<?= $barang['nama_barang'] ?>"
                                data-category="<?= $barang['kategori'] ?>" data-satuan="<?= $barang['satuan'] ?>">
                                <?= $barang['id_barang'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group-card">
                    <label for="name">Nama Barang</label>
                    <i class="fas fa-box"></i>
                    <select name="name" id="name" required
                        <?= isset($data_update) ? 'data-current-name="' . $data_update['nama_barang'] . '"' : '' ?>>
                        <option value="">-- Pilih Nama Barang --</option>
                        <?php foreach ($barangList as $barang): ?>
                            <option value="<?= $barang['nama_barang'] ?>" data-id="<?= $barang['id_barang'] ?>"
                                data-category="<?= $barang['kategori'] ?>" data-satuan="<?= $barang['satuan'] ?>">
                                <?= $barang['nama_barang'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group-card">
                    <label for="category">Kategori</label>
                    <i class="fas fa-tag"></i>
                    <input type="text" name="category" id="category" readonly>
                </div>

                <div class="form-group-card">
                    <label for="qty">QTY</label>
                    <i class="fas fa-sort-numeric-up"></i>
                    <input type="number" name="qty" value="<?= isset($data_update) ? $data_update['qty'] : ''; ?>"
                        id="qty" placeholder="Masukkan Jumlah" min="1" required>
                </div>

                <div class="form-group-card">
                    <label for="satuan">Satuan</label>
                    <i class="fas fa-weight-hanging"></i>
                    <input type="text" name="satuan" id="satuan" readonly
                        value="<?= isset($data_update['satuan']) ? ucfirst($data_update['satuan']) : '' ?>">
                </div>

                <div class="form-group-card" id="tanggal_masuk" onclick="document.getElementById('date').showPicker()">
                    <label for="date">Tanggal Keluar</label>
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" name="date"
                        value="<?= isset($data_update) ? $data_update['tanggal_keluar'] : ''; ?>" id="date" required>
                </div>
                <div class="form-actions">
                    <button type="button" class="cancel-btn" data-redirect="barang_keluar.php">
                        <i class="fas fa-times"></i> Cancel
                    </button>

                    <button type="submit" class="save-btn">
                        <i class="fas fa-save"></i> Save
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

    <?php if (!empty($start) && !empty($end)): ?>
        <p style="color: gray; margin-top: -10px; margin-left:30px;">Menampilkan data:
            <strong><?= date('d M', strtotime($start)) ?> s/d
                <?= date('d M Y', strtotime($end)) ?></strong>
        </p>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'overstock'): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Qty Melebihi Stok!',
                text: 'Qty keluar tidak boleh melebihi stok. Stok saat ini = <?= htmlspecialchars($_GET['stok']) ?>',
            }).then(() => {
                history.back();
            });
        </script>
    <?php endif; ?>

    <script>
        function showMingguSelect() {
            document.getElementById("minggu").style.display = "inline-block";
        }

        document.addEventListener("DOMContentLoaded", function() {
            const bulan = document.getElementById("bulan").value;
            if (bulan) {
                document.getElementById("minggu").style.display = "inline-block";
            }
        });
    </script>

    <script src="../../assets/js/Button/button.js"></script>
    <script src="../../assets/js/Button/button_cancel.js"></script>
    <script src="../../assets/js/Button/button_delete.js"></script>
    <script src="../../assets/js/Button/button_save.js"></script>
    <script src="../../assets/js/Opsional/pagination.js"></script>
    <script src="../../assets/js/Opsional/tom_select.js"></script>
    <script src="../../assets/js/Generate/generate_Item.js"></script>
    <script src="../../assets/js/Eksport/eksport_keluar.js"></script>
    <script src="../../assets/js/Eksport/pemakaian.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <audio id="deleteSound" src="../../assets/sound/delete.mp3" preload="auto"></audio>
    <audio id="saveSound" src="../../assets/sound/save.mp3" preload="auto"></audio>

</body>

</html>