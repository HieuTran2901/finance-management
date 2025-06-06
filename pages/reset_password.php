<?php
require_once '../module/config.php';

$error = "";
$success = "";

if (isset($_GET["token"])) {
    $token = $_GET["token"];

    $stmt = $conn->prepare("SELECT username, token_expiry FROM users WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($username, $expiry);
    $stmt->fetch();

    if ($stmt->num_rows == 1 && strtotime($expiry) > time()) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $password = $_POST["password"];
            $confirm = $_POST["confirm"];
            if ($password !== $confirm) {
                $error = "Mật khẩu xác nhận không khớp!";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt->close();
                $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE username = ?");
                $stmt->bind_param("ss", $hashed, $username);
                $stmt->execute();

                $success = "Mật khẩu đã được đặt lại thành công! <a href='login.php'>Đăng nhập</a>";
            }
        }
    } else {
        $error = "Liên kết không hợp lệ hoặc đã hết hạn!";
    }
} else {
    $error = "Token không hợp lệ!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/reset_password.css">
    <title>Document</title>
</head>
<body>
    <h2>Đặt lại mật khẩu</h2>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>

    <?php if (!$success && empty($error)) : ?>
        <form method="POST">
            <label>Mật khẩu mới:</label>
            <input type="password" name="password" required>
            <label>Xác nhận mật khẩu:</label>
            <input type="password" name="confirm" required>
            <button type="submit">Đặt lại</button>
        </form>
    <?php endif; ?>
</body>
</html>

