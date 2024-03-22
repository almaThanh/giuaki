<?php
session_start();

// Thông tin kết nối đến cơ sở dữ liệu MySQL
$servername = "localhost";
$username = "root";
$password = "";
$database = "ql_nhansu";

// Tạo kết nối đến cơ sở dữ liệu
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Xử lý đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Bảo vệ dữ liệu nhập vào để ngăn chặn tấn công SQL injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Truy vấn để kiểm tra thông tin đăng nhập
    $sql = "SELECT * FROM user WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Đăng nhập thành công
        $row = $result->fetch_assoc();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $row['role']; // Lưu vai trò của người dùng vào session

        if ($_SESSION['role'] == 'admin') {
            // Nếu người dùng có vai trò là "admin", chuyển hướng đến trang quản lý nhân viên
            header("location: giuaki.php");
        } else {
            // Nếu người dùng không có vai trò là "admin", có thể hiển thị thông báo lỗi hoặc chuyển hướng đến trang không có quyền truy cập
            echo "Bạn không có quyền truy cập vào trang này.";
        }
    } else {
        // Đăng nhập thất bại
        $error = "Tên đăng nhập hoặc mật khẩu không chính xác";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
</head>
<body>

<h2>Đăng nhập</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="username">Tên đăng nhập:</label><br>
    <input type="text" id="username" name="username"><br>
    <label for="password">Mật khẩu:</label><br>
    <input type="password" id="password" name="password"><br><br>
    <input type="submit" value="Đăng nhập">
</form>

<?php
// Hiển thị thông báo lỗi nếu có
if (isset($error)) {
    echo "<p style='color: red;'>$error</p>";
}
?>

</body>
</html>

<?php
// Đóng kết nối
$conn->close();
?>
