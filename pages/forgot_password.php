<?php
require_once '../module/config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
$email = trim($_POST["email"]);

$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND email = ?");
$stmt->bind_param("ss", $username, $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 1) {
    $token = bin2hex(random_bytes(16));
    $expiry = date("Y-m-d H:i:s", time() + 3600); // 1 tiếng

    $stmt->close();
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE username = ?");
    $stmt->bind_param("sss", $token, $expiry, $username);
    $stmt->execute();

    $link = "reset_password.php?token=$token";
    $message = "Đường dẫn đặt lại mật khẩu: <a href='$link'>$link</a>";
} else {
    $message = "Tên người dùng hoặc email không đúng!";
}

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/forgot_password.css">
    <title>Document</title>
</head>
<body>
    <h2>Quên mật khẩu</h2>
        <form method="POST">
            <label>Tên đăng nhập:</label>
            <input type="text" name="username" required>
            <label>Email:</label>
            <input type="email" name="email" required>
            <button type="submit">Gửi yêu cầu</button>
        </form>
<p><?php echo $message; ?></p>
</body>
</html>

