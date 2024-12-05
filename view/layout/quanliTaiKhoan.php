  <?php
  session_start(); // Bắt đầu session để lưu trữ thông tin người dùng

  // Include tệp kết nối database
  include '../../model/connectdb.php';
  $conn = connectdb(); // Kiểm tra kết nối
  if ($conn === null) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
  }


  // Truy vấn lấy dữ liệu từ bảng roleadminuser
  $sql = "SELECT email, username, password, full_name, diaChi, soDienThoai, trangThai,vaiTro FROM roleadminuser";
  $stmt = $conn->prepare($sql);
  $stmt->execute();

  // Lấy tất cả dữ liệu
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  ?>




  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title>Danh sách Tài Khoản | Quản trị Admin</title>
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
      <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="images/Admin.png" width="50px"
          alt="User Image">
        <div>
          <p class="app-sidebar__user-name"><b>Dũng</b></p>
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
        <li><a class="app-menu__item" href="doanh-thu.php"><i class='app-menu__icon bx bx-pie-chart-alt-2'></i><span class="app-menu__label">Báo cáo doanh thu</span></a></li>
        <li><a class="app-menu__item active" href="quanliTaiKhoan.php"><i class='app-menu__icon bx bx-id-card'></i>
            <span class="app-menu__label">Quản lý Tài Khoản</span></a></li>

      </ul>
    </aside>
    <main class="app-content">
      <div class="app-title">
        <ul class="app-breadcrumb breadcrumb side">
          <li class="breadcrumb-item active"><a href="#"><b>Danh sách Tài Khoản</b></a></li>
        </ul>
        <div id="clock"></div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <table class="table table-hover table-bordered js-copytextarea" cellpadding="0" cellspacing="0" border="0" id="sampleTable">
                <thead>
                  <tr>
                    <th width="10"><input type="checkbox" id="all"></th>
                    <th>Email</th>
                    <th width="150">UserName</th>
                    <th width="20">Password</th>
                    <th width="300">Full-Name</th>
                    <th>Địa chỉ</th>
                    <th>Số Điện Thoại</th>
                    <th>Trạng Thái</th>
                    <th>Vai Trò</th>
                    <th width="100">Tính năng</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  // Duyệt qua tất cả dữ liệu lấy từ database và hiển thị
                  foreach ($users as $user) {
                    echo '<tr>';
                    echo '<td><input type="checkbox"></td>';
                    echo '<td>' . htmlspecialchars($user['email'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($user['username'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($user['password'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($user['full_name'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($user['diaChi'] ?? '') . '</td>';
                    echo '<td>' . htmlspecialchars($user['soDienThoai'] ?? '') . '</td>';

                    // Kiểm tra và thay thế null hoặc không có giá trị cho 'trangThai' và 'vaiTro'
                    $trangThai = $user['trangThai'] ?? 'Chưa cập nhật';  // Nếu null thì hiển thị 'Chưa cập nhật'
                    $vaiTro = $user['vaiTro'] ?? 'Chưa có quyền';  // Nếu null thì hiển thị 'Chưa có quyền'

                    echo '<td>' . htmlspecialchars($trangThai) . '</td>';
                    echo '<td>' . htmlspecialchars($vaiTro) . '</td>';

                    echo '<td>
                        <button class="btn btn-primary" onclick="viewDetails(\'' . htmlspecialchars($user['email'] ?? '') . '\')">Cập nhật Vai Trò</button>
                        <button class="btn btn-success" onclick="setStatus(\'' . htmlspecialchars($user['email'] ?? '') . '\')">Cập nhật Trạng thái</button>
                      </td>';

                    echo '</tr>';
                  }
                  ?>
                </tbody>

              </table>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!--
    MODAL
  -->

    <!--
    MODAL
  -->

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
      $('#sampleTable').DataTable();
    </script>
    <script>
      oTable = $('#sampleTable').dataTable();
      $('#all').click(function(e) {
        $('#sampleTable tbody :checkbox').prop('checked', $(this).is(':checked'));
        e.stopImmediatePropagation();
      });

      // Hàm cập nhật vai trò người dùng
      function viewDetails(email) {
        // Gửi yêu cầu AJAX
        $.ajax({
          url: 'update_role.php', // Tệp PHP xử lý cập nhật
          type: 'POST',
          data: {
            email: email
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

      function setStatus(email) {
        // Gửi yêu cầu AJAX
        $.ajax({
          url: 'update-trangThai.php', // Tệp PHP xử lý cập nhật
          type: 'POST',
          data: {
            email: email
          }, // Gửi email người dùng
          success: function(response) {
            alert(response); // Hiển thị thông báo
            location.reload(); // Tải lại trang để cập nhật lại danh sách
          },
          error: function() {
            alert('Có lỗi xảy ra khi cập nhật Trạng Thái.');
          }
        });
      }

      //Thời Gian
      function time() {
        var today = new Date();
        var weekday = new Array(7);
        weekday[0] = "Chủ Nhật";
        weekday[1] = "Thứ Hai";
        weekday[2] = "Thứ Ba";
        weekday[3] = "Thứ Tư";
        weekday[4] = "Thứ Năm";
        weekday[5] = "Thứ Sáu";
        weekday[6] = "Thứ Bảy";
        var day = weekday[today.getDay()];
        var dd = today.getDate();
        var mm = today.getMonth() + 1;
        var yyyy = today.getFullYear();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);
        nowTime = h + " giờ " + m + " phút " + s + " giây";
        if (dd < 10) {
          dd = '0' + dd
        }
        if (mm < 10) {
          mm = '0' + mm
        }
        today = day + ', ' + dd + '/' + mm + '/' + yyyy;
        tmp = '<span class="date"> ' + today + ' - ' + nowTime +
          '</span>';
        document.getElementById("clock").innerHTML = tmp;
        clocktime = setTimeout("time()", "1000", "Javascript");

        function checkTime(i) {
          if (i < 10) {
            i = "0" + i;
          }
          return i;
        }
      }

      //Modal
      $("#show-emp").on("click", function() {
        $("#ModalUP").modal({
          backdrop: false,
          keyboard: false
        })
      });
    </script>
  </body>

  </html>