<div class="sidebar" id="sidebar">

    <a href="<?= $link ?>/pages/Menu/dashboard.php" class="nav-link" id="dashboard">
        <i class="fa-solid fa-house"></i>
        <span>Dashboard</span>
    </a>

    <div class="nav-item">
        <a class="nav-link" id="stok">
            <i class="fa-solid fa-industry"></i>
            <span>Stock</span>
        </a>
        <div class="submenu">
            <a href="<?= $link ?>/pages/Inventory/manage_stok.php" class="submenu-item">
                <i class="fas fa-box icon orange"></i> Manage Stok
            </a>
            <a href="<?= $link ?>/pages/Inventory/info_stok.php" class="submenu-item">
                <i class="fas fa-clipboard-list"></i> Informasi Stok
            </a>
        </div>
    </div>

    <div class="nav-item">
        <a class="nav-link" id="manajemen-barang">
            <i class="fa-solid fa-box"></i>
            <span>Item</span>
        </a>
        <div class="submenu">
            <a href="<?= $link ?>/pages/Inventory/barang_masuk.php" class="submenu-item">
                <i class="fa-solid fa-circle-arrow-down"></i> Barang Masuk
            </a>
            <a href="<?= $link ?>/pages/Inventory/barang_keluar.php" class="submenu-item">
                <i class="fa-solid fa-circle-arrow-up"></i> Barang Keluar
            </a>
            <a href="<?= $link ?>/pages/Inventory/barang_migrasi.php" class="submenu-item">
                <i class="fa-solid fa-arrow-right-arrow-left"></i> Barang Migrasi
            </a>
            <a href="<?= $link ?>/pages/Inventory/barang_eror.php" class="submenu-item">
                <i class="fas fa-tools icon purple"></i> Barang Error
            </a>
        </div>
    </div>

    <div class="nav-item">
        <a class="nav-link" id="expenses">
            <i class="fa-solid fa-money-bill-wave"></i>
            <span>Expenses</span>
        </a>
        <div class="submenu">
            <a href="<?= $link ?>/pages/Expenses/operasional.php" class="submenu-item">
                <i class="fa-solid fa-toolbox"></i> Operasional
            </a>
            <a href="<?= $link ?>/pages/Expenses/services.php" class="submenu-item">
                <i class="fa-solid fa-hammer"></i> Services
            </a>
            <a href="<?= $link ?>/pages/Expenses/cicilan.php" class="submenu-item">
                <i class="fa-solid fa-money-check-dollar"></i> Cicilan
            </a>
        </div>
    </div>

    <a href="<?= $link ?>/pages/Menu/setting.php" class="nav-link" id="setting">
        <i class="fa-solid fa-cog"></i>
        <span>Setting</span>
    </a>

    <div class="sidebar-divider">
        <a href="<?= $link ?>/assets/404/404.html" class="nav-link nav-trash" id="trash">
            <i class="fa-solid fa-trash-can"></i>
            <span>Trash</span>
        </a>
    </div>

</div>