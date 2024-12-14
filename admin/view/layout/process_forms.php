<?php
session_start(); // Bắt đầu session để lưu trữ thông tin người dùng

// Include tệp kết nối database
include '../../model/connectdb.php';
//Này là random có chữ 
function generateCode($prefix, $length = 4) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = $prefix;
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $code;
}
//này là random sau kí tự ban đầu là số thôi
function generateCode1($prefix, $length = 6) {
    // Tạo số ngẫu nhiên có độ dài $length
    $number = '';
    for ($i = 0; $i < $length; $i++) {
        $number .= rand(0, 9);  // Chỉ sử dụng số từ 0 đến 9
    }
    return $prefix . $number;  // Nối tiền tố 'TG' với số ngẫu nhiên
}
try {
    // Kết nối đến database bằng PDO
    $conn = connectdb(); 

    if ($conn === null) {
        throw new Exception("Không thể kết nối đến cơ sở dữ liệu.");
    }

    // Kiểm tra nếu form Tác Giả được gửi
    if (isset($_POST['submitAuthor'])) {
       // $maTG = $_POST['maTG'];
        $maTG = generateCode1('TG');
        $tenTG = $_POST['tenTG'];

        // Kiểm tra xem mã tác giả đã tồn tại trong cơ sở dữ liệu chưa
        $sqlCheck = "SELECT COUNT(*) FROM tacgia WHERE maTG = :maTG";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':maTG', $maTG);
        $stmtCheck->execute();
        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            echo "Mã tác giả đã tồn tại!";
            exit; // Dừng script tại đây
        } else {
            // Sử dụng prepared statements để tránh SQL Injection
            $sql = "INSERT INTO tacgia (maTG, tenTG) VALUES (:maTG, :tenTG)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':maTG', $maTG);
            $stmt->bindParam(':tenTG', $tenTG);

            if ($stmt->execute()) {
                echo "Tác giả đã được thêm thành công!";
                // Quay lại trang form-add-sp.php sau khi thêm thành công
                header('Location: form-add-sp.php');
                exit;
            } else {
                // Nếu có lỗi, lấy thông tin lỗi từ PDO
                $errorInfo = $stmt->errorInfo();
                echo "Lỗi: " . $errorInfo[2];
            }
        }
    }

    // Kiểm tra nếu form Danh Mục được gửi
    if (isset($_POST['submitCategory'])) {
        $maLoai = $_POST['maLoai'];
        $maLoai = generateCode('L', 4);
        $tenLoai = $_POST['tenLoai'];

        // Kiểm tra xem mã danh mục đã tồn tại trong cơ sở dữ liệu chưa
        $sqlCheck = "SELECT COUNT(*) FROM loaisach WHERE maLoai = :maLoai";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':maLoai', $maLoai);
        $stmtCheck->execute();
        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            echo "Mã danh mục đã tồn tại!";
            exit; // Dừng script tại đây
        } else {
            // Sử dụng prepared statements để tránh SQL Injection
            $sql = "INSERT INTO loaisach (maLoai, tenLoai) VALUES (:maLoai, :tenLoai)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':maLoai', $maLoai);
            $stmt->bindParam(':tenLoai', $tenLoai);

            if ($stmt->execute()) {
                echo "Danh mục đã được thêm thành công!";
                // Quay lại trang form-add-sp.php sau khi thêm thành công
                header('Location: form-add-sp.php');
                exit;
            } else {
                // Nếu có lỗi, lấy thông tin lỗi từ PDO
                $errorInfo = $stmt->errorInfo();
                echo "Lỗi: " . $errorInfo[2];
            }
        }
    }

    // Kiểm tra nếu form Nhà Xuất Bản được gửi
    if (isset($_POST['submitPublisher'])) {
        //$maNXB = $_POST['maNXB'];
          $maNXB = generateCode1('XB', 4);
        $tenNXB = $_POST['tenNXB'];

        // Kiểm tra xem mã nhà xuất bản đã tồn tại trong cơ sở dữ liệu chưa
        $sqlCheck = "SELECT COUNT(*) FROM nhaxuatban WHERE maNXB = :maNXB";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bindParam(':maNXB', $maNXB);
        $stmtCheck->execute();
        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            echo "Mã nhà xuất bản đã tồn tại!";
            exit; // Dừng script tại đây
        } else {
            // Sử dụng prepared statements để tránh SQL Injection
            $sql = "INSERT INTO nhaxuatban (maNXB, tenNXB) VALUES (:maNXB, :tenNXB)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':maNXB', $maNXB);
            $stmt->bindParam(':tenNXB', $tenNXB);

            if ($stmt->execute()) {
                echo "Nhà xuất bản đã được thêm thành công!";
                // Quay lại trang form-add-sp.php sau khi thêm thành công
                header('Location: form-add-sp.php');
                exit;
            } else {
                // Nếu có lỗi, lấy thông tin lỗi từ PDO
                $errorInfo = $stmt->errorInfo();
                echo "Lỗi: " . $errorInfo[2];
            }
        }
    }
} catch (PDOException $e) {
    // Xử lý lỗi kết nối PDO
    echo "Lỗi kết nối: " . $e->getMessage();
} catch (Exception $e) {
    // Xử lý các lỗi khác
    echo "Lỗi: " . $e->getMessage();
}

// Kết thúc và đóng kết nối
$conn = null;
?>
