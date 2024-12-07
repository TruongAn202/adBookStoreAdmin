<?php
// Bắt đầu session để lưu trữ thông tin người dùng
session_start();

// Include tệp kết nối database
include '../../model/connectdb.php';

// Kết nối đến cơ sở dữ liệu
$conn = connectdb(); // Kiểm tra xem kết nối cơ sở dữ liệu có thành công không
if ($conn === null) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
}

// Kiểm tra phương thức gửi yêu cầu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy thông tin từ form
    $email = $_POST['email'] ?? '';
    $newTrangThai = $_POST['newTrangThai'] ?? '';
    $newVaiTro = $_POST['newVaiTro'] ?? '';

    // Kiểm tra và cập nhật thông tin người dùng trong database
    if ($email && $newTrangThai && $newVaiTro) {
        // Câu lệnh SQL để cập nhật thông tin người dùng
        $stmt = $conn->prepare("UPDATE roleadminuser SET trangThai = ?, vaiTro = ? WHERE email = ?");
        $stmt->execute([$newTrangThai, $newVaiTro, $email]);

        // Kiểm tra nếu có bản ghi nào được sửa
        if ($stmt->rowCount() > 0) {
            echo "Thông tin đã được cập nhật thành công!";
        } else {
            echo "Không tìm thấy người dùng với email này hoặc không có thay đổi nào.";
        }
    } else {
        echo "Vui lòng nhập đầy đủ thông tin.";
    }
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa thông tin người dùng</title>
</head>

<body>

    <h2>Sửa thông tin người dùng</h2>
    <form action="sua_thong_tin.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br><br>

        <label for="newTrangThai">Trạng Thái:</label>
        <input type="text" name="newTrangThai" id="newTrangThai" required><br><br>

        <label for="newVaiTro">Vai trò:</label>
        <input type="text" name="newVaiTro" id="newVaiTro" required><br><br>

        <button type="submit">Sửa</button>
    </form>

</body>

</html>