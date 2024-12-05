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
$tinhTrangOptions = ['Còn hàng', 'Hết hàng']; // Hoặc có thể lấy từ cơ sở dữ liệu nếu có bảng TìnhTrạng
// Xử lý cập nhật thông tin sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenSach = $_POST['tenSach'];
    $giaKM = $_POST['giaKM'];
    $soLuong = $_POST['SoLuong'];
    $tinhTrang = $_POST['TinhTrang'];
    $maLoai = $_POST['maLoai'];

    // Xử lý file ảnh
    // Xử lý file ảnh
    // Xử lý file ảnh
    $anh = $product['anh']; // Giữ ảnh cũ nếu không có ảnh mới
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tenSach = $_POST['tenSach'];
        $giaKM = $_POST['giaKM'];
        $soLuong = $_POST['SoLuong'];
        $tinhTrang = $_POST['TinhTrang'];
        $maLoai = $_POST['maLoai'];

        // Xử lý file ảnh
        $anh = $product['anh']; // Giữ ảnh cũ nếu không có ảnh mới
        if (isset($_FILES['anh']) && $_FILES['anh']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../../img-sanpham/';  // Thư mục lưu ảnh
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true); // Tạo thư mục nếu chưa tồn tại
            }

            // Kiểm tra định dạng file ảnh (chấp nhận JPG, JPEG, PNG, GIF)
            $fileName = basename($_FILES['anh']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // Kiểm tra nếu định dạng ảnh hợp lệ
            if (in_array($fileExtension, $allowedExtensions)) {
                $targetPath = $uploadDir . $fileName;

                // Di chuyển file vào thư mục đích
                if (move_uploaded_file($_FILES['anh']['tmp_name'], $targetPath)) {
                    // Nếu ảnh tải lên thành công, chỉ lưu tên ảnh vào cơ sở dữ liệu
                    $anh = $fileName;  // Lưu chỉ tên ảnh, không bao gồm đường dẫn
                } else {
                    echo "Lỗi khi tải ảnh lên: " . error_get_last()['message'];
                }
            } else {
                echo "Định dạng ảnh không hợp lệ. Chỉ chấp nhận JPG, JPEG, PNG, hoặc GIF.";
                exit();
            }
        } else {
            // Nếu không có ảnh mới, giữ lại ảnh cũ
            $anh = $product['anh'];
        }

        // Cập nhật sản phẩm vào cơ sở dữ liệu
        $updateSQL = "UPDATE Sach SET tenSach = :tenSach, giaKM = :giaKM, anh = :anh, SoLuong = :soLuong, TinhTrang = :tinhTrang, maLoai = :maLoai WHERE maSach = :maSach";
        $stmt = $conn->prepare($updateSQL);
        $stmt->bindParam(':tenSach', $tenSach);
        $stmt->bindParam(':giaKM', $giaKM);
        $stmt->bindParam(':anh', $anh);  // Cập nhật tên ảnh mới (chỉ tên ảnh)
        $stmt->bindParam(':soLuong', $soLuong);
        $stmt->bindParam(':tinhTrang', $tinhTrang);
        $stmt->bindParam(':maLoai', $maLoai);
        $stmt->bindParam(':maSach', $maSach);

        if ($stmt->execute()) {
            header("Location: quanlisanpham.php"); // Quay lại trang danh sách sản phẩm
            exit();
        } else {
            echo "Cập nhật thất bại.";
        }
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
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
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
                                <label for="anh">Ảnh</label>
                                <!-- Thay đổi input để nhận file -->
                                <input type="file" class="form-control" id="anh" name="anh" accept="image/*">
                                <!-- Hiển thị ảnh hiện tại nếu có -->
                                <div class="mt-2">
                                    <label>Ảnh hiện tại:</label>
                                    <br>
                                    <!-- Kiểm tra nếu sản phẩm có ảnh -->
                                    <?php if (!empty($product['anh'])): ?>
                                        <!-- Thêm đường dẫn đến thư mục ảnh -->
                                        <img src="../../img-sanpham/<?php echo $product['anh']; ?>" alt="Ảnh sản phẩm" style="max-width: 150px; max-height: 150px;">
                                    <?php else: ?>
                                        <!-- Nếu không có ảnh, hiển thị ảnh mặc định -->
                                        <img src="path/to/default-image.jpg" alt="Ảnh mặc định" style="max-width: 150px; max-height: 150px;">
                                    <?php endif; ?>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="SoLuong">Số Lượng</label>
                                <input type="number" class="form-control" id="SoLuong" name="SoLuong" value="<?php echo $product['SoLuong']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="TinhTrang">Tình Trạng</label>
                                <select class="form-control" id="TinhTrang" name="TinhTrang" required>
                                    <?php foreach ($tinhTrangOptions as $option): ?>
                                        <option value="<?php echo $option; ?>" <?php echo ($product['TinhTrang'] == $option) ? 'selected' : ''; ?>><?php echo $option; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

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