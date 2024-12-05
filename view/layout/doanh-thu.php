<?php
session_start(); // Bắt đầu session để lưu trữ thông tin người dùng

// Include tệp kết nối database
include '../../model/connectdb.php';
$conn = connectdb(); // Kiểm tra kết nối
if ($conn === null) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
}
function soLuongDH($conn)
{
    $sql = "SELECT COUNT(*) AS count1 FROM hoadon";

    // Chuẩn bị statement
    $stmt = $conn->prepare($sql);

    // Thực thi câu lệnh
    $stmt->execute();

    // Lấy kết quả trả về
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Trả về tổng số sách có trong cơ sở dữ liệu
    return $result['count1'];
}
function TongSanPham($conn)
{
    // Câu truy vấn để đếm tổng số sách
    $sql = "SELECT COUNT(*) AS count1 FROM SACH";

    // Chuẩn bị statement
    $stmt = $conn->prepare($sql);

    // Thực thi câu lệnh
    $stmt->execute();

    // Lấy kết quả trả về
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Trả về tổng số sách có trong cơ sở dữ liệu
    return $result['count1'];
}
function getTotalOutOfStockBooks($conn, $tinhTrang = 'hết hàng')
{
    // Chuẩn bị câu truy vấn để đếm số lượng sách có tình trạng 'hết hàng'
    $sql = "SELECT COUNT(*) AS count FROM SACH WHERE TinhTrang = :TinhTrang";

    // Chuẩn bị statement
    $stmt = $conn->prepare($sql);

    // Gán giá trị cho tham số :tinhTrang
    $stmt->bindParam(':TinhTrang', $tinhTrang, PDO::PARAM_STR);

    // Thực thi câu lệnh
    $stmt->execute();

    // Lấy kết quả trả về
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Trả về số lượng sách hết hàng
    return $result['count'];
}
function tongThuNhap($conn)
{
    $sql = "SELECT SUM(donGia) AS tongGia
            FROM chitiethoadon c 
            JOIN hoadon h ON c.maHD = h.maHD  
            WHERE trangThaiHD LIKE 'dagiao'";  // Fixed the quote here
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['tongGia'];
}
function getProductsData($conn)
{
    // Câu truy vấn SQL để lấy thông tin từ bảng sach và chitiethoadon
    $sql = "SELECT s.maSach, s.tenSach, ls.tenLoai, c.donGia
            FROM loaisach ls
            JOIN sach s on ls.maLoai=s.maLoai
            JOIN chitiethoadon c ON s.masach = c.masach";

    // Chuẩn bị và thực thi câu truy vấn
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Lấy tất cả kết quả
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về kết quả
    return $results;
}
function tongDH($conn)
{
    $sql = "SELECT c.maHD,hd.tenNguoiNhan,s.tenSach,c.SoLuong,c.donGia FROM hoadon hd
            JOIN chitiethoadon c on hd.maHD=c.maHD
            JOIN sach s on c.maSach=s.maSach";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Lấy tất cả kết quả
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về kết quả
    return $results;
}
function thongTinSPHetHang($conn, $tinhTrang = 'hết hàng')
{

    $sql = "SELECT maSach,tenSach,anh,SoLuong,TinhTrang,gia,ls.tenLoai
             FROM SACH
             JOIN loaisach ls on Sach.maLoai=ls.maLoai
              WHERE TinhTrang = :TinhTrang";

    // Chuẩn bị statement
    $stmt = $conn->prepare($sql);

    // Gán giá trị cho tham số :tinhTrang
    $stmt->bindParam(':TinhTrang', $tinhTrang, PDO::PARAM_STR);

    // Thực thi câu lệnh
    $stmt->execute();

    // Lấy kết quả trả về
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Trả về tất cả thông tin sản phẩm hết hàng
    return $result;
}
function donHangHuy($conn,$trangThaiHD = 'dahuy')
{
    $sql= "SELECT count(*) as count FROM hoadon where trangThaiHD = :trangThaiHD";
    $stmt = $conn->prepare($sql);

    // Gán giá trị cho tham số :tinhTrang
    $stmt->bindParam(':trangThaiHD', $trangThaiHD, PDO::PARAM_STR);

    // Thực thi câu lệnh
    $stmt->execute();

    // Lấy kết quả trả về
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Trả về số lượng sách hết hàng
    return $result['count'];
}

$out_of_stock_count = getTotalOutOfStockBooks($conn);
$tongSP = TongSanPham($conn);
$tongHD = soLuongDH($conn);
$tongTN = tongThuNhap($conn);
$tongSPBChay = getProductsData($conn);
$tongDH = tongDH($conn);
$tongSPHH = thongTinSPHetHang($conn);
$dhHuy=donHangHuy($conn);
?>








<!DOCTYPE html>
<html lang="en">

<head>
    <title>Danh sách nhân viên | Quản trị Admin</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="assets/css/main.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- or -->
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css"
        href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">

</head>

