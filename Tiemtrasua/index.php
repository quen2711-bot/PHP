<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tiệm Trà Sữa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1 style="text-align:center; color:#d64880;">TIỆM TRÀ SỮA</h1>
    <nav>
        <a href="?page=home">Trang Chủ</a>
        <a href="?page=search">Tìm Kiếm</a>
        <a href="?page=order">Đặt Hàng</a>
        <a href="?page=track">Tra Đơn</a>
        <?php if (isset($_SESSION['admin'])): ?>
            <a href="?page=admin-products">Quản Lý Món</a>
            <a href="?page=admin-orders">Quản Lý Đơn</a>
            <a href="?page=admin-stats">Thống Kê</a>
            <form method="post" style="display:inline;">
                <button type="submit" name="logout">Đăng Xuất</button>
            </form>
        <?php else: ?>
            <a href="?page=login">Đăng Nhập</a>
        <?php endif; ?>
    </nav>

    <?php
    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    $page = $_GET['page'] ?? 'home';

    if ($page == 'home') {
        $result = $conn->query("SELECT * FROM products WHERE is_active = 1");
        echo "<h2>Thực Đơn</h2><div class='grid'>";
        while ($p = $result->fetch_assoc()) {
            echo "<div class='card'>";
            echo "<h3>".$p['name']."</h3>";
            echo "<p>Giá: ".number_format($p['price'])." đ</p>";
            echo "<p>".$p['description']."</p>";
            echo "</div>";
        }
        echo "</div>";
    }

    if ($page == 'search') {
        echo "<h2>Tìm Kiếm Món</h2>";
        echo "<form method='post'>";
        echo "<input type='text' name='keyword' placeholder='Tên món...' required>";
        echo "<button type='submit' name='do_search'>Tìm</button></form>";
        if (isset($_POST['do_search'])) {
            $kw = "%".$conn->real_escape_string($_POST['keyword'])."%";
            $stmt = $conn->prepare("SELECT * FROM products WHERE is_active = 1 AND (name LIKE ? OR description LIKE ?)");
            $stmt->bind_param("ss", $kw, $kw);
            $stmt->execute();
            $res = $stmt->get_result();
            echo "<div class='grid'>";
            while ($p = $res->fetch_assoc()) {
                echo "<div class='card'><h3>".$p['name']."</h3><p>Giá: ".number_format($p['price'])." đ</p><p>".$p['description']."</p></div>";
            }
            echo "</div>";
        }
    }

    if ($page == 'order') {
        echo "<h2>Đặt Hàng</h2>";
        $res = $conn->query("SELECT * FROM products WHERE is_active = 1");
        echo "<form method='post'>";
        echo "<input type='text' name='customer_name' placeholder='Họ và tên' required>";
        echo "<input type='tel' name='customer_phone' placeholder='Số điện thoại' required>";
        echo "<input type='text' name='customer_address' placeholder='Địa chỉ' required>";
        echo "<textarea name='note' placeholder='Ghi chú'></textarea>";
        echo "<h3>Chọn món:</h3>";
        while ($p = $res->fetch_assoc()) {
            echo "<label style='display:block; margin:5px 0;'>";
            echo "<input type='number' name='qty[".$p['id']."]' min='0' value='0' style='width:60px; margin-right:10px;'> ";
            echo $p['name']." - ".number_format($p['price'])." đ";
            echo "</label>";
        }
        echo "<button type='submit' name='do_order'>Xác Nhận Đặt Đơn</button></form>";

        if (isset($_POST['do_order'])) {
            $name = $_POST['customer_name'];
            $phone = $_POST['customer_phone'];
            $address = $_POST['customer_address'];
            $note = $_POST['note'];
            $total = 0;
            $items = [];
            foreach ($_POST['qty'] as $pid => $qty) {
                if ($qty > 0) {
                    $p = $conn->query("SELECT price FROM products WHERE id = $pid")->fetch_assoc();
                    $items[] = ['id' => $pid, 'qty' => $qty, 'price' => $p['price']];
                    $total += $p['price'] * $qty;
                }
            }
            if (empty($items)) { echo "<p style='color:red;'>Vui lòng chọn món!</p>"; }
            else {
                $stmt = $conn->prepare("INSERT INTO orders (customer_name, customer_phone, customer_address, note, total_amount) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $name, $phone, $address, $note, $total);
                $stmt->execute();
                $order_id = $conn->insert_id;
                foreach ($items as $it) {
                    $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiii", $order_id, $it['id'], $it['qty'], $it['price']);
                    $stmt->execute();
                }
                echo "<p style='color:green;'>Đặt hàng thành công! Mã đơn: $order_id</p>";
            }
        }
    }

    if ($page == 'track') {
        echo "<h2>Tra Cứu Đơn Hàng</h2>";
        echo "<form method='post'>";
        echo "<input type='tel' name='search_phone' placeholder='Nhập số điện thoại' required>";
        echo "<button type='submit' name='do_track'>Tra Cứu</button></form>";
        if (isset($_POST['do_track'])) {
            $phone = $conn->real_escape_string($_POST['search_phone']);
            $res = $conn->query("SELECT * FROM orders WHERE customer_phone = '$phone' ORDER BY created_at DESC");
            if ($res->num_rows == 0) { echo "<p>Không tìm thấy đơn hàng</p>"; }
            else {
                while ($o = $res->fetch_assoc()) {
                    echo "<div class='card'>";
                    echo "<p><strong>Mã đơn:</strong> ".$o['id']." | <strong>Trạng thái:</strong> ".$o['status']."</p>";
                    echo "<p>Ngày: ".$o['created_at']." | Tổng: ".number_format($o['total_amount'])." đ</p>";
                    echo "<a href='?page=track&order_id=".$o['id']."'>Xem chi tiết</a>";
                    echo "</div>";
                }
            }
        }
        if (isset($_GET['order_id'])) {
            $oid = intval($_GET['order_id']);
            $o = $conn->query("SELECT * FROM orders WHERE id = $oid")->fetch_assoc();
            $d = $conn->query("SELECT od.*, p.name FROM order_details od JOIN products p ON od.product_id = p.id WHERE od.order_id = $oid");
            echo "<div class='card'><h3>Chi Tiết Đơn $oid</h3>";
            echo "<p>Tên: ".$o['customer_name']." | Điện thoại: ".$o['customer_phone']."</p>";
            echo "<p>Địa chỉ: ".$o['customer_address']."</p>";
            echo "<table><tr><th>Tên món</th><th>Số lượng</th><th>Đơn giá</th><th>Thành tiền</th></tr>";
            while ($row = $d->fetch_assoc()) {
                echo "<tr><td>".$row['name']."</td><td>".$row['quantity']."</td><td>".number_format($row['price'])."</td><td>".number_format($row['price']*$row['quantity'])."</td></tr>";
            }
            echo "</table><p><strong>Tổng cộng: ".number_format($o['total_amount'])." đ</strong></p></div>";
        }
    }

    if ($page == 'login') {
        echo "<h2>Đăng Nhập</h2>";
        echo "<form method='post'>";
        echo "<input type='text' name='username' placeholder='Tên đăng nhập' required>";
        echo "<input type='password' name='password' placeholder='Mật khẩu' required>";
        echo "<button type='submit' name='do_login'>Đăng Nhập</button></form>";
        if (isset($_POST['do_login'])) {
            $u = $conn->real_escape_string($_POST['username']);
            $p = $conn->real_escape_string($_POST['password']);
            $res = $conn->query("SELECT * FROM users WHERE username = '$u' AND password = '$p'");
            if ($res->num_rows === 1) {
                $_SESSION['admin'] = $u;
                header("Location: index.php?page=admin-products");
                exit;
            } else { echo "<p style='color:red;'>Sai thông tin</p>"; }
        }
    }

    if ($page == 'admin-products' && isset($_SESSION['admin'])) {
        echo "<h2>Quản Lý Thực Đơn</h2>";
        echo "<a href='?page=admin-products&action=add'>Thêm món mới</a> | ";
        echo "<a href='?page=admin-products'>Danh sách</a><br><br>";

        if (isset($_GET['action']) && $_GET['action'] == 'add') {
            echo "<h3>Thêm Món</h3><form method='post'>";
            echo "<input type='text' name='name' placeholder='Tên Món' required>";
            echo "<input type='number' name='price' placeholder='Giá' required>";
            echo "<textarea name='description' placeholder='Mô tả'></textarea>";
            echo "<select name='is_active'><option value='1'>Đang Bán</option><option value='0'>Ẩn</option></select>";
            echo "<button type='submit' name='add_product'>Lưu</button></form>";
            if (isset($_POST['add_product'])) {
                $name = $_POST['name']; $price = $_POST['price']; $desc = $_POST['description']; $act = $_POST['is_active'];
                $stmt = $conn->prepare("INSERT INTO products (name, price, description, is_active) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sisi", $name, $price, $desc, $act);
                $stmt->execute();
                header("Location: index.php?page=admin-products"); exit;
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $p = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
            echo "<h3>Sửa Món</h3><form method='post'>";
            echo "<input type='text' name='name' value='".$p['name']."' required>";
            echo "<input type='number' name='price' value='".$p['price']."' required>";
            echo "<textarea name='description'>".$p['description']."</textarea>";
            echo "<select name='is_active'><option value='1' ".($p['is_active']?'selected':'').">Đang Bán</option><option value='0' ".($p['is_active']?'':'selected').">Ẩn</option></select>";
            echo "<button type='submit' name='edit_product'>Cập Nhật</button></form>";
            if (isset($_POST['edit_product'])) {
                $name = $_POST['name']; $price = $_POST['price']; $desc = $_POST['description']; $act = $_POST['is_active'];
                $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, is_active=? WHERE id=?");
                $stmt->bind_param("sisii", $name, $price, $desc, $act, $id);
                $stmt->execute();
                header("Location: index.php?page=admin-products"); exit;
            }
        }

        if (isset($_GET['action']) && $_GET['action'] == 'del' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $conn->query("DELETE FROM products WHERE id = $id");
            header("Location: index.php?page=admin-products"); exit;
        }

        $res = $conn->query("SELECT * FROM products ORDER BY id DESC");
        echo "<table><tr><th>Tên</th><th>Giá</th><th>Trạng thái</th><th>Hành Động</th></tr>";
        while ($p = $res->fetch_assoc()) {
            $stt = $p['is_active'] ? 'Đang Bán' : 'Đã ẩn';
            echo "<tr><td>".$p['name']."</td><td>".number_format($p['price'])."</td><td>$stt</td>";
            echo "<td><a href='?page=admin-products&action=edit&id=".$p['id']."'>Sửa</a> | ";
            echo "<a href='?page=admin-products&action=del&id=".$p['id']."' onclick='return confirm(\"Xóa?\")'>Xóa</a></td></tr>";
        }
        echo "</table>";
    } elseif ($page == 'admin-products') {
        echo "<p>Đăng Nhập</p>";
    }

    if ($page == 'admin-orders' && isset($_SESSION['admin'])) {
        echo "<h2>Quản Lý Đơn Hàng</h2>";
        $status = $_GET['status'] ?? 'all';
        echo "<a href='?page=admin-orders'>Tất cả</a> | ";
        echo "<a href='?page=admin-orders&status=pending'>Chờ xử lý</a> | ";
        echo "<a href='?page=admin-orders&status=completed'>Đã hoàn thành</a> | ";
        echo "<a href='?page=admin-orders&status=cancelled'>Đã hủy</a><br><br>";

        if ($status == 'all') {
            $res = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
        } else {
            $st = $conn->real_escape_string($status);
            $res = $conn->query("SELECT * FROM orders WHERE status = '$st' ORDER BY created_at DESC");
        }

        echo "<table><tr><th>Mã</th><th>Tên</th><th>Điện thoại</th><th>Tổng tiền</th><th>Trạng thái</th><th>Ngày</th><th>Hành động</th></tr>";
        while ($o = $res->fetch_assoc()) {
            echo "<tr><td>".$o['id']."</td><td>".$o['customer_name']."</td><td>".$o['customer_phone']."</td>";
            echo "<td>".number_format($o['total_amount'])."</td><td>".$o['status']."</td><td>".$o['created_at']."</td>";
            echo "<td><a href='?page=track&order_id=".$o['id']."'>Xem chi tiết</a> | ";
            echo "<form method='post' style='display:inline;'>";
            echo "<input type='hidden' name='order_id' value='".$o['id']."'>";
            echo "<select name='new_status' onchange='this.form.submit()'>";
            foreach(['pending'=>'Chờ xử lý','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'] as $k=>$v) {
                $sel = $o['status']==$k?'selected':'';
                echo "<option value='$k' $sel>$v</option>";
            }
            echo "</select></form></td></tr>";
        }
        echo "</table>";

        if (isset($_POST['new_status'])) {
            $id = intval($_POST['order_id']);
            $st = $conn->real_escape_string($_POST['new_status']);
            $conn->query("UPDATE orders SET status = '$st' WHERE id = $id");
            header("Location: index.php?page=admin-orders&status=$status"); exit;
        }
    } elseif ($page == 'admin-orders') {
        echo "<p>Đăng Nhập</p>";
    }

    if ($page == 'admin-stats' && isset($_SESSION['admin'])) {
        echo "<h2>Thống Kê Theo Tháng</h2>";
        $sql = "SELECT COUNT(*) as total_order, SUM(IF(status='completed', total_amount, 0)) as revenue 
                FROM orders WHERE YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
        $st = $conn->query($sql)->fetch_assoc();
        echo "<div class='card'>";
        echo "<p><strong>Tổng số đơn hàng:</strong> ".$st['total_order']."</p>";
        echo "<p><strong>Doanh thu đơn thành công:</strong> ".number_format($st['revenue'] ?? 0)." đ</p>";
        echo "</div>";
    } elseif ($page == 'admin-stats') {
        echo "<p>Đăng Nhập</p>";
    }
    ?>
</div>
</body>
</html>