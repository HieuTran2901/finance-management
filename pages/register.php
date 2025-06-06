<?php
require_once '../module/config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];
$confirm  = $_POST["confirm_password"];

if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
    $error = "Vui lòng điền đầy đủ thông tin!";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Email không hợp lệ!";
} elseif (strlen($password) < 8) {
    $error = "Mật khẩu phải có ít nhất 8 ký tự!";
} elseif ($password !== $confirm) {
    $error = "Mật khẩu xác nhận không khớp!";
} else {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Tên đăng nhập hoặc email đã tồn tại!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        if ($stmt->execute()) {
            header("Location: /pages/login.php");
            exit;
        } else {
            $error = "Đăng ký thất bại!";
        }
    }
    $stmt->close();
}

}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/register.css">
    <title>Đăng ký</title>
</head>
<body>

    <form method="POST" class="form">
        <p id="heading">Đăng ký</p>

        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

        <div class="field">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 
                0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 
                0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 
                0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 
                0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 
                1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 
                2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 
                1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"></path>
            </svg>
            <input type="text" name="username" placeholder="Tên đăng nhập" required class="input-field">
        </div>

        <div class="field">
    <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.708 2.827L15 11.5V5.383zM14.8 12L10 8.803l-.8.481-.8-.481L1.2 12H14.8zM1 5.383V11.5l4.708-3.29L1 5.383z"/>
    </svg>
    <input type="email" name="email" placeholder="Email" required class="input-field">
</div>

        <div class="field">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 
                6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 
                2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
            </svg>
            <input type="password" name="password" placeholder="Mật khẩu" required class="input-field">
        </div>

        <div class="field">
            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 
                6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 
                2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"></path>
            </svg>
            <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required class="input-field">
        </div>

        <div class="btn">
            <button type="submit" class="button1">Đăng ký</button>
        </div>

        <div class="link">
            
            <p>Bạn đã có tài khoản? <a href="login.php" style="color:lightblue;">Đăng nhập</a></p>
        </div>
    </form>
</body>
</html>
