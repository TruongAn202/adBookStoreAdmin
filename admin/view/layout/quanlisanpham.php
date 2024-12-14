<?php
session_start(); // Bắt đầu session để lưu trữ thông tin người dùng

// Include tệp kết nối database
include '../../model/connectdb.php';
$conn = connectdb(); // Kiểm tra kết nối
if ($conn === null) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
}
// Hàm lấy tất cả sách
function getBooks($conn)
{
    $sql = "SELECT maSach,tenSach,loaisach.tenLoai,giaKM,anh,SoLuong,TinhTrang,moTa FROM sach join loaisach on sach.maLoai=loaisach.maLoai";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function updateAndGetBookStatus($conn, $maSach, $soLuong)
{
    // Xác định tình trạng dựa trên số lượng
    $tinhTrang = ($soLuong > 0) ? 'Còn hàng' : 'Hết hàng';

    // Cập nhật tình trạng vào cơ sở dữ liệu
    $sql = "UPDATE sach SET TinhTrang = :tinhTrang WHERE maSach = :maSach";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':tinhTrang', $tinhTrang);
    $stmt->bindParam(':maSach', $maSach);
    $stmt->execute();

    // Trả về tình trạng
    return $tinhTrang;
}


// Hàm tìm kiếm sách
function searchBooks($conn, $searchTerm)
{
    $sql = "SELECT * FROM Sach WHERE tenSach LIKE :searchTerm";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':searchTerm', '%' . $searchTerm . '%');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Kiểm tra nếu có từ khóa tìm kiếm
$searchTerm = '';
if (isset($_GET['search'])) {
    // Xử lý bảo mật và tránh SQL injection
    $searchTerm = htmlspecialchars($_GET['search']);
}

