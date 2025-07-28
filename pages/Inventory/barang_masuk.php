<?php require_once '../../settings.php'; ?>
<?php
include '../../config/logic/logic_masuk.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Masuk - Item</title>
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

<bscri class="flex flex-col min-h-screen">
    <div id="navbar-container">
        <?php include __DIR__ . '/../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>
    </div>

    <div class="content">
        <section id="dashboard">
            <h2 class="table-title">Barang Masuk</h2>
            <h4 class="sub-title">Menu Untuk Mengatur Barang Masuk</h4>
            <form method="get" class="search" action="barang_masuk.php">
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

            <table id="myTable">
                <thead id="tabel-barang">
                    <tr>
                        <th style="text-align: center;">No</th>
                        <th style="text-align: center;">ID Barang</th>
                        <th style="text-align: center;">Nama Barang</th>
                        <th style="text-align: center;">Kategori</th>
                        <th style="text-align: center;">QTY</th>
                        <th style="text-align: center;">Satuan</th>
                        <th style="text-align: center;">Tanggal Masuk</th>
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
                        $conditions[] = "(id_barang LIKE '%$cari%' OR nama_barang LIKE '%$cari%' OR kategori LIKE '%$cari%')";
                    }

                    if (!empty($bulan)) {
                        $conditions[] = "DATE_FORMAT(tanggal_masuk, '%Y-%m') = '$bulan'";
                    }

                    $whereClause = '';
                    if (!empty($conditions)) {
                        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
                    }

                    $sql = "SELECT * FROM barang_masuk $whereClause ORDER BY id DESC";
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
                        <td><?= htmlspecialchars($row['tanggal_masuk']) ?></td>
                        <td>
                            <button type="button" data-link="?hapus_data=<?= $row['id'] ?>" class="delete-btn">
                                <i class="fas fa-trash-alt"></i>
                            </button>

                            <button type="button" data-link="?update_row=<?= $row['id'] ?>#form_edit_insert"
                                class="edit-btn">
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
                </div>
                <div class="pagination" id="pagination-barang"></div>
            </div>
        </section>

        <section class="form-container" id="form_edit_insert">
            <h2 class="table-title" id="table-title-form">Form <?= isset($data_update) ? 'Update' : 'Input'; ?> Barang
                Masuk</h2>
            <form action="barang_masuk.php" method="POST">
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
                    <label for="date">Tanggal Masuk</label>
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" name="date"
                        value="<?= isset($data_update) ? $data_update['tanggal_masuk'] : ''; ?>" id="date" required>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" data-redirect="barang_masuk.php">
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

    <script src="../../assets/js/Button/button.js"></script>
    <script src="../../assets/js/Button/button_cancel.js"></script>
    <script src="../../assets/js/Button/button_delete.js"></script>
    <script src="../../assets/js/Button/button_save.js"></script>
    <script src="../../assets/js/Opsional/pagination.js"></script>
    <script src="../../assets/js/Opsional/tom_select.js"></script>
    <script src="../../assets/js/Generate/generate_Item.js"></script>
    <script src="../../assets/js/Eksport/eksport_masuk.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <audio id="deleteSound" src="../../assets/sound/delete.mp3" preload="auto"></audio>
    <audio id="saveSound" src="../../assets/sound/save.mp3" preload="auto"></audio>

    </body>

</html>