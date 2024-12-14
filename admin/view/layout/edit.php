<?php
session_start();
include '../../model/connectdb.php';
$conn = connectdb(); // Kiểm tra kết nối
if ($conn === null) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
}

// Kiểm tra ID sản phẩm
if (isset($_GET['id'])) {
    $maSach = $_GET['id'];
    $sql = "SELECT * FROM Sach WHERE maSach = :maSach";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':maSach', $maSach, PDO::PARAM_STR);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra xem sản phẩm có tồn tại
    if (!$product) {
        echo "Sản phẩm không tồn tại.";
        exit();
    }
} else {
    echo "ID sản phẩm không hợp lệ.";
    exit();
}

// Lấy danh sách danh mục
$sqlLoai = "SELECT * FROM LoaiSach";
$stmtLoai = $conn->prepare($sqlLoai);
$stmtLoai->execute();
$loaiSachList = $stmtLoai->fetchAll(PDO::FETCH_ASSOC);

// Lấy các tình trạng sách
$tinhTrangOptions = ['Còn hàng', 'Hết hàng']; 

    $anh = $product['anh']; // Giữ ảnh cũ nếu không có ảnh mới
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tenSach = $_POST['tenSach'];
        $giaKM = $_POST['giaKM'];
        $soLuong = $_POST['SoLuong'];
        $maLoai = $_POST['maLoai'];
        $moTa = $_POST['moTa'];
        $moTaDayDu = $_POST['moTaDayDu'];
    
        // Xử lý file ảnh
        $anh = $product['anh']; // Giữ ảnh cũ nếu không có ảnh mới
        if (isset($_FILES['anh1']) && $_FILES['anh1']['error'] == 0) {
            $fileTmpPath = $_FILES['anh1']['tmp_name'];
            $fileName = $_FILES['anh1']['name'];
    
            // Đường dẫn 1 - nằm trong thư mục admin
            $uploadDir1 = $_SERVER['DOCUMENT_ROOT'] . '/adBookStoreUser/admin/view/layout/' . 'assets/img-sanpham/';
            $uploadFilePath1 = $uploadDir1 . $fileName;
    
            // Đường dẫn 2 - view/layout/assets/image/
            $uploadDir2 = $_SERVER['DOCUMENT_ROOT'] . '/adBookStoreUser/view/layout/' . 'assets/image/';
            $uploadFilePath2 = $uploadDir2 . $fileName;
    
            // Di chuyển ảnh vào thư mục admin
            if (move_uploaded_file($fileTmpPath, $uploadFilePath1)) {
                // Sao chép file đến thư mục thứ hai
                if (copy($uploadFilePath1, $uploadFilePath2)) {
                    // Cập nhật tên file ảnh mới vào biến $anh
                    $anh = $fileName;
                } else {
                    echo "Lỗi khi sao chép file vào thư mục thứ hai.";
                    exit();
                }
            } else {
                echo "Lỗi khi tải lên file vào thư mục đầu tiên.";
                exit();
            }
        }
        // Cập nhật thông tin sản phẩm
        $updateSQL = "UPDATE Sach SET tenSach = :tenSach, giaKM = :giaKM, anh = :anh, SoLuong = :soLuong, maLoai = :maLoai, moTa = :moTa, moTaDayDu = :moTaDayDu WHERE maSach = :maSach";
        $stmt = $conn->prepare($updateSQL);
        $stmt->bindParam(':tenSach', $tenSach);
        $stmt->bindParam(':giaKM', $giaKM);
        $stmt->bindParam(':anh', $anh);
        $stmt->bindParam(':soLuong', $soLuong);
        $stmt->bindParam(':maLoai', $maLoai);
        $stmt->bindParam(':moTa', $moTa);
        $stmt->bindParam(':moTaDayDu', $moTaDayDu);
        $stmt->bindParam(':maSach', $maSach);
    
        if ($stmt->execute()) {
            header("Location: quanlisanpham.php");
            exit();
        } else {
            echo "Cập nhật thất bại.";
        }
    }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sửa sản phẩm | Quản trị Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="assets/css1/main.css">
</head>

<body class="app sidebar-mini rtl">
    <header class="app-header">
        <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    </header>

    <main class="app-content">
        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb side">
                <li class="breadcrumb-item active"><a href="#"><b>Sửa sản phẩm</b></a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="tenSach">Tên Sách</label>
                                <input type="text" class="form-control" id="tenSach" name="tenSach" value="<?php echo $product['tenSach']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="giaKM">Giá</label>
                                <input type="number" class="form-control" id="giaKM" name="giaKM" value="<?php echo $product['giaKM']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="anh1">Ảnh</label>
                                <!-- Thay đổi input để nhận file -->
                                <input type="file" class="form-control" id="anh1" name="anh1" accept="image/*">
                                <!-- Hiển thị ảnh hiện tại nếu có -->
                                <div class="mt-2">
                                    <label>Ảnh hiện tại:</label>
                                    <br>
                                    <!-- Kiểm tra nếu sản phẩm có ảnh -->
                                    <?php if (!empty($product['anh'])): ?>
                                        <!-- Thêm đường dẫn đến thư mục ảnh -->
                                        <img src="assets/img-sanpham/<?php echo $product['anh']; ?>" alt="Ảnh sản phẩm" style="max-width: 150px; max-height: 150px;">

                                        <!-- Nếu không có ảnh, hiển thị ảnh mặc định -->

                                    <?php endif; ?>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="SoLuong">Số Lượng</label>
                                <input type="number" class="form-control" id="SoLuong" name="SoLuong" value="<?php echo $product['SoLuong']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="moTa">Mô Tả</label>
                                <input type="text" id="moTa" name="moTa" value="<?php echo $product['moTa']; ?>">
                                
                            </div>
                            <div class="form-group">
                                <label for="moTaDayDu">Mô tả đầy đủ</label>
                                <input type="text" id="moTaDayDu" name="moTaDayDu" value="<?php echo $product['moTaDayDu']; ?>">
                            </div>
                            <!-- <div class="form-group"> -->
                                <!-- <label for="TinhTrang">Tình Trạng</label> -->
                                <!-- <select class="form-control" id="TinhTrang" name="TinhTrang" required> -->
                                   
                            <!-- </div> -->

                            <div class="form-group">
                                <label for="maLoai">Danh Mục</label>
                                <select class="form-control" id="maLoai" name="maLoai" required>
                                    <?php foreach ($loaiSachList as $loai): ?>
                                        <option value="<?php echo $loai['maLoai']; ?>" <?php echo ($product['maLoai'] == $loai['maLoai']) ? 'selected' : ''; ?>><?php echo $loai['tenLoai']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="quanlisanpham.php" class="btn btn-secondary">Hủy</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>