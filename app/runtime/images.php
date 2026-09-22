<?php
namespace Taha;
if($path==='/captha.php'){
 $cap=(string)($_GET['cap']??'');if(!preg_match('/^[A-Za-z0-9]{1,10}$/D',$cap)){http_response_code(400);return;}
 $style=$_GET['style']??'';$asset=$style==='taha'?'taha.jpg':($style==='1'?'2.jpg':'1.jpg');
 $im=imagecreatefromjpeg(TC_APP.'/legacy/'.$asset);$w=imagesx($im);$h=imagesy($im);
 $size=min(72,$h/3,$w/(max(1,strlen($cap))*.8));$font=TC_APP.'/legacy/font.ttf';$color=imagecolorallocate($im,187,222,251);
 $box=imageftbbox($size,0,$font,$cap);$x=($w-($box[2]-$box[0]))/2;$y=($h+($box[1]-$box[7]))/2;
 imagettftext($im,$size,0,(int)$x,(int)$y,$color,$font,$cap);header('Content-Type: image/jpeg');imagejpeg($im);imagedestroy($im);return;
}
$v=$_GET['dayamar']??[];if(!is_array($v))$v=[];$v=array_slice($v,0,31,true);$v=array_map(fn($n)=>max(0,min(100000000,(int)$n)),$v);
$im=imagecreatetruecolor(600,320);$bg=imagecolorallocate($im,245,248,251);$fg=imagecolorallocate($im,17,126,149);imagefill($im,0,0,$bg);
$max=max(1,...array_values($v));$i=0;$step=540/max(1,count($v));
foreach($v as $day=>$n){$x=30+$i*$step;$top=280-240*$n/$max;imagefilledrectangle($im,(int)$x,(int)$top,(int)($x+$step*.7),280,$fg);imagestring($im,2,(int)$x,287,substr((string)$day,0,10),$fg);imagestring($im,2,(int)$x,(int)$top-16,(string)$n,$fg);$i++;}
header('Content-Type: image/png');imagepng($im);imagedestroy($im);
