<?php
function do_login()
{
    global $conn;
    if (empty($_POST['username']) || empty($_POST['password'])) {

        return json_encode([
            'success' => false,
            'message' => 'Username atau password dibutuhkan'
        ]);
    }

    $username_input = trim($_POST['username']);
    $password_input = trim($_POST['password']);

    if (!empty($username_input) && !empty($password_input)) {
        $sql = "SELECT * FROM `login` WHERE `username` = ? AND `password` = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $username_input, $password_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return json_encode([
                'success' => true,
                'message' => 'Login berhasil'
            ]);
        } else {
            return json_encode([
                'success' => false,
                'message' => 'Username atau password salah'
            ]);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    echo json_encode([
        'success' => false,
        'message' => 'Request harus menggunakan metode POST'
    ]);
    exit;
}

if (!isset($_POST['action'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Action required'
    ]);
    exit;
}

$action = $_POST['action'];

if ($action == "login") {
    echo do_login();
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'You are not belong here'
]);
exit;
