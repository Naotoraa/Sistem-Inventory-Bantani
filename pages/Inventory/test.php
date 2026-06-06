<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>TEST SCANNER</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        body {
            text-align: center;
            font-family: Arial;
        }

        #reader {
            width: 300px;
            margin: auto;
        }
    </style>
</head>

<body>

    <h2>TEST SCAN</h2>
    <div id="reader"></div>

    <script>
        function onScanSuccess(decodedText) {
            alert("KEBACA: " + decodedText);
        }

        let scanner = new Html5QrcodeScanner("reader", {
            fps: 10,
            qrbox: 250
        });

        scanner.render(onScanSuccess);
    </script>

</body>

</html>