<?php

$cap = $_GET['cap'];
/*
$image = imagecreate(100, 20);
$background = imagecolorallocate($image, 0, 0, 0);
$foreground = imagecolorallocate($image, 255, 255, 255);
//this is going to write our string in the image
imagestring($image, 5,5,1,$cap,$foreground);
header("Content-type: image/jpeg");
imagejpeg($image);
// تنظیم هدر HTTP
*/
header("Content-type: image/jpeg");
// ایجاد منبع تصویر
if($_GET['style']==1){
$one = 187;
$two = 222;
$three = 251;
$aks = '2.jpg';
}elseif($_GET['style']=="taha"){
$one = 187;
$two = 222;
$three = 251;
$aks = 'taha.jpg';
}else{
	$one = 73;
$two = 188;
$three = 216;
$aks = '1.jpg';
	}
$image = imagecreatefromjpeg($aks);
// مشخص کردن مقدار RGB رنگ قرمز

$color = imagecolorallocate($image, $one, $two, $three);
// متن موردنظر جهت درج کردن
$string = $cap;
// درج نام فایل TTF فونت
$font = 'font.ttf';
// درج اندازه فونت
$fontSize = '95px';
// درج مختصات موردنظر
  $width = imagesx($image);
  $height = imagesy($image);
// Get center coordinates of image
  $centerX = $width / 2;
  $centerY = $height / 2;
// Get size of text
  list($left, $bottom, $right, , , $top) = imageftbbox($fontSize, $angle, $font, $string);
// Determine offset of text
  $left_offset = ($right - $left) / 2;
  $top_offset = ($bottom - $top) / 2;
// Generate coordinates
  $x = $centerX - $left_offset;
  $y = $centerY - $top_offset + 50;
// استفاده از تابع
imagettftext($image, $fontSize , 0, $x, $y , $color, $font, $string);
// نمایش تصویر ایجاد شده در خروجی
imagejpeg($image);
?>
?>