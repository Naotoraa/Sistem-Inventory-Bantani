<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

require_once '../../settings.php';
require '../../config/conn.php';

$session_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? null;

if (!isset($_SESSION['username']) || !$session_id) {
    header("Location: login.php?expired=1");
    exit();
}

$user_id = intval($session_id);
$query = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$data  = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data akun tidak ditemukan.";
    exit;
}

$data['role'] = 'admin';

$foto = !empty($data['foto']) ? $data['foto'] : "default.png";

$qBarang = mysqli_query($conn, "
SELECT id_barang, nama_barang 
FROM barang 
ORDER BY nama_barang ASC
");

$barangList = [];
while ($row = mysqli_fetch_assoc($qBarang)) {
    $barangList[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dark_mode.css">
    <link rel="stylesheet" href="../../assets/css/Menu/setting.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="flex flex-col min-h-screen">

    <script src="../../assets/js/init_theme.js"></script>

    <div id="navbar-container">
        <?php include __DIR__ . '/../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>
    </div>

    <main class="content setting-container">
        <div class="setting-header mb-4">
            <div class="profile-card">
                <img src="../uploads/profile/<?php echo $foto; ?>"
                    style="width:80px;height:80px;border-radius:50%;object-fit:cover;">

                <div class="device-name"><?= htmlspecialchars($data['nama']) ?></div>
            </div>
        </div>

        <div class="card-setting">

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5>Data Personal</h5>
                    <p class="mb-0">
                        Nama, email, nomor telepon, dan informasi lain terkait akun admin dapat diatur di sini.
                    </p>
                </div>
                <button type="button" class="btn btn-sm btn-primary toggle-personal">
                    Show Data
                </button>
            </div>

            <div class="personal-data mt-4" style="display:none;">
                <form action="../Setting/profile.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $data['id'] ?>">

                    <div class="row">
                        <div class="col-md-3 text-center mb-3">

                            <img src="../uploads/profile/<?php echo $foto; ?>"
                                style="width:80px;height:80px;border-radius:50%;object-fit:cover;">

                            <input type="file" name="foto" class="form-control form-control-sm mt-2">

                        </div>

                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label>Nama</label>
                                    <input type="text" name="nama" class="form-control"
                                        value="<?= htmlspecialchars($data['nama']) ?>">
                                </div>

                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="<?= htmlspecialchars($data['email']) ?>">
                                </div>

                                <div class="col-md-6">
                                    <label>Nomor Telepon</label>
                                    <input type="text" name="telp" class="form-control"
                                        value="<?= htmlspecialchars($data['telp']) ?>">
                                </div>

                                <div class="col-md-6">
                                    <label>Role</label>
                                    <input type="text" class="form-control"
                                        value="<?= htmlspecialchars($data['role']) ?>" readonly>
                                </div>

                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-setting">
            <h5>Theme</h5>
            <div class="d-flex justify-content-between align-items-center">
                <span>Ubah Theme</span>

                <div class="form-check form-switch">
                    <input class="form-check-input theme-toggle-checkbox" type="checkbox" role="switch"
                        id="btSwitchSetting">

                    <label class="form-check-label theme-label-text" for="btSwitchSetting">
                        Tidak aktif
                    </label>
                </div>
            </div>
        </div>

        <div class="card-setting p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Ganti Password</h5>
                    <p class="mb-0 text-white">
                        Ubah kata sandi akun Anda untuk menjaga keamanan akun.
                    </p>
                </div>

                <button class="btn btn-sm btn-warning" onclick="togglePasswordForm()">
                    <i class="fas fa-key me-1"></i> Change
                </button>
            </div>

            <form method="POST" action="../Setting/change_password.php">
                <div id="passwordForm" class="mt-4" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">Password Lama</label>
                        <div class="input-group">
                            <input type="password" name="oldPassword" id="oldPassword" class="form-control" required>
                            <span class="input-group-text toggle-password" data-target="oldPassword"
                                style="cursor:pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="newPassword" id="newPassword" class="form-control" required>
                            <span class="input-group-text toggle-password" data-target="newPassword"
                                style="cursor:pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Konfirmasi Password</label>
                        <div class="input-group">
                            <input type="password" name="confirmPassword" id="confirmPassword" class="form-control"
                                required>
                            <span class="input-group-text toggle-password" data-target="confirmPassword"
                                style="cursor:pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <div id="passwordMessage" class="mt-2 fw-bold"></div>
                    </div>

                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary btn-sm cancel-btn">
                            Cancel
                        </button>

                        <button type="submit" id="saveBtn" class="btn btn-success btn-sm" disabled>
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </main>

    <footer class="footer-container py-20 px-4 text-white">
        <div id="footer-placeholder">
            <?php include __DIR__ . '/../../partials/footer.html'; ?>
        </div>
    </footer>

    <script src="../../assets/js/Button/button.js"></script>
    <script src="../../assets/js/Opsional/pagination.js"></script>
    <script src="../../assets/js/Generate/generate_Item.js"></script>
    <script src="../../assets/js/Eksport/eksport_masuk.js"></script>
    <script src="../../assets/js/Setting/change_password.js"></script>
    <script src="../../assets/js/Setting/change_theme.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <audio id="deleteSound" src="../../assets/sound/delete.mp3" preload="auto"></audio>
    <audio id="saveSound" src="../../assets/sound/save.mp3" preload="auto"></audio>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('status') === 'updated') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data profil berhasil diperbarui',
                    confirmButtonColor: '#6faa3f' // Hijau estetik
                });

                window.history.replaceState({}, document.title, window.location.pathname);
            }

            const toggleBtn = document.querySelector(".toggle-personal");
            const box = document.querySelector(".personal-data");

            toggleBtn.onclick = function() {
                if (box.style.display === "none" || box.style.display === "") {
                    box.style.display = "block";
                    this.innerText = "Hide Data";
                } else {
                    box.style.display = "none";
                    this.innerText = "Show Data";
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

</body>

</html>