<body onload="time()" class="app sidebar-mini rtl">
    <!-- Navbar-->
    <header class="app-header">
        <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar"
            aria-label="Hide Sidebar"></a>
        <!-- Navbar Right Menu-->
        <ul class="app-nav">
            <!-- User Menu-->
            <li><a class="app-nav__item" href="Dashboard.php"><i class='bx bx-log-out bx-rotate-180'></i> </a>
            </li>
        </ul>
    </header>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
        <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="images/Admin.png" width="50px"
                alt="User Image">
            <div>
                <p class="app-sidebar__user-name"><b>Admin</b></p>
                <p class="app-sidebar__user-designation">Chào mừng bạn trở lại</p>
            </div>
        </div>
        <hr>
        <ul class="app-menu">
            <li><a class="app-menu__item " href="Dashboard.php"><i class='app-menu__icon bx bx-tachometer'></i><span
                        class="app-menu__label">Bảng điều khiển</span></a></li>

            <li><a class="app-menu__item" href="quanlisanpham.php"><i
                        class='app-menu__icon bx bx-purchase-tag-alt'></i><span class="app-menu__label">Quản lý sản phẩm</span></a>
            </li>
            <li><a class="app-menu__item" href="donhang.php"><i class='app-menu__icon bx bx-task'></i><span
                        class="app-menu__label">Quản lý đơn hàng</span></a></li>
            </li>
            <li><a class="app-menu__item  active" href="doanh-thu.php"><i class='app-menu__icon bx bx-pie-chart-alt-2'></i><span class="app-menu__label">Báo cáo doanh thu</span></a></li>
            <li><a class="app-menu__item " href="quanliTaiKhoan.php"><i class='app-menu__icon bx bx-id-card'></i>
                    <span class="app-menu__label">Quản lý Tài Khoản</span></a></li>

        </ul>
    </aside>
    <main class="app-content">
        <div class="row">
            <div class="col-md-12">
                <div class="app-title">
                    <ul class="app-breadcrumb breadcrumb">
                        <li class="breadcrumb-item"><a href="#"><b>Báo cáo doanh thu </b></a></li>
                    </ul>
                    <div id="clock"></div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="widget-small info coloured-icon"><i class='icon bx bxs-purchase-tag-alt fa-3x'></i>
                    <div class="info">
                        <h4>Tổng sản phẩm</h4>
                        <p><b><?php echo $tongSP; ?> Sản Phẩm</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="widget-small warning coloured-icon"><i class='icon fa-3x bx bxs-shopping-bag-alt'></i>
                    <div class="info">
                        <h4>Tổng đơn hàng</h4>
                        <p><b><?php echo $tongHD; ?> Đơn Hàng</b></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="widget-small primary coloured-icon"><i class='icon fa-3x bx bxs-chart'></i>
                    <div class="info">
                        <h4>Tổng thu nhập</h4>
                        <p><b><?php echo $tongTN; ?> VNĐ</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="widget-small warning coloured-icon"><i class='icon fa-3x bx bxs-tag-x'></i>
                    <div class="info">
                        <h4>Hết hàng</h4>
                        <p><b><?php echo $out_of_stock_count; ?> sản phẩm</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="widget-small danger coloured-icon"><i class='icon fa-3x bx bxs-receipt'></i>
                    <div class="info">
                        <h4>Đơn hàng hủy</h4>
                        <p><b><?php echo $dhHuy; ?> đơn hàng</b></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div>
                        <h3 class="tile-title">SẢN PHẨM BÁN CHẠY</h3>
                    </div>
                    <div class="tile-body">
                        <table class="table table-hover table-bordered" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>Mã sản phẩm</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá tiền</th>
                                    <th>Danh mục</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tongSPBChay as $row): ?>
                                    <tr>
                                        <td><?php echo $row['maSach']; ?></td>
                                        <td><?php echo $row['tenSach']; ?></td>
                                        <td><?php echo $row['donGia']; ?></td>
                                        <td><?php echo $row['tenLoai']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div>
                        <h3 class="tile-title">TỔNG ĐƠN HÀNG</h3>
                    </div>
                    <div class="tile-body">
                        <table class="table table-hover table-bordered" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>Mã Đơn Hàng</th>
                                    <th>Khách hàng</th>
                                    <th>Đơn hàng</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tongDH as $row): ?>
                                    <tr>
                                        <td><?php echo $row['maHD']; ?></td>
                                        <td><?php echo $row['tenNguoiNhan']; ?></td>
                                        <td><?php echo $row['tenSach']; ?></td>
                                        <td><?php echo $row['SoLuong']; ?></td>
                                        <td><?php echo $row['donGia']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div>
                        <h3 class="tile-title">SẢN PHẨM ĐÃ HẾT</h3>
                    </div>
                    <div class="tile-body">
                        <table class="table table-hover table-bordered" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>Mã sản phẩm</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Ảnh</th>
                                    <th>Số lượng</th>
                                    <th>Tình trạng</th>
                                    <th>Giá tiền</th>
                                    <th>Danh mục</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tongSPHH as $row): ?>
                                    <tr>
                                        <td><?php echo $row['maSach']; ?></td>
                                        <td><?php echo $row['tenSach']; ?></td>
                                        <td><?php echo $row['anh']; ?></td>
                                        <td><?php echo $row['SoLuong']; ?></td>
                                        <td><?php echo $row['TinhTrang']; ?></td>
                                        <td><?php echo $row['gia']; ?></td>
                                        <td><?php echo $row['tenLoai']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="js/plugins/pace.min.js"></script>
    <!-- Page specific javascripts-->
    <script type="text/javascript" src="js/plugins/chart.js"></script>


</body>