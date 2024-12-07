<?php
session_start(); // Bắt đầu session để lưu trữ thông tin người dùng

// Include tệp kết nối database
include '../../model/connectdb.php';
$conn = connectdb(); // Kiểm tra kết nối
if ($conn === null) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
}

// Hàm tạo mã sách ngẫu nhiên
function generateBookCode($length = 12)
{
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = 'S'; // Mã sách bắt đầu bằng ký tự 'S'

    // Tạo mã gồm các ký tự ngẫu nhiên, với tổng độ dài là $length (bao gồm 'S')
    for ($i = 1; $i < $length; $i++) {
        $code .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $code;
}

// Lấy danh sách loại sách, nhà xuất bản và tác giả
$sql = "SELECT maLoai, tenLoai FROM loaisach";
$stmt = $conn->prepare($sql);
$stmt->execute();
$loaisach = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlNXB = "SELECT maNXB, tenNXB FROM nhaxuatban";
$stmtNXB = $conn->prepare($sqlNXB);
$stmtNXB->execute();
$nhaxuatban = $stmtNXB->fetchAll(PDO::FETCH_ASSOC);

$sqlTG = "SELECT maTG, tenTG FROM tacgia";
$stmtTG = $conn->prepare($sqlTG);
$stmtTG->execute();
$tacgia = $stmtTG->fetchAll(PDO::FETCH_ASSOC);



// Tiếp tục xử lý thêm thông tin sách vào database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mã sách tự sinh
    $maSach = generateBookCode();
    $tenSach = $_POST['tenSach'];
    $soLuong = $_POST['soLuong'];
   
    $maLoai = $_POST['maLoai'];
    $maNXB = $_POST['maNXB'];
    $giaBan = $_POST['giaBan'];
    $giaKhuyenMai = $_POST['giaKhuyenMai'];
    $maTG = $_POST['maTG'];
    $moTa= $_POST['moTa'];
    $moTaDayDu= $_POST['moTaDayDu'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Kiểm tra dữ liệu đầu vào từ form
        $tenSach = isset($_POST['tenSach']) ? $_POST['tenSach'] : '';
        $soLuong = isset($_POST['soLuong']) ? $_POST['soLuong'] : 0;
        $maLoai = isset($_POST['maLoai']) ? $_POST['maLoai'] : '';
        $maNXB = isset($_POST['maNXB']) ? $_POST['maNXB'] : '';
        $giaBan = isset($_POST['giaBan']) ? $_POST['giaBan'] : 0;
        $giaKhuyenMai = isset($_POST['giaKhuyenMai']) ? $_POST['giaKhuyenMai'] : 0;
        $maTG = isset($_POST['maTG']) ? $_POST['maTG'] : '';
        $moTa = isset($_POST['moTa']) ? $_POST['moTa'] : '';
        $moTaDayDu= isset($_POST['moTaDayDu']) ? $_POST['moTaDayDu'] : '';
        $tinhTrang = ($soLuong <= 0) ? "Hết Hàng" : "Còn Hàng";
        // Mã sách tự sinh
        $maSach = generateBookCode();
    
        // Kiểm tra ảnh và tải lên
        if (isset($_FILES['anh1']) && $_FILES['anh1']['error'] == 0) {
            $fileTmpPath = $_FILES['anh1']['tmp_name'];
            $fileName = $_FILES['anh1']['name'];
            $uploadDir = 'assets/img-sanpham/'; // Đảm bảo thư mục này tồn tại
            $uploadFilePath = $uploadDir . $fileName;
    
            // Di chuyển ảnh vào thư mục
            if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                // Câu lệnh SQL để chèn dữ liệu vào bảng sách
                $stmtInsert = $conn->prepare("INSERT INTO sach (maSach, tenSach, SoLuong, TinhTrang, maLoai, maNXB, gia, giaKM, maTG, anh,moTa,moTaDayDu) 
                                            VALUES (:maSach, :tenSach, :soLuong, :tinhTrang, :maLoai, :maNXB, :giaBan, :giaKhuyenMai, :maTG, :anh,:moTa,:moTaDayDu)");
    
                // Liên kết các tham số với giá trị
                $stmtInsert->bindParam(':maSach', $maSach);
                $stmtInsert->bindParam(':tenSach', $tenSach);
                $stmtInsert->bindParam(':soLuong', $soLuong);
                $stmtInsert->bindParam(':tinhTrang', $tinhTrang);
                $stmtInsert->bindParam(':maLoai', $maLoai);
                $stmtInsert->bindParam(':maNXB', $maNXB);
                $stmtInsert->bindParam(':giaBan', $giaBan);
                $stmtInsert->bindParam(':giaKhuyenMai', $giaKhuyenMai);
                $stmtInsert->bindParam(':maTG', $maTG);
                $stmtInsert->bindParam(':moTa', $moTa);
                $stmtInsert->bindParam(':moTaDayDu', $moTaDayDu);
                $stmtInsert->bindParam(':anh', $fileName); // Lưu tên ảnh vào CSDL
    
                // Thực thi câu lệnh SQL
                if ($stmtInsert->execute()) {
                    $_SESSION['success_message'] = "Sách đã được thêm thành công!";
                    header("Location: quanlisanpham.php");
                    exit();
                } else {
                    echo "Lỗi khi thêm sách.";
                }
            } else {
                echo "Lỗi khi tải lên ảnh.";
            }
        } else {
            echo "Chưa chọn ảnh hoặc có lỗi khi tải ảnh.";
        }
    }
}    
?>






