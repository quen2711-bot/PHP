<?php
 //Khai báo biến   
$tenSanPham = "Bánh mì";
$donGia = 15000;
$soLuong = 3;

//Phép tính
$thanhTien = $donGia * $soLuong;
$vat = $thanhTien * 10 / 100;
$tongTien = $thanhTien + $vat;

//In kết quả
echo "Tên sản phẩm: $tenSanPham\n";
echo "Đơn giá: $donGia VNĐ\n";
echo "Số lượng: $soLuong\n";
echo "Thành tiền: $thanhTien VNĐ\n";
echo "VAT 10%: $vat VNĐ\n";
echo "Tổng tiền phải trả: $tongTien VNĐ\n";

?>