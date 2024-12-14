<?php
include '../../model/connectdb.php';
if (isset($_POST['maHD'])) {
    try {
        $conn = connectdb();
        if ($conn === null) {
            echo "Không thể kết nối đến cơ sở dữ liệu.";
            exit();
        }

        $maHD = $_POST['maHD'];
        $newTrangThai = '';
        $sql = "SELECT trangThaiHD FROM hoaDon WHERE maHD = :maHD";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':maHD', $maHD);
        $stmt->execute();

        $currentRole = $stmt->fetchColumn();

        if ($currentRole === false) {
            echo 'Không tìm thấy đơn hàng.';
            exit();
        }

        switch ($currentRole) {
            case 'choxuly':
                $newTrangThai = 'danggiao';
                break;
            case 'danggiao':
                $newTrangThai = 'dagiao';
                break;
            case 'dahuy':
                echo 'Đơn hàng đã hủy, không thể cập nhật.';
                exit();
            case 'dagiao':
                echo 'Đơn hàng đã giao, không thể cập nhật thêm.';
                exit();
            default:
                echo 'Trạng thái không hợp lệ.';
                exit();
        }


        $updateSql = "UPDATE hoaDon SET trangThaiHD = :newTrangThai,  ngayNhan = CASE 
            WHEN :newTrangThai = 'dagiao' THEN CURDATE() 
            ELSE ngayNhan END  WHERE maHD = :maHD";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':newTrangThai', $newTrangThai);
        $updateStmt->bindParam(':maHD', $maHD);
        if ($updateStmt->execute()) {
            echo 'Cập nhật Trạng Thái thành công!';
        } else {
            echo 'Lỗi khi cập nhật Trạng Thái.';
        }
    } catch (PDOException $e) {
        echo 'Lỗi kết nối hoặc truy vấn: ' . $e->getMessage();
    }
}
