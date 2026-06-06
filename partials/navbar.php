<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../../config/conn.php';

$user = null;

if (isset($_SESSION['user_id']) || isset($_SESSION['id'])) {
    $user_id = $_SESSION['user_id'] ?? $_SESSION['id'];
    $q = mysqli_query($conn, "SELECT nama,email,foto FROM users WHERE id='$user_id' LIMIT 1");
    $user = mysqli_fetch_assoc($q);
}

$foto = !empty($user['foto']) ? $user['foto'] : "default.png";
?>
<header class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <a class="navbar-brand d-flex align-items-center" href="https://bantani.net.id/">
            <img src="../../assets/img/Bantani 1.png" alt="Logo" width="35" height="35" class="me-2">
            <span>BANTANI INVENTORY</span>
        </a>

        <div class="navbar-icons d-flex align-items-center">

            <div class="theme-switch-wrapper me-4">
                <label class="theme-switch" for="btSwitchNav">
                    <input type="checkbox" id="btSwitchNav" class="theme-toggle-checkbox" />
                    <div class="slider round">
                        <i class="fas fa-sun sun-icon"></i>
                        <i class="fas fa-moon moon-icon"></i>
                    </div>
                </label>
                <span class="theme-label-text" style="display: none;">Tidak aktif</span>
            </div>

            <div class="profile-wrapper">
                <div class="profile-pentagon">
                    <img src="../../assets/img/user.png" alt="avatar" />
                </div>

                <div class="profile-dropdown">
                    <div class="profile-info">
                        <img src="/../Gudang/pages/uploads/profile/<?php echo $foto; ?>"
                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;"
                            onerror="this.src='../../assets/img/user.png'">

                        <div>
                            <strong><?= htmlspecialchars($user['nama'] ?? 'Admin'); ?></strong>
                            <small><?= htmlspecialchars($user['email'] ?? 'admin@bantani.net.id'); ?></small>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <a href="<?= BASE_URL; ?>/pages/Menu/setting.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a href="<?= BASE_URL; ?>/pages/Menu/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</header>