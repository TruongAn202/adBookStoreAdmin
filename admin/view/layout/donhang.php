<?php
include '../../model/connectdb.php';
$sql = "SELECT 
            h.maHD, 
            h.tenNguoiNhan, 
            h.diaChiNguoiNhan, 
            h.soDienThoaiHD, 
            h.ngayLapHD, 
            h.ngayNhan, 
            h.trangThaiHD, 
            h.phuongThucThanhToan, 
            c.soLuong, 
            c.donGia,
            s.tenSach,
            h.phuongThucGiaoHang  
        FROM 
            hoadon h
        JOIN 
              chitiethoadon c ON h.maHD = c.maHD
        JOIN 
            sach s ON c.maSach = s.maSach";
$conn = connectdb(); // Kiểm tra kết nối
if ($conn === null) {
  echo "Không thể kết nối đến cơ sở dữ liệu.";
  exit();
}
function searchInvoices($conn, $searchTerm) //Tìm Kiếm
{
  $sql = "SELECT 
              h.maHD, 
              h.tenNguoiNhan, 
              h.diaChiNguoiNhan, 
              h.soDienThoaiHD, 
              h.ngayLapHD, 
              h.ngayNhan, 
              h.trangThaiHD, 
              h.phuongThucThanhToan, 
              c.soLuong, 
              c.donGia,
              s.tenSach
              h.phuongThucGiaoHang  
          FROM 
              hoadon h
          JOIN 
              chitiethoadon c ON h.maHD = c.maHD
          JOIN 
              sach s ON c.maSach = s.maSach
          WHERE 
              h.maHD LIKE :searchTerm"; // Sửa đây để tìm kiếm theo maHD

  $stmt = $conn->prepare($sql);
  $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%'); // Áp dụng từ khóa tìm kiếm
  $stmt->execute();
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$searchTerm = '';
if (isset($_GET['search'])) {
  // Xử lý bảo mật và tránh SQL injection
  $searchTerm = htmlspecialchars($_GET['search']);
}

// Nếu có từ khóa tìm kiếm, thực hiện tìm kiếm theo mã hóa đơn
if ($searchTerm) {
  $result = searchInvoices($conn, $searchTerm);
} else {
  // Nếu không có từ khóa tìm kiếm, lấy tất cả hóa đơn
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Danh sách đơn hàng | Quản trị Admin</title>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Main CSS-->
  <link rel="stylesheet" type="text/css" href="assets/css1/main.css">
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
      <li><a class="app-nav__item" href="#"><i class='bx bx-log-out bx-rotate-180'></i> </a>
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
      <li><a class="app-menu__item " href="bangDK.php"><i class='app-menu__icon bx bx-tachometer'></i><span
            class="app-menu__label">Bảng điều khiển</span></a></li>

      <li><a class="app-menu__item" href="quanlisanpham.php"><i
            class='app-menu__icon bx bx-purchase-tag-alt'></i><span class="app-menu__label">Quản lý sản phẩm</span></a>
      </li>
      <li><a class="app-menu__item active" href="donhang.php"><i class='app-menu__icon bx bx-task'></i><span
            class="app-menu__label">Quản lý đơn hàng</span></a></li>
      </li>
      <li><a class="app-menu__item" href="doanh-thu.php"><i class='app-menu__icon bx bx-pie-chart-alt-2'></i><span class="app-menu__label">Báo cáo doanh thu</span></a></li>
      <li><a class="app-menu__item " href="quanliTaiKhoan.php"><i class='app-menu__icon bx bx-id-card'></i>
          <span class="app-menu__label">Quản lý Tài Khoản</span></a></li>

    </ul>
  </aside>
  <main class="app-content">
    <div class="app-title">
      <ul class="app-breadcrumb breadcrumb side">
        <li class="breadcrumb-item active"><a href="#"><b>Danh sách đơn hàng</b></a></li>
      </ul>
      <div id="clock"></div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="tile">
          <div class="tile-body">
            <input type="text" id="searchInput" placeholder="Tìm kiếm Hóa Đơn..." onkeyup="searchBooks()">
            <table class="table table-hover table-bordered" id="sampleTable">
              <thead>
                <tr>
                  <th>Mã Đơn Hàng</th>
                  <th>Tên Khách hàng</th>
                  <th>Đơn hàng</th>
                  <th>Số lượng</th>
                  <th>Đơn Giá</th>
                  <th>Địa Chỉ Nhận</th>
                  <th>Số Điện Thoại </th>
                  <th>Ngày Lập Hóa Đơn</th>
                  <th>Ngày Nhận</th>
                  <th>Trạng Thái Đơn Hàng</th>
                  <th>Phương thức thanh toán</th>
                  <th>Phương thức Giao Hàng</th>
                  <th>Tính năng</th>
                </tr>
              </thead>
              <tbody id="tableResults">
                <?php foreach ($result as $result1): ?>
                  <tr>
                    <td><?php echo $result1['maHD']; ?></td>
                    <td><?php echo $result1['tenNguoiNhan']; ?></td>
                    <td><?php echo $result1['tenSach']; ?></td>
                    <td><?php echo $result1['soLuong']; ?></td>
                    <td><?php echo $result1['donGia']; ?></td>
                    <td><?php echo $result1['diaChiNguoiNhan']; ?></td>
                    <td><?php echo $result1['soDienThoaiHD']; ?></td>
                    <td><?php echo $result1['ngayLapHD']; ?></td>
                    <td><?php echo isset($result1['ngayNhan']) && !empty($result1['ngayNhan']) ? $result1['ngayNhan'] : ''; ?></td>
                    <<td>
                      <?php
                      // Mảng ánh xạ trạng thái
                      $statusMap = [
                        'choxuly' => 'Chờ Xử Lý',
                        'danggiao' => 'Đang Giao',
                        'dagiao' => 'Đã Giao',
                        'dahuy' => 'Đã Hủy'
                      ];

                      // Lấy trạng thái từ cơ sở dữ liệu
                      $trangThai = $result1['trangThaiHD'];

                      // Kiểm tra và hiển thị trạng thái thân thiện
                      echo isset($statusMap[$trangThai]) ? $statusMap[$trangThai] : 'Trạng thái không xác định';
                      ?>
                      </td>
                      <td><?php echo $result1['phuongThucThanhToan']; ?></td>
                      <td><?php echo $result1['phuongThucGiaoHang']; ?></td>
                      <td>
                        <button class="btn btn-danger" onclick="updateTrangThaiDH('<?php echo $result1['maHD']; ?>')">
                          Cập Nhật Trạng Thái Đơn Hàng
                        </button>
                      </td>

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
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="src/jquery.table2excel.js"></script>
  <script src="js/main.js"></script>
  <!-- The javascript plugin to display page loading on top-->
  <script src="js/plugins/pace.min.js"></script>
  <!-- Page specific javascripts-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
  <!-- Data table plugin-->
  <script type="text/javascript" src="js/plugins/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="js/plugins/dataTables.bootstrap.min.js"></script>
  <script type="text/javascript">
    oTable = $('#sampleTable').DataTable({
      searching: false // Tắt tính năng tìm kiếm
    });

    function searchBooks() {
      const query = document.getElementById('searchInput').value.trim().toLowerCase(); // Lấy giá trị tìm kiếm và chuyển thành chữ thường
      console.log("Tìm kiếm: " + query);

      if (query !== "") {
        const rows = document.querySelectorAll('#tableResults tr');
        rows.forEach(row => {
          const titleCell = row.querySelector('td:nth-child(1)'); // Kiểm tra cột tên sách (cột thứ 3)
          if (titleCell) {
            const title = titleCell.textContent.trim().toLowerCase();
            console.log("Tên sách: " + title); // Kiểm tra tên sách của mỗi dòng

            if (title.includes(query)) {
              row.style.display = ''; // Hiển thị nếu tên sách khớp
            } else {
              row.style.display = 'none'; // Ẩn nếu không khớp
            }
          }
        });
      } else {
        // Nếu ô tìm kiếm trống, hiển thị tất cả các hàng
        const rows = document.querySelectorAll('#tableResults tr');
        rows.forEach(row => row.style.display = '');
      }
    }
    $('#all').click(function(e) {
      $('#sampleTable tbody :checkbox').prop('checked', $(this).is(':checked'));
      e.stopImmediatePropagation();
    });

    function updateTrangThaiDH(maHD) {
      // Gửi yêu cầu AJAX
      $.ajax({
        url: 'update_TrangThaiDH.php', // Tệp PHP xử lý cập nhật
        type: 'POST',
        data: {
          maHD: maHD
        }, // Gửi email người dùng
        success: function(response) {
          alert(response); // Hiển thị thông báo
          location.reload(); // Tải lại trang để cập nhật lại danh sách
        },
        error: function() {
          alert('Có lỗi xảy ra khi cập nhật vai trò.');
        }
      });
    }
  </script>

</body>