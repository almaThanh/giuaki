<?php
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

// Thực hiện thêm, sửa, xóa nhân viên nếu có yêu cầu từ form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['them'])) {
        // Xử lý thêm nhân viên
        $ma_nv = $_POST['ma_nv'];
        $ten_nv = $_POST['ten_nv'];
        $phai = $_POST['phai'];
        $noi_sinh = $_POST['noi_sinh'];
        $ma_phong = $_POST['ma_phong'];
        $luong = $_POST['luong'];

        // Thực hiện truy vấn INSERT để thêm nhân viên vào cơ sở dữ liệu
        $sql_them = "INSERT INTO nhanvien (Ma_NV, Ten_NV, Phai, Noi_Sinh, Ma_Phong, Luong) VALUES ('$ma_nv', '$ten_nv', '$phai', '$noi_sinh', '$ma_phong', '$luong')";
        if ($conn->query($sql_them) === TRUE) {
            echo "Thêm nhân viên thành công";
        } else {
            echo "Lỗi: " . $sql_them . "<br>" . $conn->error;
        }
    } elseif(isset($_POST['xoa'])) {
        // Xử lý xóa nhân viên
        $ma_nv_xoa = $_POST['ma_nv_xoa'];

        // Thực hiện truy vấn DELETE để xóa nhân viên khỏi cơ sở dữ liệu
        $sql_xoa = "DELETE FROM nhanvien WHERE Ma_NV='$ma_nv_xoa'";
        if ($conn->query($sql_xoa) === TRUE) {
            echo "Xóa nhân viên thành công";
        } else {
            echo "Lỗi: " . $sql_xoa . "<br>" . $conn->error;
        }
    } elseif(isset($_POST['sua'])) {
        // Xử lý sửa thông tin nhân viên
        $ma_nv_sua = $_POST['ma_nv_sua'];
        $ten_nv_sua = $_POST['ten_nv_sua'];
        $phai_sua = $_POST['phai_sua'];
        $noi_sinh_sua = $_POST['noi_sinh_sua'];
        $ma_phong_sua = $_POST['ma_phong_sua'];
        $luong_sua = $_POST['luong_sua'];

        // Thực hiện truy vấn UPDATE để cập nhật thông tin nhân viên
        $sql_sua = "UPDATE nhanvien SET Ten_NV='$ten_nv_sua', Phai='$phai_sua', Noi_Sinh='$noi_sinh_sua', Ma_Phong='$ma_phong_sua', Luong='$luong_sua' WHERE Ma_NV='$ma_nv_sua'";
        if ($conn->query($sql_sua) === TRUE) {
            echo "Sửa thông tin nhân viên thành công";
        } else {
            echo "Lỗi: " . $sql_sua . "<br>" . $conn->error;
        }
    }
}

// Số nhân viên trên mỗi trang
$nhansu_tren_trang = 5;

// Xác định trang hiện tại
if (isset($_GET['trang'])) {
    $trang_hien_tai = $_GET['trang'];
} else {
    $trang_hien_tai = 1;
}

// Tính toán vị trí bắt đầu của kết quả truy vấn
$vi_tri_bat_dau = ($trang_hien_tai - 1) * $nhansu_tren_trang;

// Truy vấn để lấy danh sách thông tin nhân viên dựa trên phân trang
$sql = "SELECT nhanvien.Ma_NV, nhanvien.Ten_NV, nhanvien.Phai, nhanvien.Noi_Sinh, nhanvien.Ma_Phong, nhanvien.Luong, phongban.Ten_Phong
        FROM nhanvien 
        INNER JOIN phongban ON phongban.Ma_Phong = nhanvien.Ma_Phong
        LIMIT $vi_tri_bat_dau, $nhansu_tren_trang";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Hiển thị dữ liệu
    echo "<table border='1'>
            <tr>
                <th>Mã nhân viên</th>
                <th>Tên nhân viên</th>
                <th>Giới tính</th>
                <th>Nơi sinh</th>
                <th>Tên phòng</th>
                <th>Phòng</th>
                <th>Lương</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["Ma_NV"] . "</td>";
        echo "<td>" . $row["Ten_NV"] . "</td>";
        echo "<td>";
        // Kiểm tra giới tính để chèn hình ảnh tương ứng
if($row["Phai"] == "NAM") {
    echo "<td><img src='img/man.jpg' alt='Man' width='50' height='50'></td>";
} elseif($row["Phai"] == "NU") {
    echo "<td><img src='img/woman.jpg' alt='Woman' width='50' height='50'></td>";
}

echo "<td>" . $row["Noi_Sinh"] . "</td>";
echo "<td>" . $row["Ten_Phong"] . "</td>";
echo "<td>" . $row["Luong"] . "</td>";
echo "<td>
        <form method='post'>
            <input type='hidden' name='ma_nv_xoa' value='" . $row["Ma_NV"] . "'>
            <input type='submit' name='xoa' value='Xóa'>
        </form>
    </td>";
echo "<td>
        <form method='post'>
            <input type='hidden' name='ma_nv_sua' value='" . $row["Ma_NV"] . "'>
            <input type='submit' name='sua' value='Sửa'>
        </form>
    </td>";
echo "</tr>";
}
echo "</table>";
} else {
echo "Không có dữ liệu nhân viên";
}


// Đếm tổng số nhân viên
$sql_total = "SELECT COUNT(*) AS total FROM nhanvien";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_nhanvien = $row_total['total'];

// Tính toán số trang
$sotrang = ceil($total_nhanvien / $nhansu_tren_trang);

// Hiển thị các liên kết phân trang
echo "<div>";
for ($i = 1; $i <= $sotrang; $i++) {
    echo "<a href='?trang=$i'>$i</a> ";
}
echo "</div>";
// Đóng kết nối
$conn->close();
?>

                    
