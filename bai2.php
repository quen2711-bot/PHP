<?php

$name = "Nguyen Van An";
$score = 1.5;

if ($score >= 8 ) {
    $xepLoai = "Giỏi";
}
elseif ($score >= 6.5 ) {
        $xepLoai = "Khá";
}
elseif ($score >= 5 ) {
        $xepLoai = "TB";
}
else {
        $xepLoai = "Không đạt";
}

if ($score >= 5) {
    $message = "Bạn đã đạt";
} else {
    $message = "Bạn chưa đạt";
}

echo "Họ tên: " . $name . "<br>";
echo "Điểm: " . $score . "<br>";
echo "Xếp loại: " . $rank . "<br>";
echo "Kết quả: " . $message;

?>