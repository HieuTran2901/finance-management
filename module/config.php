<?php
$host = 'localhost';
$username = 'root';
$password = ''; // hoặc 'matkhaucuaban' nếu có
$database = 'dangkydangnhap1'; // thay bằng tên thật của database

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Lỗi kết nối: " . $conn->connect_error);
}
?>
