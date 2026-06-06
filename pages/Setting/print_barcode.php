<?php
require '../../config/conn.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Barang</title>
    <link rel="icon" href="../../assets/img/Bantani 1.png" type="image/x-icon">
    <link rel="stylesheet" href="../../assets/css/Opsional/barcode.css">
</head>

<body>
    <button class="btn-print no-print" onclick="window.print()">[ CETAK 40 LABEL SYSTEM ]</button>

    <div class="a4-paper">
        <?php
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            die("<h3 style='color:black; padding:20px;'>Error: ID Barang tidak ditemukan. Balik lagi ke halaman setting ya!</h3>");
        }

        $id_target = mysqli_real_escape_string($conn, $_GET['id']);

        $query = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id_target'");
        $data = mysqli_fetch_array($query);

        if ($data) {
            $id_barang = $data['id_barang'];
            $nama_barang = $data['nama_barang'];

            for ($i = 1; $i <= 40; $i++) {
        ?>

                <div class="label-item">
                    <div class="warning-stripe"></div>

                    <div class="content-area">
                        <div class="data-panel">
                            <div class="sys-title">BANTANI.INVENTORY</div>
                            <div class="sys-id"><?= $id_barang; ?></div>
                            <div class="sys-name"><?= $nama_barang; ?></div>
                        </div>

                        <div class="qr-module">
                            <div class="qrcode" data-id="<?= $id_barang; ?>"></div>
                        </div>
                    </div>

                    <div class="corner-bracket cb-bottom-right"></div>
                </div>

        <?php
            }
        } else {
            echo "<h4 style='color:black; padding: 20px;'>Barang tidak ditemukan di database.</h4>";
        }
        ?>
    </div>

    <!-- QR LIB -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let qrElements = document.querySelectorAll('.qrcode');

            qrElements.forEach(function(el) {
                let idBarang = el.getAttribute('data-id');

                // 🔥 FORMAT FINAL (prefix biar aman + gampang parsing)
                let qrValue = "BRG-" + idBarang;

                new QRCode(el, {
                    text: qrValue,
                    width: 60, // diperbesar biar gampang discan
                    height: 60,
                    colorDark: "#0f172a",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            });
        });
    </script>

</body>

</html>