<?php
// cập nhật vai trò người dùng
include '../../model/connectdb.php';

if (isset($_POST['email'])) {
    $conn = connectdb(); // Kiểm tra kết nối
    if ($conn === null) {
        echo "Không thể kết nối đến cơ sở dữ liệu.";
        exit();
    }

    $email = $_POST['email']; // Lấy email người dùng
    $newRole = ''; // Biến để lưu vai trò mới

    // Truy vấn lấy vai trò hiện tại
    $sql = "SELECT vaiTro FROM roleadminuser WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Lấy vai trò hiện tại
    $currentRole = $stmt->fetchColumn();

    // Kiểm tra vai trò và thay đổi
    if ($currentRole == 'admin') {
        $newRole = 'user';
    } else if ($currentRole == 'user') {
        $newRole = 'admin';
    }

    // Cập nhật vai trò mới vào cơ sở dữ liệu
    $updateSql = "UPDATE roleadminuser SET vaiTro = :newRole WHERE email = :email";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bindParam(':newRole', $newRole);
    $updateStmt->bindParam(':email', $email);
    if ($updateStmt->execute()) {
        echo 'Cập nhật vai trò thành công!';
    } else {
        echo 'Lỗi khi cập nhật vai trò.';
    }
}

?>
