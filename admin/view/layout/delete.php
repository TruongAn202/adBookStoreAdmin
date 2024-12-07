<?php
session_start(); // Bắt đầu session để lưu trữ thông tin người dùng

// Include tệp kết nối database
include '../../model/connectdb.php';
$conn = connectdb(); // Kiểm tra kết nối
if ($conn === null) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
}

// Lấy mã sách cần xóa từ URL
$maSach = isset($_GET['id']) ? $_GET['id'] : null;
if ($maSach == null) {
    echo "Mã sách không hợp lệ.";
    exit();
}

// Kiểm tra xem mã sách có tồn tại trong bảng chi tiết hóa đơn không
$sqlCheck = "SELECT COUNT(*) FROM chitiethoadon WHERE maSach = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->execute([$maSach]);
$count = $stmt->fetchColumn();

// Nếu có bản ghi trong chi tiết hóa đơn, không cho phép xóa
if ($count > 0) {
    echo "Không thể xóa sách này vì nó đang có trong chi tiết hóa đơn. Vui lòng xóa chi tiết hóa đơn trước.";
    exit();
}

// Nếu không có ràng buộc khóa ngoại, tiến hành xóa sách
$sqlDelete = "DELETE FROM Sach WHERE maSach = ?";
$stmt = $conn->prepare($sqlDelete);

if ($stmt->execute([$maSach])) {
    echo "Sách đã được xóa thành công!";
    // Điều hướng lại trang danh sách sản phẩm
    header("Location: quanlisanpham.php");
    exit();
} else {
    echo "Có lỗi xảy ra khi xóa sách. Vui lòng thử lại.";
}
?>
