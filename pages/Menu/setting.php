<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['username'])) {
    header("Location: login.php?expired=1");
    exit();
}
require_once '../../settings.php';
require '../../config/conn.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/Menu/setting.css">
    <link rel="stylesheet" href="../../assets/css/Partials/navbar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/Partials/footer.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<bscri class="flex flex-col min-h-screen">
    <div id="navbar-container">
        <?php include __DIR__ . '/../../partials/navbar.php'; ?>
    </div>

    <div id="sidebar-placeholder">
        <?php include __DIR__ . '/../../partials/sidebar.php'; ?>
    </div>

    <main class="content setting-container">
        <div class="setting-header mb-4">
            <div class="profile-card">
                <img src="../../assets/img/user.png" alt="User">
                <div class="device-name">Admin</div>
                <a href="#" class="rename-link">Rename</a>
            </div>
        </div>


        <div class="card-setting">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5>Data Personal</h5>
                    <p class="mb-0">Nama, email, nomor telepon, dan informasi lain terkait akun admin dapat diatur di
                        sini.</p>
                </div>
                <button class="btn btn-sm btn-light">Show Data</button>
            </div>
        </div>

        <div class="card-setting">
            <h5>Theme</h5>
            <div class="d-flex justify-content-between align-items-center">
                <span>Ubah Theme</span>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="btSwitch">
                    <label class="form-check-label text-white" for="btSwitch">Tidak aktif</label>
                </div>
            </div>
        </div>

        <div class="card-setting">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5>Ganti Password</h5>
                    <p class="mb-0">Ubah kata sandi akun Anda untuk menjaga keamanan akun.</p>
                </div>
                <button class="btn btn-sm btn-warning">Change</button>
            </div>
        </div>

    </main>



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
    <script src="../../assets/js/Generate/generate_Item.js"></script>
    <script src="../../assets/js/Eksport/eksport_masuk.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

    <audio id="deleteSound" src="../../assets/sound/delete.mp3" preload="auto"></audio>
    <audio id="saveSound" src="../../assets/sound/save.mp3" preload="auto"></audio>

    </body>

</html>