<!DOCTYPE html>
<html lang="en">

<head>
    <title>Thêm sản phẩm | Quản trị Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css1/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body class="app sidebar-mini rtl">

    <!-- Navbar-->
    <header class="app-header">
        <!-- Sidebar toggle button-->
        <a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
    </header>

    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
        <ul class="app-menu">
            <li><a class="app-menu__item " href="bangDK.php"><i class='app-menu__icon bx bx-tachometer'></i><span
                        class="app-menu__label">Bảng điều khiển</span></a></li>
            <li><a class="app-menu__item active" href="quanlisanpham.php"><i
                        class='app-menu__icon bx bx-purchase-tag-alt'></i><span class="app-menu__label">Quản lý sản phẩm</span></a></li>
            <li><a class="app-menu__item " href="donhang.php"><i class='app-menu__icon bx bx-tachometer'></i><span
                        class="app-menu__label">Quản lí đơn hàng</span></a></li>
            <li><a class="app-menu__item " href="doanh-thu.php"><i class='app-menu__icon bx bx-tachometer'></i><span
                        class="app-menu__label">Quản lí doanh thu</span></a></li>
            <li><a class="app-menu__item " href="quanliTaiKhoan.php"><i class='app-menu__icon bx bx-id-card'></i>
                    <span class="app-menu__label">Quản lý Tài Khoản</span></a></li>
        </ul>
    </aside>

    <main class="app-content">
        <div class="app-title">
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item">Danh sách sản phẩm</li>

            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <h3 class="tile-title">Tạo mới sản phẩm</h3>
                    <div class="tile-body">
                        <form class="row" method="POST" enctype="multipart/form-data">
                            <!-- Các nút thêm -->
                            <div class="row">
                                <!-- Hàng chứa các nút thêm -->
                                <div class="form-group col-12 d-flex justify-content-start gap-3 mb-3">
                                    <!--data-bs-toggle="modal" data-bs-target="#addAuthorModal để kích hoạt một modal lướt xuống dưới thấy"-->
                                    <a class="btn btn-add btn-sm" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
                                        <i class="fas fa-folder-plus"></i> Thêm tác giả
                                    </a>
                                    <a class="btn btn-add btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                        <i class="fas fa-folder-plus"></i> Thêm danh mục
                                    </a>
                                    <a class="btn btn-add btn-sm" data-bs-toggle="modal" data-bs-target="#addPublisherModal">
                                        <i class="fas fa-folder-plus"></i> Thêm nhà xuất bản
                                    </a>
                                </div>

                                <!-- Mã sách (Nếu muốn tự nhập thì để cái này lại cho có cái nhập)-->
                                <!-- <div class="form-group col-md-3">
                                    <label class="control-label">Mã Sách</label>
                                    <input class="form-control" type="text" name="maSach" placeholder="Nhập Mã sách">
                                </div> -->

                                <!-- Tên sách -->
                                <div class="form-group col-md-3">
                                    <label class="control-label">Tên Sách</label>
                                    <input class="form-control" type="text" name="tenSach" placeholder="Nhập tên sách">
                                </div>

                                <!-- Số lượng -->
                                <div class="form-group col-md-3">
                                    <label class="control-label">Số lượng</label>
                                    <input class="form-control" type="number" name="soLuong" placeholder="Nhập số lượng">
                                </div>

                                
                               
                                
                                <div class="form-group col-md-3">
                                    <label class="control-label">Mô tả</label>
                                    <input class="form-control" type="text" name="moTa" placeholder="Nhập Mô Tả">
                                </div>
                                <div class="form-group col-md-3">
                                    <label class="control-label">Mô tả đầy đủ</label>
                                    <input class="form-control" type="text" name="moTaDayDu" placeholder="Nhập Mô Tả Đầy Đủ">
                                </div>
                                <!-- Danh mục -->
                                <div class="form-group col-md-3">
                                    <label class="control-label">Danh Mục</label>
                                    <select class="form-control" name="maLoai">
                                        <option>-- Chọn Danh Mục --</option>
                                        <?php
                                        // Lặp qua mảng loaisach để tạo các tùy chọn cho dropdown
                                        foreach ($loaisach as $row) {
                                            // Tạo một tùy chọn cho mỗi danh mục sách
                                            // "maLoai" là giá trị của tùy chọn, "maLoai - tenLoai" là văn bản hiển thị
                                            echo "<option value='" . $row['maLoai'] . "'>" . $row['maLoai'] . " - " . $row['tenLoai'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Nhà xuất bản -->
                                <div class="form-group col-md-3">
                                    <label class="control-label">Nhà Xuất Bản</label>
                                    <select class="form-control" name="maNXB">
                                        <option>-- Chọn Nhà Xuất Bản --</option>
                                        <?php
                                        foreach ($nhaxuatban as $rowNXB) {
                                            echo "<option value='" . $rowNXB['maNXB'] . "'>" . $rowNXB['tenNXB'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Giá bán -->
                                <div class="form-group col-md-3">
                                    <label class="control-label">Giá bán</label>
                                    <input class="form-control" type="text" name="giaBan" placeholder="Nhập giá bán">
                                </div>

                                <!-- Giá khuyến mãi -->
                                <div class="form-group col-md-3">
                                    <label class="control-label">Giá Khuyến Mãi</label>
                                    <input class="form-control" type="text" name="giaKhuyenMai" placeholder="Nhập giá khuyến mãi">
                                </div>

                                <!-- Tác giả -->
                                <div class="form-group col-md-3">
                                    <label class="control-label">Tác Giả</label>
                                    <select class="form-control" name="maTG">
                                        <option>-- Chọn Tác Giả --</option>
                                        <?php
                                        foreach ($tacgia as $rowTG) {
                                            echo "<option value='" . $rowTG['maTG'] . "'>" . $rowTG['tenTG'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Tải ảnh lên -->
                                <div class="form-group col-md-3">
                                    <label for="anh1">Ảnh sách:</label>
                                    <input type="file" name="anh1" accept="image/*" required><br>
                                </div>

                                <!-- Nút lưu -->
                                <div class="form-group col-md-3">
                                    <button class="btn btn-primary" type="submit">Lưu</button>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <!-- Modal thêm tác giả (Click vào sẽ hiện những đoạn này lên)  -->
    <div class="modal fade" id="addAuthorModal" tabindex="-1" aria-labelledby="addAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="process_forms.php" id="addAuthorForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAuthorModalLabel">Thêm Tác Giả</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- <div class="form-group">
                            <label for="maTG">Mã Tác Giả</label>
                            <input type="text" class="form-control" id="maTG" name="maTG" required>
                        </div> -->
                        <div class="form-group">
                            <label for="tenTG">Tên Tác Giả</label>
                            <input type="text" class="form-control" id="tenTG" name="tenTG" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="submitAuthor">Lưu</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal thêm danh mục(Click vào sẽ hiện những đoạn này lên) -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="process_forms.php" id="addCategoryForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryModalLabel">Thêm Danh Mục</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="maLoai">Mã Danh Mục</label>
                            <input type="text" class="form-control" id="maLoai" name="maLoai" required>
                        </div>
                        <div class="form-group">
                            <label for="tenLoai">Tên Danh Mục</label>
                            <input type="text" class="form-control" id="tenLoai" name="tenLoai" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="submitCategory">Lưu</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal thêm nhà xuất bản -->
    <div class="modal fade" id="addPublisherModal" tabindex="-1" aria-labelledby="addPublisherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="process_forms.php" id="addPublisherForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPublisherModalLabel">Thêm Nhà Xuất Bản</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- <div class="form-group">
                            <label for="maNXB">Mã Nhà Xuất Bản</label>
                            <input type="text" class="form-control" id="maNXB" name="maNXB" required>
                        </div> -->
                        <div class="form-group">
                            <label for="tenNXB">Tên Nhà Xuất Bản</label>
                            <input type="text" class="form-control" id="tenNXB" name="tenNXB" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="submitPublisher">Lưu</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/plugins/pace.min.js"></script>
</body>