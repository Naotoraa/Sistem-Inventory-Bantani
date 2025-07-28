<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
require_once '../../config/conn.php';
include '../../config/logic/logic_login.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title>Login Inventory Bantani</title>

    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/css/Menu/login_style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
    <div class="Login_form_container">
        <div class="Login_form">
            <img src="../../assets/img/Bantani 1.png" class="Logo" alt="">
            <h2>Log In</h2>
            <h4>Harap login untuk melanjutkan</h4>

            <?php if (isset($error)): ?>
            <div class="floating-alert error-alert">
                <i class="fa fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['logout']) && $_SERVER['REQUEST_METHOD'] !== 'POST'): ?>
            <div class="floating-alert success-alert">
                <i class="fa fa-sign-out-alt"></i> Anda berhasil logout.
            </div>
            <?php endif; ?>

            <?php if (isset($loginSuccess) && $loginSuccess): ?>
            <div class="floating-alert success-alert" id="loginSuccessAlert">
                <i class="fa fa-check-circle"></i> Login berhasil, mengalihkan...
            </div>
            <script>
            setTimeout(function() {
                window.location.href = "dashboard.php";
            }, 2000);
            </script>
            <?php endif; ?>

            <form class="form" method="POST">
                <div class="Input_grup">
                    <i class="fa fa-user"></i>
                    <input type="text" name="username" placeholder="Masukan Username" class="Input_text"
                        autocomplete="off" required>
                </div>

                <div class="Input_grup" style="position: relative;">
                    <i class="fa fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Masukan password"
                        class="Input_text" autocomplete="off" required>
                    <i class="fa fa-eye-slash toggle-password" onclick="togglePasswordVisibility()"></i>
                </div>

                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="Button_grup" id="Login_button">
                    <button type="submit">Sign In</button>
                </div>

                <div class="Footer">
                    <p>Belum punya Akun? <a href="https://wa.me/+628995294242" target="_blank"
                            style="color: blue; text-decoration: underline;">Hubungi Naotoraa</a></p>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_GET['expired'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    Swal.fire({
        icon: 'warning',
        title: 'Sesi Berakhir',
        text: 'Sesi anda telah berakhir. Silakan login ulang.',
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK'
    }).then(() => {

        if (window.history.replaceState) {
            const url = new URL(window.location);
            url.searchParams.delete('expired');
            window.history.replaceState({}, '', url);
        }
    });
    </script>
    <?php endif; ?>

    <script src="../../assets/js/Menu/login.js"></script>
</body>

</html>