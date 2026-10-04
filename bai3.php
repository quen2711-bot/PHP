<?php
$products = [
    [
        "name" => "Bánh mì",
        "price" => 15000,
        "quantity" => 2
    ],
    [
        "name" => "Sữa",
        "price" => 30000,
        "quantity" => 1
    ],
    [
        "name" => "Trứng",
        "price" => 25000,
        "quantity" => 3
    ]
];

$total = 0;

foreach ($products as $product){
    $money = $product["price"] * $product["quantity"];
    $total += $money;

    echo "Tên sản phẩm: " . $product["name"] . "<br>";
    echo "Đơn giá: " . $product["price"] . " VNĐ<br>";
    echo "Số lượng: " . $product["quantity"] . "<br>";
    echo "Thành tiền: " . $money . " VNĐ<br>";
    echo "<hr>";
}

if ($total >= 100000) {
    $discount = $total * 10 / 100;
} else {
    $discount = 0;
}

$finalTotal = $total - $discount;

echo "Tổng tiền: " . $total . " VNĐ<br>";
echo "Giảm giá: " . $discount . " VNĐ<br>";
echo "Tổng tiền phải trả: " . $finalTotal . " VNĐ";

    
?>