// Kiểm tra và lấy kết quả tìm kiếm hoặc tất cả sách
if ($searchTerm) {
    // Tìm kiếm sách theo từ khóa
    $result = searchBooks($conn, $searchTerm);
} else {
    // Lấy tất cả sách
    $result = getBooks($conn);
}
$result = getBooks($conn);
if (isset($_SESSION['success_message'])) {
    // Hiển thị thông báo
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";

    // Xóa thông báo sau khi hiển thị để tránh hiển thị lại trong lần tải trang tiếp theo
    unset($_SESSION['success_message']);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <title>Danh sách nhân viên | Quản trị Admin</title>
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
            <li><a class="app-nav__item" href="/adBookStoreUser/admin/index.php"><i class='bx bx-log-out bx-rotate-180'></i> </a>
            </li>
        </ul>
    </header>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
        <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="assets/images/Admin.png" width="50px"
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

            <li><a class="app-menu__item active" href="quanlisanpham.php"><i
                        class='app-menu__icon bx bx-purchase-tag-alt'></i><span class="app-menu__label">Quản lý sản phẩm</span></a>
            </li>
            <li><a class="app-menu__item " href="donhang.php"><i class='app-menu__icon bx bx-task'></i><span
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
                <li class="breadcrumb-item active"><a href="#"><b>Danh sách sản phẩm</b></a></li>
            </ul>
            <div id="clock"></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="tile-body">
                        <!-- Phần nút tạo mới sản phẩm -->
                        <div class="row element-button">
                            <div class="col-sm-2">
                                <a class="btn btn-add btn-sm" href="form-add-sp.php" title="Thêm">
                                    <i class="fas fa-plus"></i> Tạo mới sản phẩm
                                </a>
                            </div>
                        </div>

                        <!-- Phần input tìm kiếm -->
                        <input type="text" id="searchInput" placeholder="Tìm kiếm sách..." onkeyup="searchBooks()">

                        <!-- Bảng danh sách sản phẩm -->
                        <table class="table table-hover table-bordered" id="sampleTable">
                            <thead>
                                <tr>
                                    <th>Mã Sách</th>
                                    <th>Tên Sách</th>
                                    <th>Giá</th>
                                    <th>Ảnh</th>
                                    <th>Số Lượng</th>
                                    <th>Tình Trạng</th>
                                    <th>Mô Tả</th>
                                    <th>Danh Mục</th>
                                    <th>Chức năng</th>
                                </tr>
                            </thead>
                            <tbody id="tableResults">
                                <?php if (count($result) > 0): ?>
                                    <?php foreach ($result as $row): ?>
                                        <tr>
                                            <td><?php echo $row['maSach']; ?></td>
                                            <td><?php echo $row['tenSach']; ?></td>
                                            <td><?php echo number_format($row['giaKM'], 0, ',', '.'); ?> VND</td>
                                            <td>
                                                <?php
                                                // Đường dẫn đến ảnh trong các thư mục
                                                $image_path_1 = "assets/img-sanpham/" . $row['anh'];
                                                // Kiểm tra ảnh trong thư mục img-sanpham/
                                                if (file_exists($image_path_1)) {
                                                    echo '<img src="' . $image_path_1 . '" alt="Ảnh sách" width="150px">';
                                                }
                                                // Nếu ảnh không tồn tại, hiển thị thông báo lỗi hoặc ảnh mặc định
                                                else {
                                                    echo "Ảnh không tồn tại: " . $row['anh'];
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo $row['SoLuong']; ?></td>
                                            <td>
                                                <?php
                                                // Gọi hàm để kiểm tra và cập nhật tình trạng
                                                $tinhTrang = updateAndGetBookStatus($conn, $row['maSach'], $row['SoLuong']);

                                                // Hiển thị tình trạng
                                                if ($tinhTrang == 'Hết hàng'): ?>
                                                    <span class="badge bg-danger">Hết hàng</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Còn hàng</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $row['moTa']; ?></td>
                                            <td><?php echo $row['tenLoai']; ?></td>
                                            
                                           
                                            <td>
                                                <a href="delete.php?id=<?php echo $row['maSach']; ?>"
                                                    class="btn btn-primary btn-sm trash" title="Xóa"
                                                    onclick="return confirm('Bạn có chắc chắn muốn xóa sách này không?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                                <a href="edit.php?id=<?php echo $row['maSach']; ?>"
                                                    class="btn btn-primary btn-sm edit" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9">Không có dữ liệu.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

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

        function searchBooks() {
            const query = document.getElementById('searchInput').value.trim().toLowerCase(); // Lấy giá trị tìm kiếm và chuyển thành chữ thường
            console.log("Tìm kiếm: " + query);

            if (query !== "") {
                const rows = document.querySelectorAll('#tableResults tr');
                rows.forEach(row => {
                    const titleCell = row.querySelector('td:nth-child(2)'); // Kiểm tra cột tên sách (cột thứ 2)
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
       // oTable = $('#sampleTable').DataTable({
            //searching: true // Tắt tính năng tìm kiếm
        // });
        // $(document).ready(function() {
        //     // Khởi tạo DataTable với phân trang
        //     var table = $('#sampleTable').DataTable({
        //         searching: false, // Tắt tìm kiếm mặc định của DataTable
        //         paging: true, // Bật phân trang
        //         pageLength: 10 // Số dòng mỗi trang
        //     });

        //     // Sự kiện tìm kiếm khi nhập dữ liệu vào ô tìm kiếm
        //     $('#searchInput').on('keyup', function() {
        //         var searchTerm = this.value.trim().toLowerCase(); // Lấy từ khóa tìm kiếm và chuyển thành chữ thường

        //         // Vô hiệu hóa phân trang tạm thời khi tìm kiếm
        //         table.page.len(-1).draw(); // Hiển thị tất cả các dòng khi tìm kiếm

        //         // Duyệt qua tất cả các hàng và ẩn/hiển thị các hàng dựa trên từ khóa tìm kiếm
        //         table.rows().every(function() {
        //             var row = this.node();
        //             var titleCell = $(row).find('td:nth-child(2)').text().toLowerCase(); // Lấy tên sách từ cột thứ 2

        //             // Nếu tên sách chứa từ khóa tìm kiếm, hiển thị nó
        //             if (titleCell.includes(searchTerm)) {
        //                 $(row).show(); // Hiển thị nếu trùng
        //             } else {
        //                 $(row).hide(); // Ẩn nếu không trùng
        //             }
        //         });

        //         // Vẽ lại bảng và kích hoạt lại phân trang (nếu có)
        //         table.page.len(10).draw(); // Quay lại phân trang bình thường với 10 dòng mỗi trang
        //     });

        //     // Xử lý sự kiện click vào checkbox "Select All"
        //     $('#all').click(function(e) {
        //         var isChecked = $(this).is(':checked'); // Kiểm tra trạng thái của checkbox "Select All"
        //         table.rows().every(function() {
        //             var row = this.node();
        //             $(row).find(':checkbox').prop('checked', isChecked); // Cập nhật trạng thái checkbox của tất cả các hàng
        //         });
        //         e.stopImmediatePropagation();
        //     });
        // });
    </script>

</body>

</html>