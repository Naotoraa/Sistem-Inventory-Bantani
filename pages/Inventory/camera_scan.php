<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Barang Keluar</title>

    <link rel="icon" href="../../assets/img/Bantani 1.png">
    <link rel="stylesheet" href="../../assets/css/Opsional/scan.css">

    <!-- 🔥 pakai CDN yang lebih stabil -->
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        #scanner {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #059669;
            background-color: #000;
        }
    </style>
</head>

<body>

    <div class="scan-container">

        <div class="scan-title">
            Scan QR / Barcode Barang
        </div>

        <div id="scanner"></div>

        <div id="result" style="margin-top: 15px; font-weight: bold;">
            Arahkan QR Code ke kamera
        </div>

        <button onclick="window.location.href='barang_keluar.php'" class="btn-back">
            ← Kembali
        </button>

        <button id="btn-refresh" class="btn-refresh">
            🔄 Scan Lagi
        </button>

    </div>

    <script>
        let isScanning = true;
        let html5QrCode;

        // 🔊 beep
        let beep = new Audio("https://actions.google.com/sounds/v1/cartoon/wood_plank_flicks.ogg");

        document.getElementById("btn-refresh").addEventListener("click", () => {
            isScanning = true;
            document.getElementById("result").innerText = "Arahkan QR Code ke kamera";
        });

        // 🔥 START SCANNER
        function startScanner() {

            html5QrCode = new Html5Qrcode("scanner");

            html5QrCode.start({
                        facingMode: "environment"
                    }, {
                        fps: 10,
                        qrbox: {
                            width: 250,
                            height: 250
                        }
                    },
                    (decodedText) => {

                        if (!isScanning) return;
                        isScanning = false;

                        console.log("SCAN:", decodedText);
                        document.getElementById("result").innerText = "Scan: " + decodedText;

                        // 🔥 PATH FIX (WAJIB)
                        fetch("/Gudang/pages/Inventory/scan.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: "barcode=" + encodeURIComponent(decodedText)
                            })
                            .then(res => res.json())
                            .then(data => {

                                console.log("DATA:", data);

                                if (data.status === "success") {

                                    beep.currentTime = 0;
                                    beep.play();

                                    Swal.fire({
                                        icon: "success",
                                        title: data.nama_barang,
                                        text: "Berhasil keluar",
                                        timer: 1200,
                                        showConfirmButton: false
                                    });

                                } else {
                                    Swal.fire({
                                        icon: "error",
                                        title: "Gagal",
                                        text: data.msg,
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }

                                // 🔄 lanjut scan lagi
                                setTimeout(() => {
                                    isScanning = true;
                                    document.getElementById("result").innerText = "Arahkan QR Code ke kamera";
                                }, 1200);

                            })
                            .catch(err => {
                                console.error("Fetch Error:", err);
                                Swal.fire("Error koneksi ke server");
                                isScanning = true;
                            });

                    },
                    (errorMessage) => {
                        // ignore biar ga spam
                    }
                )
                .catch(err => {
                    console.log("Kamera error:", err);
                    document.getElementById("result").innerHTML =
                        "<span style='color:red;'>Kamera tidak bisa diakses</span>";
                });
        }

        // jalanin
        document.addEventListener("DOMContentLoaded", startScanner);
    </script>

</body>

</html>