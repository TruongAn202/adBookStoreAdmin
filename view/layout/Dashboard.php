<?php
session_start(); // Bắt đầu session để lưu trữ thông tin người dùng

// Include tệp kết nối database
include '../../model/connectdb.php';
$conn = connectdb(); // Kiểm tra kết nối
if ($conn === null) {
  echo "Không thể kết nối đến cơ sở dữ liệu.";
  exit();
}
function getTotalOutOfStockBooks($conn, $tinhTrang = 'hết hàng')
{
  // Chuẩn bị câu truy vấn để đếm số lượng sách có tình trạng 'hết hàng'
  $sql = "SELECT COUNT(*) AS count FROM SACH WHERE tinhTrang = :tinhTrang";

  // Chuẩn bị statement
  $stmt = $conn->prepare($sql);

  // Gán giá trị cho tham số :tinhTrang
  $stmt->bindParam(':tinhTrang', $tinhTrang, PDO::PARAM_STR);

  // Thực thi câu lệnh
  $stmt->execute();

  // Lấy kết quả trả về
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  // Trả về số lượng sách hết hàng
  return $result['count'];
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
function SoLuongKhachHang($conn, $vaiTro = 'user')
{
  $sql = "SELECT COUNT(*) AS count FROM roleadminuser WHERE vaiTro = :vaiTro";
  $stmt = $conn->prepare($sql);

  // Gán giá trị cho tham số :tinhTrang
  $stmt->bindParam(':vaiTro', $vaiTro, PDO::PARAM_STR);

  // Thực thi câu lệnh
  $stmt->execute();

  // Lấy kết quả trả về
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  // Trả về số lượng sách hết hàng
  return $result['count'];
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
function tinhTrangDH($conn)
{
  $sql = "SELECT 
              h.maHD, 
              h.tenNguoiNhan, 
              h.trangThaiHD,  
              c.donGia  
          FROM 
              hoadon h
          JOIN 
              chitiethoadon c ON h.maHD = c.maHD";
  $stmt = $conn->prepare($sql);

  // Thực thi câu lệnh
  $stmt->execute();

  // Lấy kết quả trả về
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  return $result;
}
function thongTinKhachHang($conn, $vaiTro = "user")
{
  $sql = "SELECT email,full_name,diaChi,soDienThoai FROM roleadminuser WHERE vaiTro = :vaiTro";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':vaiTro', $vaiTro, PDO::PARAM_STR);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $result;
}
$out_of_stock_count = getTotalOutOfStockBooks($conn);
$tongSP = TongSanPham($conn);
$tongKH = SoLuongKhachHang($conn);
$tongHD = soLuongDH($conn);
$result = tinhTrangDH($conn);
$thongTinKH = thongTinKhachHang($conn);
$conn = null;
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
      <li><a class="app-nav__item" href="/index.html"><i class='bx bx-log-out bx-rotate-180'></i> </a>

      </li>
    </ul>
  </header>
  <!-- Sidebar menu-->
  <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
  <aside class="app-sidebar">
    <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="/images/Admin.png" width="50px"
        alt="User Image">
      <div>
        <p class="app-sidebar__user-name"><b>Admin</b></p>
        <p class="app-sidebar__user-designation">Chào mừng bạn trở lại</p>
      </div>
    </div>
    <hr>
    <ul class="app-menu">
      <li><a class="app-menu__item active" href="Dashboard.php"><i class='app-menu__icon bx bx-tachometer'></i><span
            class="app-menu__label">Bảng điều khiển</span></a></li>

      <li><a class="app-menu__item " href="quanlisanpham.php"><i
            class='app-menu__icon bx bx-purchase-tag-alt'></i><span class="app-menu__label">Quản lý sản phẩm</span></a>
      </li>
      <li><a class="app-menu__item" href="donhang.php"><i class='app-menu__icon bx bx-task'></i><span
            class="app-menu__label">Quản lý đơn hàng</span></a></li>
      </li>
      <li><a class="app-menu__item" href="doanh-thu.php"><i class='app-menu__icon bx bx-pie-chart-alt-2'></i><span class="app-menu__label">Báo cáo doanh thu</span></a></li>
      <li><a class="app-menu__item " href="quanliTaiKhoan.php"><i class='app-menu__icon bx bx-id-card'></i>
          <span class="app-menu__label">Quản lý Tài Khoản</span></a></li>
    </ul>
  </aside>
  <main class="app-content">
    <div class="row">
      <div class="col-md-12">
        <div class="app-title">
          <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><a href="#"><b>Bảng điều khiển</b></a></li>
          </ul>
          <div id="clock"></div>
        </div>
      </div>
    </div>
    <div class="row">
      <!--Left-->
      <div class="col-md-12 col-lg-6">
        <div class="row">
          <!-- col-6 -->
          <div class="col-md-6">
            <div class="widget-small primary coloured-icon"><i class='icon bx bxs-user-account fa-3x'></i>
              <div class="info">
                <h4>Tổng khách hàng</h4>
                <p><b><?php echo $tongKH; ?> Khách Hàng</b></p>
                <p class="info-tong">Tổng số khách hàng được quản lý.</p>
              </div>
            </div>
          </div>
          <!-- col-6 -->
          <div class="col-md-6">
            <div class="widget-small info coloured-icon"><i class='icon bx bxs-data fa-3x'></i>
              <div class="info">
                <h4>Tổng sản phẩm</h4>
                <p><b><?php echo $tongSP; ?> sản phẩm</b></p>
                <p class="info-tong">Tổng số sản phẩm được quản lý.</p>
              </div>
            </div>
          </div>
          <!-- col-6 -->
          <div class="col-md-6">
            <div class="widget-small warning coloured-icon"><i class='icon bx bxs-shopping-bags fa-3x'></i>
              <div class="info">
                <h4>Tổng đơn hàng</h4>
                <p><b><?php echo $tongHD; ?> Đơn Hàng</b></p>
                <p class="info-tong">Tổng số hóa đơn bán hàng trong tháng.</p>
              </div>
            </div>
          </div>
          <!-- col-6 -->
          <div class="col-md-6">
            <div class="widget-small danger coloured-icon"><i class='icon bx bxs-error-alt fa-3x'></i>
              <div class="info">
                <h4>Sắp hết hàng</h4>
                <p><b><?php echo $out_of_stock_count; ?> sản phẩm</b></p>
                <p class="info-tong">Số sản phẩm cảnh báo hết cần nhập thêm.</p>
              </div>
            </div>
          </div>
          <!-- col-12 -->
          <div class="col-md-12">
            <div class="tile">
              <h3 class="tile-title">Tình trạng đơn hàng</h3>
              <div>
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Mã Hóa Đơn</th>
                      <th>Tên khách hàng</th>
                      <th>Tổng tiền</th>
                      <th>Trạng thái</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($result as $row): ?>
                      <tr>
                        <td><?php echo $row['maHD']; ?></td>
                        <td><?php echo $row['tenNguoiNhan']; ?></td>
                        <td><?php echo number_format($row['donGia'], 0, ',', '.'); ?> VND</td>
                        <td><?php echo $row['trangThaiHD']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <!-- / div trống-->
            </div>
          </div>
          <!-- / col-12 -->
          <!-- col-12 -->
          <div class="col-md-12">
            <div class="tile">
              <h3 class="tile-title">Khách hàng mới</h3>
              <div>
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Email Khách Hàng</th>
                      <th>Tên khách hàng</th>
                      <th>Địa Chỉ</th>
                      <th>Số điện thoại</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($thongTinKH as $row): ?>
                      <tr>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['diaChi']; ?></td>
                        <td><?php echo $row['soDienThoai']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--END left-->
      <!-- / col-12 -->
      <div class="col-md-12 col-lg-6">
          <div class="row">
            <div class="col-md-12">
              <div class="tile">
                <h3 class="tile-title">Dữ liệu 6 tháng đầu vào</h3>
                <div class="embed-responsive embed-responsive-16by9">
                  <canvas class="embed-responsive-item" id="lineChartDemo"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div class="tile">
                <h3 class="tile-title">Thống kê 6 tháng doanh thu</h3>
                <div class="embed-responsive embed-responsive-16by9">
                  <canvas class="embed-responsive-item" id="barChartDemo"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      <!--Right-->
  </main>
  <script src="js/jquery-3.2.1.min.js"></script>
  <!--===============================================================================================-->
  <script src="js/popper.min.js"></script>
  <script src="https://unpkg.com/boxicons@latest/dist/boxicons.js"></script>
  <!--===============================================================================================-->
  <script src="js/bootstrap.min.js"></script>
  <!--===============================================================================================-->
  <script src="js/main.js"></script>
  <!--===============================================================================================-->
  <script src="js/plugins/pace.min.js"></script>
  <!--===============================================================================================-->
  <script type="text/javascript" src="js/plugins/chart.js"></script>
  <!--===============================================================================================-->
  <script type="text/javascript">
    var data = {
      labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6"],
      datasets: [{
          label: "Dữ liệu đầu tiên",
          fillColor: "rgba(255, 213, 59, 0.767), 212, 59)",
          strokeColor: "rgb(255, 212, 59)",
          pointColor: "rgb(255, 212, 59)",
          pointStrokeColor: "rgb(255, 212, 59)",
          pointHighlightFill: "rgb(255, 212, 59)",
          pointHighlightStroke: "rgb(255, 212, 59)",
          data: [20, 59, 90, 51, 56, 100]
        },
        {
          label: "Dữ liệu kế tiếp",
          fillColor: "rgba(9, 109, 239, 0.651)  ",
          pointColor: "rgb(9, 109, 239)",
          strokeColor: "rgb(9, 109, 239)",
          pointStrokeColor: "rgb(9, 109, 239)",
          pointHighlightFill: "rgb(9, 109, 239)",
          pointHighlightStroke: "rgb(9, 109, 239)",
          data: [48, 48, 49, 39, 86, 10]
        }
      ]
    };
    var ctxl = $("#lineChartDemo").get(0).getContext("2d");
    var lineChart = new Chart(ctxl).Line(data);

    var ctxb = $("#barChartDemo").get(0).getContext("2d");
    var barChart = new Chart(ctxb).Bar(data);
  </script>
</body>