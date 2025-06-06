<?php
session_start();
require_once '../module/config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    if ($username === '' || $password === '') {
        $error = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['logged_in'] = true;
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $id;
                header("Location: ../index.php");
                exit;
            } else {
                $error = "Sai mật khẩu!";
            }
        } else {
            $error = "Tài khoản không tồn tại!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/login.css">
    <title>Đăng nhập</title>
</head>
<body>
    <div class="form">
        <div id="heading">Đăng nhập</div>
        <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST" action="">
            <div class="field">
                <input class="input-field" type="text" name="username" placeholder="Tên đăng nhập" required>
            </div>
            <div class="field">
                <input class="input-field" type="password" name="password" placeholder="Mật khẩu" required>
            </div>
            <div class="btn">
                <button class="button1" type="submit">Đăng nhập</button>
                 <button type='button' class="button2" onclick="window.location.href='forgot_password.php'" type="button">Forgot Password</button>
            </div>
        </form>
        <div class="link">
            Chưa có tài khoản? <a href="register.php" style="color:#4fc3f7;">Đăng ký tại đây</a>
        </div>
    </div>
</body>
</html>
