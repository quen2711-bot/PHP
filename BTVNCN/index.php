<?php
require_once "db.php";

if (isset($_POST["them"])) {
    $sql = "INSERT INTO SanPham (TenSP, Loai, ThuongHieu, Gia, SoLuong)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $_POST["TenSP"],
        $_POST["Loai"],
        $_POST["ThuongHieu"],
        $_POST["Gia"],
        $_POST["SoLuong"]
    ]);
    header("Location: index.php");
    exit;
}

if (isset($_POST["sua"])) {
    $sql = "UPDATE SanPham
            SET TenSP=?, Loai=?, ThuongHieu=?, Gia=?, SoLuong=?
            WHERE MaSP=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $_POST["TenSP"],
        $_POST["Loai"],
        $_POST["ThuongHieu"],
        $_POST["Gia"],
        $_POST["SoLuong"],
        $_POST["MaSP"]
    ]);
    header("Location: index.php");
    exit;
}

if (isset($_POST["xoa"])) {
    $stmt = $conn->prepare("DELETE FROM SanPham WHERE MaSP=?");
    $stmt->execute([$_POST["MaSP"]]);
    header("Location: index.php");
    exit;
}

$sanPhamSua = null;

if (isset($_GET["sua"])) {
    $stmt = $conn->prepare("SELECT * FROM SanPham WHERE MaSP=?");
    $stmt->execute([$_GET["sua"]]);
    $sanPhamSua = $stmt->fetch(PDO::FETCH_ASSOC);
}

$dsSanPham = $conn->query("SELECT * FROM SanPham ORDER BY MaSP")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm</title>
    <style>
        body {
            font-family: Arial;
            margin: 30px;
        }

        h2 {
            text-align: center;
        }

        form {
            margin-bottom: 20px;
        }

        input {
            padding: 8px;
            margin: 5px;
        }

        button {
            padding: 8px 12px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #ddd;
        }

        a {
            margin: 5px;
        }
    </style>
</head>
<body>

<h2>QUẢN LÝ SẢN PHẨM</h2>

<h3><?php echo $sanPhamSua ? "Sửa sản phẩm" : "Thêm sản phẩm"; ?></h3>

<form method="post">
    <?php if ($sanPhamSua): ?>
        <input type="hidden" name="MaSP"
               value="<?php echo $sanPhamSua['MaSP']; ?>">
    <?php endif; ?>

    <input type="text" name="TenSP" placeholder="Tên sản phẩm"
           value="<?php echo htmlspecialchars($sanPhamSua['TenSP'] ?? ''); ?>"
           required>

    <input type="text" name="Loai" placeholder="Loại sản phẩm"
           value="<?php echo htmlspecialchars($sanPhamSua['Loai'] ?? ''); ?>">

    <input type="text" name="ThuongHieu" placeholder="Thương hiệu"
           value="<?php echo htmlspecialchars($sanPhamSua['ThuongHieu'] ?? ''); ?>">

    <input type="number" name="Gia" placeholder="Giá"
           min="0" step="0.01"
           value="<?php echo $sanPhamSua['Gia'] ?? ''; ?>" required>

    <input type="number" name="SoLuong" placeholder="Số lượng"
           min="0"
           value="<?php echo $sanPhamSua['SoLuong'] ?? ''; ?>" required>

    <?php if ($sanPhamSua): ?>
        <button type="submit" name="sua">Cập nhật</button>
        <a href="index.php">Hủy</a>
    <?php else: ?>
        <button type="submit" name="them">Thêm sản phẩm</button>
    <?php endif; ?>
</form>

<h3>Danh sách sản phẩm</h3>

<table>
    <tr>
        <th>Mã SP</th>
        <th>Tên sản phẩm</th>
        <th>Loại</th>
        <th>Thương hiệu</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Chức năng</th>
    </tr>

    <?php foreach ($dsSanPham as $sp): ?>
        <tr>
            <td><?php echo $sp['MaSP']; ?></td>
            <td><?php echo htmlspecialchars($sp['TenSP']); ?></td>
            <td><?php echo htmlspecialchars($sp['Loai']); ?></td>
            <td><?php echo htmlspecialchars($sp['ThuongHieu']); ?></td>
            <td><?php echo number_format($sp['Gia'], 0, ',', '.'); ?> đ</td>
            <td><?php echo $sp['SoLuong']; ?></td>
            <td>
                <a href="index.php?sua=<?php echo $sp['MaSP']; ?>">Sửa</a>

                <form method="post" style="display:inline"
                      onsubmit="return confirm('Bạn có muốn xóa sản phẩm này không?')">
                    <input type="hidden" name="MaSP"
                           value="<?php echo $sp['MaSP']; ?>">
                    <button type="submit" name="xoa">Xóa</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html