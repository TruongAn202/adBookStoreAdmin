<?php
session_start(); // Bắt đầu session để lưu trữ thông tin người dùng

// Include tệp kết nối database
include 'model/connectdb.php';
$conn = connectdb();
if ($conn === null) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
}

// Kiểm tra nếu form đã được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Tránh SQL Injection bằng cách sử dụng prepared statements
    $stmt = $conn->prepare("SELECT * FROM roleadminuser WHERE username = :username");
    $stmt->bindValue(":username", $username, PDO::PARAM_STR);  // Sử dụng bindValue cho PDO
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra xem người dùng có tồn tại không
    if ($result) {
        // Lấy dữ liệu người dùng
        // Kiểm tra mật khẩu
        if ($password == $result['password']) {
            // Mật khẩu chính xác, lưu thông tin người dùng vào session
            $_SESSION['username'] = $result['username'];
            $_SESSION['vaiTro'] = $result['vaiTro']; // Lưu vai trò người dùng vào session
    
            // Kiểm tra vai trò của người dùng
            if ($_SESSION['vaiTro'] == 'admin') {
                // Nếu người dùng là admin, chuyển hướng đến trang quản trị
                header("Location:view/layout/Dashboard.php");
                exit();
            } else {
                // Nếu người dùng không phải admin, không cho phép truy cập
                $_SESSION['error_message'] = 'Bạn không có quyền truy cập vào trang này!';
                header("Location: index.php");
                exit();
            }
        } else {
            // Nếu mật khẩu sai
            $_SESSION['error_message'] = 'Mật khẩu sai!';
            header("Location: index.php");
            exit();
        }
    } else {
        // Nếu không tìm thấy người dùng
        $_SESSION['error_message'] = 'Tài khoản không tồn tại!';
        header("Location: index.php");
        exit();
    }

}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng nhập quản trị | Website quản trị v2.0</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
</head>

<body>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <img src="https://cdn.prod.website-files.com/6062dae861710f58b492ab1d/60803c7e7b41304ea401bc83_2691162%402x.png" loading="lazy" alt="">
                </div>
                <!--=====TIÊU ĐỀ======-->
                <form class="login100-form validate-form" method="POST" action="">
                    <span class="login100-form-title">
                        <b>ĐĂNG NHẬP</b>
                    </span>
                    <!--=====FORM INPUT TÀI KHOẢN VÀ PASSWORD======-->
                    <div class="wrap-input100 validate-input">
                        <input class="input100" type="text" placeholder="Tài khoản quản trị" name="username" id="username" required>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class='bx bx-user'></i>
                        </span>
                    </div>
                    <div class="wrap-input100 validate-input">
                        <input autocomplete="off" class="input100" type="password" placeholder="Mật khẩu" name="password" id="password-field" required>
                        <span toggle="#password-field" class="bx fa-fw bx-hide field-icon click-eye"></span>
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class='bx bx-key'></i>
                        </span>
                    </div>

                    <!--=====ĐĂNG NHẬP======-->
                    <div class="container-login100-form-btn">
                        <input type="submit" value="Đăng nhập" id="submit" />
                    </div>
                    <!--=====LINK TÌM MẬT KHẨU======-->
                    <div class="text-right p-t-12">
                        <a class="txt2" href="/forgot.html">
                            Bạn quên mật khẩu?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--Javascript-->
    <script src="/js/main.js"></script>
    <script src="https://unpkg.com/boxicons@latest/dist/boxicons.js"></script>
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/select2/select2.min.js"></script>
    <script type="text/javascript">
        //show - hide mật khẩu
        $(".click-eye").click(function () {
            $(this).toggleClass("bx-show bx-hide");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
</body>

