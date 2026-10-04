<?php

// define("DB_HOST", "127.0.0.1");   // máy local. Dùng 127.0.0.1 (không dùng "localhost") để PHP kết nối qua cổng 3306
// define("DB_PORT", 3306);           // cổng mặc định của MySQL
// define("DB_NAME", "bai9_quan_ly_sinh_vien");
// define("DB_USER", "root");
// define("DB_PASS", "123456789");     // <-- ĐIỀN MẬT KHẨU MYSQL CỦA BẠN VÀO ĐÂY (XAMPP mặc định để trống)

// DSN: chuỗi mô tả "kết nối tới đâu"
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=Quanlysinhvien;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // có lỗi SQL thì báo lỗi ngay
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // kết quả trả về dạng mảng "cột" => giá trị
    ]);

    $dsSinhVien = $pdo->query("SELECT MaSV, TenSV, Ngaysinh, Email, SDT, Lop FROM SinhVien ORDER BY MaSV")->fetchAll();
    echo json_encode($dsSinhVien);
} catch (PDOException $e) {
    // Không kết nối được: dừng chương trình và báo lỗi dễ hiểu
    die("Không kết nối được database: " . $e->getMessage()
        . "\nHãy kiểm tra: MySQL đã bật chưa? Đã chạy file database.sql chưa? Mật khẩu trong config.php đúng chưa?\n");
}

?>