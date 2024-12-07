<?php
   function connectdb(){
    $servername = "localhost";
    $username = "root";
    $password = "";
   
    try {
        $conn = new PDO("mysql:host=$servername;dbname=dbbookstore1", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected successfully";
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        return null;  // trả về null nếu kết nối thất bại
    }
    return $conn;  // trả về đối tượng kết nối nếu thành công
}
    function get_all($sql){
        $conn = connectdb();
        if (!$conn) {
            // Nếu không kết nối được, trả về thông báo lỗi
            echo "Không thể kết nối tới cơ sở dữ liệu.";
            return false;
        }
    
        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $arr = $stmt->fetchAll(); // trả về tất cả (hiển thị)
            $conn = null; //đóng kết nối
            return $arr;
        } catch (PDOException $e) {
            // Nếu có lỗi khi thực hiện truy vấn
            echo "Lỗi truy vấn: " . $e->getMessage();
            return false;
        }
    }
    
    
?>