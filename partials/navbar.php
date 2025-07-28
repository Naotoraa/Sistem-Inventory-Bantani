<?php
require_once 'settings.php';
?>

<header class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <a class="navbar-brand d-flex align-items-center" href="https://bantani.net.id/">
            <img src="../../assets/img/Bantani 1.png" alt="Logo" width="35" height="35" class="me-2">
            <span>BANTANI INVENTORY</span>
        </a>

        <div class="navbar-icons">
            <div class="profile-wrapper">
                <div class="profile-pentagon">
                    <img src="../../assets/img/user.png" alt="avatar" />
                </div>

                <div class="profile-dropdown">
                    <div class="profile-info">
                        <img src="../../assets/img/profile.png" alt="avatar" />
                        <div>
                            <strong>Admin</strong>
                            <small>admin@bantani.net.id</small>
                        </div>
                    </div>
                    <div class="profile-actions">
                        <a href="<?php echo $link; ?>pages/Menu/setting.php"><i class="fas fa-user"></i>
                            Profile</a>
                        <a href="<?php echo $link; ?>pages/Menu/logout.php"><i class="fas fa-sign-out-alt"></i>
                            Logout</a>
                    </div>

                </div>
            </div>
        </div>
</header>