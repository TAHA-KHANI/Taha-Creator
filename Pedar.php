<?php
ob_start();
date_default_timezone_set("Asia/Tehran");
//unlink("error_log");
///////////////////
define('API_KEY' , railway_token('CREATOR_TOKEN',"1909615504:AAGBiaD60f7ULx3zj5fq1JHni8cUd4kJsP4"));
//////////////////
$railway_base=railway_base_url();
$con=railway_mysql();

// Check connection
if (mysqli_connect_errno())
 {
   sm($admin,"Failed to connect to MySQL: " . mysqli_connect_error());
 }else{
$sql = "CREATE TABLE `user` 
 ( 
 chatid BIGINT,
firstname TEXT,
lastname TEXT,
username TEXT,
userid INT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
step TEXT,
code TEXT,
zirmaj INT,
emtiaz INT,
PRIMARY KEY(chatid),
Other TEXT,
Other2 TEXT,
Other3 INT,
Other4 TEXT
)";
mysqli_query($con,$sql);

	$sql = "CREATE TABLE `foroshmahsol` 
 ( 
 `id` BIGINT,
sellerid BIGINT,
userid TEXT,
`date` CHAR(15),
`time` CHAR(15),
`tozihat` TEXT,
`type` TEXT,
`caption` TEXT,
`file` TEXT,
`price` TEXT,
`ok` TEXT,
Other TEXT
)";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `amarbot`
(
bot TEXT,
token TEXT,
userid INT,
username TEXT,
creatorid INT
)
";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `blocklist` 
 ( 
 chatid INT
)";
mysqli_query($con,$sql);
}

$sql = "CREATE TABLE `foroshgah`
(
bot TEXT,
tozih TEXT,
price TEXT
)
";
mysqli_query($con,$sql);

function bot($method,$datas=[]){
    $url = "https://api.telegram.org/bot".API_KEY."/".$method;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($datas));
    $res = curl_exec($ch);
    if(curl_error($ch)){
        var_dump(curl_error($ch));
    }else{
        return json_decode($res);
    }
}
function curl($url){
$cSession = curl_init($url);
//curl_setopt($cSession,CURLOPT_URL,"$url");
 curl_setopt($cSession,CURLOPT_RETURNTRANSFER,true);
 curl_setopt($cSession,CURLOPT_HEADER, false);

$result=curl_exec($cSession);

if(curl_error($ch)){
        return curl_error($ch);
    }else{
return $result;
}
curl_close($cSession);

}
function random($length){
//create a or array string called chars
        $chars ="16953060294965954949394012039369223456789";
         $str = "";
        //is the variable which is equal to the length of the string called chars
        $size = strlen($chars);
        for($i=0; $i<$length; $i++){
             $str .= $chars[rand(0, $size-1)];

    }
    return $str;
}

function sm($chatid,$text,$keyboard=null,$parse_mode= 'Html',$disable_web_page_preview=false){
 return bot('sendMessage',[
        'chat_id'=>$chatid,
        'text' =>$text,
        'parse_mode'=>$parse_mode,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'reply_markup'=>$keyboard
        ]);
} 
function fm($chatid,$userid,$message){
    bot('ForwardMessage',[
        'chat_id'=>$chatid,
        'from_chat_id'=>$userid,
        'message_id'=>$message
        ]);
}
function sp($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendPhoto', [
        'chat_id' => $chat_id,
        'photo' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function sv($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendVideo', [
        'chat_id' => $chat_id,
        'video' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function sd($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendDocument', [
        'chat_id' => $chat_id,
        'document' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function sa($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendAudio', [
        'chat_id' => $chat_id,
        'audio' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function svo($chat_id,$file_id,$message= null,$parse_mode= "Html"){
    bot('sendVoice', [
        'chat_id' => $chat_id,
        'voice' => $file_id,
        'caption' =>$message,
        'parse_mode' => $parse_mode
        ]);
}
function ss($chat_id,$file_id){
    bot('sendSticker', [
        'chat_id' => $chat_id,
        'sticker' => $file_id,
        ]);
}
function erm($chatid,$message,$reply_markup){
    bot('editMessageReplyMarkup',[
        'chat_id'=>$chatid,
        'message_id'=>$message,
        'reply_markup'=>$reply_markup
        ]);
}
function em($chatid,$text,$message,$keyboard=null,$disable_web_page_preview=false,$parse_mode =  'Html'){
    bot('EditMessageText',[
        'chat_id'=>$chatid,
        'text'=>$text,
        'message_id'=>$message,
        'parse_mode'=>$parse_mode,
        'disable_web_page_preview'=>$disable_web_page_preview,
        'reply_markup'=>$keyboard
        ]);
} 
function ToDie(){
	global $con;
mysqli_close($con);
die();
}
function gregorian_to_jalali ($g_y, $g_m, $g_d,$str) 
{ 
    $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31); 
    $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29); 
 
  
   $gy = $g_y-1600; 
   $gm = $g_m-1; 
   $gd = $g_d-1; 
 
   $g_day_no = 365*$gy+div($gy+3,4)-div($gy+99,100)+div($gy+399,400); 
 
   for ($i=0; $i < $gm; ++$i) 
      $g_day_no += $g_days_in_month[$i]; 
   if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0))) 
      /* leap and after Feb */ 
      $g_day_no++; 
   $g_day_no += $gd; 
 
   $j_day_no = $g_day_no-79; 
 
   $j_np = div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */ 
   $j_day_no = $j_day_no % 12053; 
 
   $jy = 979+33*$j_np+4*div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */ 
 
   $j_day_no %= 1461; 
 
   if ($j_day_no >= 366) { 
      $jy += div($j_day_no-1, 365); 
      $j_day_no = ($j_day_no-1)%365; 
   } 
 
   for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i) 
      $j_day_no -= $j_days_in_month[$i]; 
   $jm = $i+1; 
   $jd = $j_day_no+1; 
 if($str) return $jy.'/'.$jm.'/'.$jd ;
   return array($jy, $jm, $jd); 
} 
 function div($a,$b) { 
    return (int) ($a / $b); 
} 
function amarcount($type){
	global $con;
$result = mysqli_query($con, "SELECT * FROM $type");
$num_rows = mysqli_num_rows($result);
return $num_rows;
}
function getstep($chatid){
	global $con;
$query = "SELECT * FROM user WHERE chatid='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
return $row['step'];
}
}
function getvalue($table,$roow,$chatid,$row2){
	global $con;
$query = "SELECT * FROM `$table` WHERE `$roow`='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
return $row[$row2];
}
}
function getallvalue($table,$row2){
	global $con;
$query = "SELECT * FROM $table ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
$data[] = $row[$row2];
}
return $data;
}
function setvalue($table,$row2,$val2,$row1,$val1){
	global $con;
$query = "UPDATE `$table` SET `$row1` = '$val1' WHERE `$row2` = '$val2'";
$result = mysqli_query($con,$query);
return $result;
}
function step($chatid,$step){
	global $con;
$query = "UPDATE user SET step = '$step' WHERE chatid = $chatid";
$result = mysqli_query($con,$query);
}
function deletevalue($table,$row,$val){
	global $con;
	$sql = "DELETE FROM `$table` WHERE `$row`='$val'";
	$result = mysqli_query($con,$sql);
	}
	function setcolumn($table,$col,$text){
		global $con;
	$insert_query = "INSERT INTO `$table`($col) VALUES ('$text')";
mysqli_query($con, $insert_query);
									
	}
function isetrow($table,$row){
	global $con;
	$sql = "SELECT EXISTS(SELECT * FROM `$table` WHERE `$row`)";
return	$result = mysqli_query($con,$sql);
	}
function createrow($table,$after,$row,$type){
global $con;
$sql ="ALTER TABLE `$table` ADD `$row` $type after `$after`";
$result = mysqli_query($con,$sql);
}
if(!file_exists("Robat")){
	mkdir("Robat");
	}
	function deleterow($table,$col){
	global $con;
	$query = "ALTER TABLE $table DROP $col";
	$result = mysqli_query($con,$query);
	}
	function deletecolumn($table,$column,$chatid){
		global $con;
	$sql = "DELETE FROM `$table` WHERE `$column`='$chatid'";
	return mysqli_query($con,$sql);
	}

function isetval($table,$roow,$chatid,$row2){
	global $con;
$query = "SELECT * FROM $table WHERE $roow='$chatid' ";
$result = mysqli_query($con,$query);
while ($row = mysqli_fetch_array($result)) {
if(isset($row[$row2]) && !empty($row[$row2])){
	return true;
	}else{
	return false;
	}
}
}
function isetcol($table,$roow,$chatid){
	global $con;
$query = "SELECT $roow FROM $table WHERE $roow='$chatid' ";
$result = mysqli_query($con,$query);
if(count(mysqli_fetch_array($result)) == 0 ) return false; else return true;
}
function columnexsits($table,$row){
$result = mysql_query($con,"SHOW COLUMNS FROM `$table` LIKE '$row'");
$exists = (mysql_num_rows($result))?TRUE:FALSE;
return $exists;
}
function getCoin($chatid){
$coin = getvalue("user","chatid",$chatid,"emtiaz");
return $coin;
}
function setCoin($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"emtiaz",$text);
return $coin;
}
function getZirmaj($chatid){
$coin = getvalue("user","chatid",$chatid,"zirmaj");
return $coin;
}
function setZirmaj($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"zirmaj",$text);
return $coin;
}
function getOther($chatid){
$coin = getvalue("user","chatid",$chatid,"Other");
return $coin;
}
function setOther($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"Other",$text);
return $coin;
}
function getOther2($chatid){
$coin = getvalue("user","chatid",$chatid,"Other2");
return $coin;
}
function setOther2($chatid,$text){
$coin = setvalue("user","chatid",$chatid,"Other2",$text);
return $coin;
}
function alert($querid,$qt,$type=true){
return bot('answerCallbackQuery',[
		'callback_query_id'=>$querid,
		'text'=>$qt,
		'show_alert'=>$type
		]);
}
$admin = "1720440554";
$update = json_decode(file_get_contents('php://input'));
$Message = $update->message;
$messageid = $Message->message_id;
$text = $Message->text;
$chatid = $Message->chat->id;
$fromid = $Message->from->id;
$firstname = $Message->from->first_name;
$username = $Message->from->username;
$chusername = $update->channel_post->chat->id;
$type2= $update->channel_post->chat->type;
$query = $update->callback_query;
$queryid=$query->id;
$querydata=$query->data;

$qmessage = $query->message;
$qmid = $qmessage->message_id;
$qname = $query->from->first_name;
$quser = $query->from->username;
$qid = $query->from->id;
$querychatid=$query->message->chat->id;
$gpname = $Message->chat->title;
$gpuser = $Message->chat->username;
$bio = $Message->chat->bio;
$description = $Message->chat->description;
$newmember = $Message->new_chat_members;
$newid = $newmember[0]->id;
$newname = $newmember[0]->first_name;
$newuser = $newmember[0]->username;
$type= $Message->chat->type;
$photo= $Message->photo[0]->file_id;
$video = $Message->video->file_id;
$sticker = $Message->sticker->file_id;
$audio = $Message->audio->file_id;
$document= $Message->document->file_id;
$voice = $Message->voice->file_id;
$dice = $Message->dice->emoji;
$caption = $Message->caption;
$getmebot = bot("getMe");
$idbot = $getmebot->result->id;
$botname = $getmebot->result->first_name;
$botuser = $getmebot->result->username;
$datesh = gregorian_to_jalali(date("Y"),date("m"),date("d"),"/");
$time = date("H:i:s");
$datem = date("Y:m:d");
$step=getstep($fromid);
$keyback = json_encode([
'keyboard'=>[
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);
$keymahsol = json_encode([
'keyboard'=>[
[["text"=>"فروش ربات"]],
[["text"=>"فروش یک محصول"]],
[["text"=>"برگشت↪"]]
],
'resize_keyboard'=>true
]);

if(isetval("user","chatid",$chatid,"keyboard")){
	$keyboard = getvalue("user","chatid",$chatid,"keyboard");
	$keykarbar='{"keyboard":['.$keyboard.',[{"text":"برگشت↪"}]],"resize_keyboard":true}';
	}
	if($type=="private"){
									$sql = "CREATE TABLE `qw$chatid`
									(
									`bot` TEXT,
									`token` TEXT
									)";
									
									mysqli_query($con, $sql);
									
								
								
$insert_query = "INSERT INTO `user`
(
chatid,
userid,
firstname,
lastname,
username,
joindatesh,
joindatem,
jointime,
zirmaj,
emtiaz,
`Other3`
)
VALUES ('$chatid','$fromid','$firstname','$lastname','$username','$datesh','$datem','$time','0','20','1')";
mysqli_query($con, $insert_query);

	}
	if(!isetrow("user","Other1")){
	createrow("user","emtiaz","Other1","TEXT");
	}
	if(!isetrow("user","Other3")){
	createrow("user","emtiaz","Other3","INT");
	}
	if(!isetrow("user","Other4")){
	createrow("user","Other3","Other4","TEXT");
	}
	if(!isetrow("user","Other5")){
	createrow("user","Other4","Other5","TEXT");
	}
	if(!isetrow("user","Other6")){
	createrow("user","Other5","Other6","INT");
	}
	if(!isetrow("user","like")){
	createrow("user","Other6","like","INT");
	}
	if(!isetrow("user","captha")){
	createrow("user","like","captha","TEXT");
	}
	if(!isetrow("user","buy")){
	createrow("user","like","buy","INT");
	}
	if(!isetrow("foroshmahsol","like")){
	createrow("foroshmahsol","Other","like","TEXT");
	}
	if(!isetrow("foroshmahsol","dislike")){
	createrow("foroshmahsol","like","dislike","TEXT");
	}
	
	
	if(isset($query)){
		if(preg_match('/^rad\-\+\-([0-9]+)$/',$querydata,$ma)){
			if($qid == 1720440554 or $qid==1377243724 or $qid==749260081 or $qid ==1822927777){
				$id = $ma[1];
				$keytamam = json_encode([
"inline_keyboard"=>[
[["text"=>"محصول رد شد توسط ⛔ $quser ",'callback_data'=>"hi"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
	}
erm($querychatid,$qmid,$keytamam);
step($qid,"radkar$id");
sm($querychatid,"لطفا بگید چرا رد شده است؟!!\nاگر نمیخواهید متنی برای کاربر /notext را ارسال کنید");
				return false;
				}
				}
		if(preg_match('/^taiid\-\+\-([0-9]+)$/',$querydata,$ma)){
			if($qid == 1720440554 or $qid==1377243724 or $qid==749260081 or $qid==1822927777 ){
				alert($update->callback_query->id,"تایید شد ، بنر به کانال فرستاده شد✅",true);
				$id = $ma[1];
				$keytamam = json_encode([
"inline_keyboard"=>[
[["text"=>"محصول تایید شده توسط $quser ",'callback_data'=>"hi"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
	}
erm($querychatid,$qmid,$keytamam);

$date = getvalue("foroshmahsol","id",$id,"date");
$time = getvalue("foroshmahsol","id",$id,"time");
$tedad = getvalue("foroshmahsol","id",$id,"Other");
$price = getvalue("foroshmahsol","id",$id,"price");
$toman = number_format($price);
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$likeseller = getvalue("user","chatid",$seller,"like");
if(empty($likeseller)){
	$likeseller = 0;
	}
$buyseller = getvalue("user","chatid",$seller,"buy");
if(empty($buyseller)){
	$buyseller = 0;
	}
$tozihat = getvalue("foroshmahsol","id",$id,"tozihat");
$typea = getvalue("foroshmahsol","id",$id,"type");
$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
if($typea=="text"){
	$nok ="متنی ✏";
	}elseif($typea=="photo"){
	$nok ="عکس 🌌";
	}elseif($typea=="video"){
	$nok ="فیلم 📹";
	}elseif($typea=="document"){
	$nok ="فایل 💾";
	}elseif($typea=="audio"){
	$nok ="موزیک 🎵";
	}
	$txt = "
<b>💠فروش محصول</b>

<b>❤تعداد لایک های فروشنده : $likeseller </b>
<b>📥تعداد فایل های فروخته شده : $buyseller</b> 

<b>📆تاریخ : </b> $date
<b>⏰ساعت :</b> $time
 

★★★★★★★★★★★★★★★★

<b>🖲 نوع محصول : </b>$nok

<b>🗂توضیحات محصول : </b>
$tozihat

<b>🛒حداکثر تعداد دانلود : </b>$tedad

★★★★★★★★★★★★★★★★

<b>💰قیمت : </b> $toman سکه طاها کریتور 


💎برای خرید مستقیم روی دکمه زیر کلیک کنید تا محصول درخواستی در پیوی شما توسط ربات ارسال شود
";
$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"📥خرید محصول📥",'callback_data'=>"mahsol-+-$id"]],
[["text"=>"📌تعداد دانلود مانده : ",'callback_data'=>"--"],["text"=>"$tedad 📌",'callback_data'=>"--"]],
[["text"=>"👍 ($like)","callback_data"=>"like-+-$id"],["text"=>"👎 ($dislike)","callback_data"=>"dislike-+-$id"]],
]
]);
sm("-1001479705339",$txt,$keyfor);
return false;

				}else{
					alert($update->callback_query->id,"تو دسترسی نداری داداچ😐❤",false);
					}
			}
			if(preg_match('/^like\-\+\-([0-9]+)$/',$querydata,$ma)){
		$id = $ma[1];
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$tedad = getvalue("foroshmahsol","id",$id,"Other");
$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
if(!in_array($qid,$exp1)){
	if(in_array($qid,$exp2)){
	$str = str_replace("$qid,","",$dislikeone);
	setvalue("foroshmahsol","id",$id,"dislike",$str);
	}
	setvalue("foroshmahsol","id",$id,"like",$likeone.$qid.",");
	$likeseller = getvalue("user","chatid",$seller,"like");
	if(empty($likeseller)){
		$likeseller = 0;
		}
	$likeseller = $likeseller + 1;
	setvalue("user","chatid",$seller,"like",$likeseller);
	alert($update->callback_query->id,"لایک 👍 شما ثبت شد",false);
	}else{
		alert($update->callback_query->id,"شما قبلا این پست را لایک کردیدℹ",false);
		}
		$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
						$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"📥خرید محصول📥",'callback_data'=>"mahsol-+-$id"]],
[["text"=>"📌تعداد دانلود مانده : ",'callback_data'=>"--"],["text"=>"$tedad 📌",'callback_data'=>"--"]],
[["text"=>"👍 ($like)","callback_data"=>"like-+-$id"],["text"=>"👎 ($dislike)","callback_data"=>"dislike-+-$id"]],
]
]);
						if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
	}
erm($querychatid,$qmid,$keyfor);
}
				if(preg_match('/^dislike\-\+\-([0-9]+)$/',$querydata,$ma)){
		$id = $ma[1];
		$tedad = getvalue("foroshmahsol","id",$id,"Other");
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
if(!in_array($qid,$exp2)){
	if(in_array($qid,$exp1)){
	$str = str_replace("$qid,","",$likeone);
	setvalue("foroshmahsol","id",$id,"like",$str);
	}
	setvalue("foroshmahsol","id",$id,"dislike","$dislikeone$qid,");
	$likeseller = getvalue("user","chatid",$seller,"like");
	if(empty($likeseller)){
		$likeseller = 0;
		}
	$likeseller = $likeseller - 1;
	setvalue("user","chatid",$seller,"like",$likeseller);
	alert($update->callback_query->id,"دیسلایک👎 شما ثبت شد",false);
	}else{
		alert($update->callback_query->id,"شما قبلا این پست را قبلا دیسلایک کردیدℹ",false);
		}
		$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
						$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
						$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"📥خرید محصول📥",'callback_data'=>"mahsol-+-$id"]],
[["text"=>"📌تعداد دانلود مانده : ",'callback_data'=>"--"],["text"=>"$tedad 📌",'callback_data'=>"--"]],
[["text"=>"👍 ($like)","callback_data"=>"like-+-$id"],["text"=>"👎 ($dislike)","callback_data"=>"dislike-+-$id"]],
]
]);
						if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
	}
erm($querychatid,$qmid,$keyfor);
}

			if(preg_match('/^mahsol\-\+\-([0-9]+)$/',$querydata,$ma)){
		$id = $ma[1];
$cap = getvalue("foroshmahsol","id",$id,"caption");
$fileid = getvalue("foroshmahsol","id",$id,"file");
$seller = getvalue("foroshmahsol","id",$id,"sellerid");
$price = getvalue("foroshmahsol","id",$id,"price");
$typem = getvalue("foroshmahsol","id",$id,"type");
$tedad = getvalue("foroshmahsol","id",$id,"Other");

if($qid==$seller){
				alert($update->callback_query->id,"محصول خودتو میخوایی بخری😐?!!!",true);
				return false;
				}
				$coin2 = getCoin($qid);
		if($coin2 < $price){
			alert($update->callback_query->id,"موجودی شما برای خرید این محصول کافی نمی باشد⛔\n\nموجودی شما : $coin2",true);
			}else{
				$coin3 = $coin2 -$price;
				setCoin($qid,$coin3);
				$coin4 =getCoin($seller) + $price;
				setCoin($seller,$coin4);
				$mande = $tedad -1;
				alert($update->callback_query->id,"محصول با موفقیت خریداری شد✅\n\nجزییات توسط ربات در pv شما ارسال میگردد",true);
				$txt = "
⭐یک محصول شما فروخته شد

💰قیمت فروخته شده : $price

📥تعداد خرید مانده : $mande

فایل فروخته شده👇👇👇👇👇
";
sm($seller,$txt);
$buyseller = getvalue("user","chatid",$seller,"buy");
if(empty($buyseller)){
		$buyseller = 0;
		}
	$buyseller = $buyseller + 1;
	setvalue("user","chatid",$seller,"buy",$buyseller);
if($typem=="text"){
	sm($seller,$fileid);
	}elseif($typem=="photo"){
	sp($seller,$fileid,$cap);
	}elseif($typem=="video"){
	sv($seller,$fileid,$cap);
	}elseif($typem=="document"){
	sd($seller,$fileid,$cap);
	}elseif($typem=="audio"){
	sa($seller,$fileid,$cap);
	}
	$txt = "
محصول شما با موفقیت خریداری شد✅

محصول خریداری شده👇👇👇👇
";
sm($qid,$txt);
if($typem=="text"){
	sm($qid,$fileid);
	}elseif($typem=="photo"){
	sp($qid,$fileid,$cap);
	}elseif($typem=="video"){
	sv($qid,$fileid,$cap);
	}elseif($typem=="document"){
	sd($qid,$fileid,$cap);
	}elseif($typem=="audio"){
	sa($qid,$fileid,$cap);
	}
				if($tedad -1 == 0 or $tedad == 0){
					$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"🛂پایان ظرفیت دانلود!!🛂",'callback_data'=>"temam"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
	}
erm($querychatid,$qmid,$keyfor);
					}else{
						$tedad = $tedad -1;
						setvalue("foroshmahsol","id",$id,"Other",$tedad);
						$likeone = getvalue("foroshmahsol","id",$id,"like");
$exp1= explode(",",$likeone);
						$like = count($exp1)-1;
$dislikeone = getvalue("foroshmahsol","id",$id,"dislike");
$exp2= explode(",",$dislikeone);
$dislike = count($exp2)-1;
						$keyfor = json_encode([
"inline_keyboard"=>[
[["text"=>"📥خرید محصول📥",'callback_data'=>"mahsol-+-$id"]],
[["text"=>"📌تعداد دانلود مانده : ",'callback_data'=>"--"],["text"=>"$tedad 📌",'callback_data'=>"--"]],
[["text"=>"👍 ($like)","callback_data"=>"like-+-$id"],["text"=>"👎 ($dislike)","callback_data"=>"dislike-+-$id"]],
]
]);
						if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
	}
erm($querychatid,$qmid,$keyfor);
						
						}
				}

return false;

			}
			
		if(preg_match('/^buy\-([A-Za-z0-9\_]+)\-\+\-([0-9]+)\-\+\-([0-9]+)$/',$querydata,$m)){
			
			$userbot = $m[1];
			$price = $m[2];
			$seller = $m[3];
			if($qid==$seller){
				alert($update->callback_query->id,"ربات خودتو میخوایی بخری😐?!!!",true);
				return false;
				}
				
				if(empty(getvalue("qw$seller","bot",$userbot,"token"))){
					sm($seller,"⚠خطا\n\nادمین گرامی :\nربات @$userbot دیگر در مالکیت شما نیست!! یا از لیست ربات های شما حذف شده هست\nامکان فروش این ربات نیست و محتوای ان در چنل پاک شد");
					alert($update->callback_query->id,"متاسفم!!\n⚠این ربات توسط فروشنده به یک کاربر دیگر انتقال مالکیت داده شده یا حذف شده هست\n\nما به فروشنده اعلان لازم را فرستادیم و این پست پاک میشود!!",true);
				if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
	}
bot("DeleteMessage",[
				'chat_id'=>$querychatid,
				'message_id'=>$qmid
				]);
return false;
					}
		$coin2 = getCoin($qid);
		if($coin2 < $price){
			alert($update->callback_query->id,"موجودی شما برای خرید این ربات کافی نمی باشد⛔\n\nموجودی شما : $coin2",true);
			}else{
				$coin3 = $coin2 -$price;
				setCoin($qid,$coin3);
				$coin4 =getCoin($seller) + $price;
				setCoin($seller,$coin4);
				sm($seller,"ربات شما با یوزرنیم @$userbot به فروش رفت✅\n\nمقدار $price سکه به حساب شما واریز شد\n\n⚠یوزرنیم ربات از لیست ربات های شما حذف شد!",$keyasli);
				alert($update->callback_query->id,"ربات برای شما با موفقیت خریداری شد✅\n\nیوزرنیم ربات در لیست ربات های شما افزوده شد✅",true);
				$file =file_get_contents("BotList/$userbot/$userbot.php");
				$file2 = preg_replace('/(\$admin = ")(.*)(";)/','$admin = "'.$qid.'";',$file);
				file_put_contents("BotList/$userbot/$userbot.php",$file2);
				if(empty(getvalue("user","chatid",$qid,"keyboard"))){
											$keyst='[{"text":""}],[{"text":""}]';
											setvalue("user","chatid",$qid,"keyboard",$keyst);
											}
									$token =	getvalue("qw$seller","bot",$userbot,"token");
									$keysha = getvalue("user","chatid",$seller,"keyboard");
		$cc = str_replace('[{"text":"'.$userbot.'"}],',"",$keysha);
		setvalue("user","chatid",$seller,"keyboard",$cc);
	
								
										$key1 = getvalue("user","chatid",$qid,"keyboard");
											$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$userbot.'"}],[{"text":""}]',$key1);
											setvalue("user","chatid",$qid,"keyboard",$newdokme);
											$sql ="INSERT INTO `qw$qid`(`bot`,`token`)VALUES ('$userbot','$token')";
											mysqli_query($con,$sql);
					
									deletevalue("qw$seller","bot",$userbot);
											sm("-1001578844277","یک ربات در کانال بفروش رفت✅\n\nCreator : tg://user?id=$seller \nCreator ID : $seller\n\nSendFor :$qid\n\nUsername : $userbot\n\nToken :\n$token");
									
												$keyforosh = json_encode([
"inline_keyboard"=>[
[["text"=>"ربات به فروش رفت✅",'callback_data'=>"hi"]],
]
]);
if(isset($update->callback_query)){
$query = $update->callback_query;
$qmid = $query->message->message_id;
	}
erm($querychatid,$qmid,$keyforosh);

				}
			
			return false;
			}
		}
		if($chatid == "-1001520926505" && preg_match('/^radkar([0-9]+)$/',$step,$cv)){
			if($text=="/notext@Taha_Creator_Robot"){
				$id = $cv[1];
				step($fromid,"");
				sm($chatid,"بدون متن برای کاربر ارسال شد✅");
				$seller = getvalue("foroshmahsol","id",$id,"sellerid");
				$txt="محصول شما به دلایل نامعلوم از طرف مدیران رد شد❌";
sm($seller,$txt);
				}else{
					if(isset($text)){
						step($fromid,"");
				sm($chatid,"متن برای کاربر ارسال شد و رد شد✅");
				$id = $cv[1];
				$seller = getvalue("foroshmahsol","id",$id,"sellerid");
				$txt="محصول شما به دلایل از طرف مدیران رد شد❌\n\n⚠دلیل : \n$text";
sm($seller,$txt);
						}
					}
			}
	if(preg_match("/^\/start ([0-9]+)$/i",$text)){
								preg_match("/^(\/start) ([0-9]+)$/i",$text,$match);
								$user = $match[2];
								setOther2($chatid,$user);
								//$exp = getallvalue("user","chatid");
								$text=="/start";
			if(getvalue("user","chatid",$fromid,"Other3")==1){
				setvalue("user","chatid",$fromid,"Other3",2);
				
				}
				
							//	sm($chatid,"text$user");
								
								
								}
								if($chatid == "-1001520926505" ){
									return false;
									}
									/////================
									
									if($chatid !=$admin && $step != "getcaptha1" && empty(getvalue("user","chatid",$chatid,"captha")) && $type=="private"){
				$matn = random(5);
				setOther($chatid,$matn);
				$ch13="لطفا رقم داخل عکس را بفرستید :";
				$ke=json_encode([
				"hide_keyboard"=>true
				]);
				step($chatid,"getcaptha1");
				sp($chatid,"$railway_base/captha.php?cap=$matn&style=taha",$ch13,$ke);
				return false;
			}
if($step=="getcaptha1" && $type=="private" ){
				if(getOther($chatid)==$text){
					step($chatid,"");
					setvalue("user","chatid",$chatid,"captha","true");
					$text="/start";
					}else{
						$matn = random(5);
				setOther($chatid,$matn);
						$ch13= "لطفا کپچا را به درستی وارد کنید!!!";
			sp($chatid,"$railway_base/captha.php?cap=$matn&style=taha",$ch13,$kei);
			return false;
						}
				}
									
									///================
								if( $chatid !=$admin){
			$url2 = json_decode(file_get_contents("https://api.telegram.org/bot".API_KEY."/getChatMember?chat_id=@NorseGem&user_id=$chatid"),true);
			$status = $url2["result"]["status"];
			if($status !== "creator" && $status !== "administrator" && $status !=="member"){
				$keybo=json_encode([
'inline_keyboard'=>[
[["text"=>"کانال📣","url"=>"https://t.me/NorseGem"]],
]
]);
				sm($chatid,"لطفا ابتدا در کانال ما عضو شوید ⛔\n\n@NorseGem\n\nسپس پس از عضو شدن /start رو بزنید\n/start\n/start",$keybo);
	return false;
			}
			}
				
			if(getvalue("user","chatid",$chatid,"Other3")==2 || empty(getvalue("user","chatid",$chatid,"chatid"))){
				$user = getOther2($chatid);
				$co = getvalue("user","chatid",$user,"emtiaz");
	$ozv = getvalue("user","chatid",$user,"zirmaj");
	$co2 = $co + 10;
	$ozv2 = $ozv + 1;
			setvalue("user","chatid",$user,"emtiaz",$co2);
			setvalue("user","chatid",$chatid,"Other3",0);
			setvalue("user","chatid",$user,"zirmaj",$ozv2);
			$txt1="
			یک کاربر جدید به عضو مجموعه شما اضافه شد✅\n\n💰تعداد سکه های شما : $co2
			";
			sm($user,$txt1);
			$text=="/start";
				}
	$txtback="
		به ربات طاها کریتور خوش آمدید🌹

⭐️شما میتوانید ربات دلخواه خودتان را توسط این ربات بسازید .

⭐️شخصی سازی ربات 99% توسط خودتان انجام میشود

⭐️ربات در نسخه های اولیه خود قرار دارد و کم و کاستی های ربات به زودی رفع خواهند شد

⭐️هم اکنون شما میتوانید ربات خودتون رو بسازید 
برای ساخت ربات ابتدا دکمه ی ساخت ربات🔩را بزنید ، سپس توکن دریافتی از @BotFather را برای ما بفرستید .

⭐️اگر در دریافت توکن مشکل دارید ایدی کانال را بزنید .

Channel : @Taha_Creator
		";
$blocklist = getvalue("blocklist","chatid",$chatid,"chatid");
if(!empty($blocklist)){
if(isetval("blocklist","chatid",$chatid,"chatid") !==false || isetval("blocklist","chatid","@$username","chatid") !==false){
//step($chatid,"blocked"); 

  $key=json_encode([
       "hide_keyboard"=>true
       ]);
       sm($chatid,"شما از ربات بلاک شدید⛔",$key);
 return false;
 }}

$keynoyes=json_encode([
'keyboard'=>[
[["text"=>"بله✅"],["text"=>"خیر🚫"]]
],
'resize_keyboard'=>true
]);
$keyasli=json_encode([
"keyboard"=>[
[["text"=>"ساخت ربات🔩(غیرفعال)"]],
[["text"=>"اپدیت ربات🆙"],["text"=>"♻️تغییر توکن"]],
[["text"=>"انتقال مالکیت💠"],["text"=>"تنظیم وبهوک اتوماتیک🎫"]],
[["text"=>"پاکسازی اپدیت های در حال انتظار☢️"]],
[["text"=>"حذف ربات🚮"],["text"=>"ربات های من🤖"]],
[["text"=>"🛍️فروشگاه🛍️"]],
[["text"=>"🚀زیرمجموعه گیری🚀"],["text"=>"💲خرید سکه💲"]],
[["text"=>"انتقال سکه↔"]],
[["text"=>"سکه های من💸"],["text"=>"زیرمجموعه های من👥"]],
[["text"=>"📣کانال اطلاع رسانی📣"]]
],
]);
$keybuy = json_encode([
"keyboard"=>[
[["text"=>"فروش با سکه طاها کریتور"]],
[["text"=>"مجانی"],["text"=>"توافقی"]],
[["text"=>"فقط معاوضه"]],
[["text"=>"برگشت↪"]],
],
'resize_keyboard'=>true
]);



			
if($text=="/start"){
	step($chatid,"");
		$txt="
		به ربات طاها کریتور خوش آمدید🌹

⭐️شما میتوانید ربات دلخواه خودتان را توسط این ربات بسازید .

⭐️شخصی سازی ربات 99% توسط خودتان انجام میشود

⭐️ربات در نسخه های اولیه خود قرار دارد و کم و کاستی های ربات به زودی رفع خواهند شد

⭐️هم اکنون شما میتوانید ربات خودتون رو بسازید 
برای ساخت ربات ابتدا دکمه ی ساخت ربات🔩را بزنید ، سپس توکن دریافتی از @BotFather را برای ما بفرستید .

⭐️اگر در دریافت توکن مشکل دارید ایدی کانال را بزنید .

Channel : @Taha_Creator
		";
	sm($chatid,$txt,$keyasli);
	
	}elseif($text=="📣کانال اطلاع رسانی📣"){
			$txt="
			⭐️جهت اطلاع از آخرین اپدیت ها و آموزش های ربات
 همچنین طریقه ی کار با ربات و ترفند های مهم حتما در کانال ما عضو شوید :

Channel : @Taha_Creator

https://t.me/Taha_Creator
			";
			sm($chatid,$txt);
			
			}elseif($text=="💲خرید سکه💲" || $text=="/buycoin"){
				$txt="⭐️شما میتوانید برای خرید سکه به ایدی زیر 
@tahakhani 
مراجعه فرمایید،

یا میتوانید با ربات زیر با پشتیبانی ارتباط برقرار نمایید
@tahakhaniBot


⚠️قیمت هر 10 سکه معادل با 5,000 ریال میباشد";
				sm($chatid,$txt,$keyasli);
				}elseif($text=="سکه های من💸" || $text=="/mycoin"){
					$coin = getvalue("user","chatid",$chatid,"emtiaz");
					sm($chatid,"💰تعداد موجودی های باقی مانده شما : $coin سکه",$keyasli);
					}elseif($text=="زیرمجموعه های من👥"){
						$get = getvalue("user","chatid",$chatid,"zirmaj");
						$coin = getvalue("user","chatid",$chatid,"emtiaz");
						if(!isset($get)){
							$zir = 0;
							}else{
							$zir = $get;
							}
							$soud = $zir * 10;
						$txt="
						🔖تعداد زیرمجموعه های افزوده شده : $zir\n\n💰سکه های بدست آمده از زیرمجموعه : $soud\n\n💳تعداد سکه های باقی مانده : $coin";
					sm($chatid,$txt,$keyasli);
						}elseif($text=="🚀زیرمجموعه گیری🚀" || $text=="/mylink"){
							$link="http://t.me/Taha_Creator_Robot?start=$chatid";
							$txt1="
							لینک شما برای زیرمجموعه گیری👇👇
							";
							$txt2="
							⭐️میخوایی ربات دلخواهتو بسازی ؟!!

🎉ساخت ربات دلخواهتون با شخصی سازی 99% و بدون یک خط کدنویسی !!!🎉
★افزودن بی نهایت دکمه !!
★با پنل کاربری راحت و آسان!!
★ویژگی های منحصر به فرد!!

💥تست کردنش ضرر نداره💥

💠همیــن الان با لینک زیر شروع کن 👇👇
$link
							";
							$txt3="
							💫از هر نفر کاربری که با لینک شما وارد ربات شوند و start کنن 10 سکه به حساب کاربری شما واریز خواهد شد✅\n\n🚫دقت داشته باشید کاربر نباید از قبل عضو ربات باشد!!!
							";
							sm($chatid,$txt1);
							sm($chatid,$txt2);
							sm($chatid,$txt3,$keyasli);
							}elseif($text=="ساخت ربات🔩(خاموش)" || $text=="/newbot"){
									$coin = getvalue("user","chatid",$chatid,"emtiaz");
									if($coin < 500){
										$txt="
										سکه های شما برای ساخت ربات کافی نمی باشد❌

⭐️در نظر داشته باشید برای ساخت هر ربات 500 سکه از حساب شما کسر خواهد شد!!!

⭐️شما به سه روش میتوانید اقدام به خرید سکه کنید :

1⃣:از طریق زیر مجموعه گیری که هر نفر عضو ربات از طریق لینک شما شود 10 سکه به شما تعلق خواهد گرفت که :

برای دریافت لینک دستور /mylink را بزنید.

2⃣: شما میتوانید اقدام به خرید سکه کنید که بعد از پرداخت سریعا به حساب شما واریز و شما میتوانید ربات دلخواهتون رو بسازید :)
برای خرید سکه دستور /buycoin را بزنید

3⃣:شما میتوانید در کانال ما عضو باشید و از طریق کد هدیه های کانال سکه های خود را افزایش دهید.
Channel : @Taha_Creator
										";
										sm($chatid,$txt,$keyasli);
										}else{
										step($chatid,"gettoken");
										fm($chatid,"-1001387567181",5);
										$txt="
										⭐️لطفا توکن Token دریافتی از BotFather ربات خود را برای ما بفرستید :

👈در نظر داشته باشید تنها خود Token را بفرستید 
👈حتما  فیلم آموزشی داخل کانال را تماشا کنید
تگ #آموزش را در کانال زیر سرچ کنید
Channel : @Taha_Creator
										";
										sm($chatid,$txt,$keyback);
										}
									}elseif($step=="gettoken"){
										if($text=="برگشت↪"){
											step($chatid,"");
											$txt2="به ربات طاها کریتور خوش آمدید🌹

⭐️شما میتوانید ربات دلخواه خودتان را توسط این ربات بسازید .

⭐️شخصی سازی ربات 99% توسط خودتان انجام میشود

⭐️ربات در نسخه های اولیه خود قرار دارد و کم و کاستی های ربات به زودی رفع خواهند شد

⭐️هم اکنون شما میتوانید ربات خودتون رو بسازید 
برای ساخت ربات ابتدا دکمه ی ساخت ربات🔩را بزنید ، سپس توکن دریافتی از @BotFather را برای ما بفرستید .

⭐️اگر در دریافت توکن مشکل دارید ایدی کانال را بزنید .

Channel : @Taha_Creator";
			//$txt="احراز هویت انجام شد✅\n\nهم اکنون شما میتوانید ربات خودتون رو بسازید \nبرای ساخت ربات ابتدا دکمه ی ساخت ربات🔩را بزنید ، سپس توکن دریافتی از @BotFather را برای ما بفرستید : \n\nاگر در دریافت توکن مشکل دارید ایدی کانال را بزنید :";
			sm($chatid,$txt2,$keyasli);
											}else{
												if(preg_match('/^[0-9]+\:[a-zA-Z0-9\_\-]+$/i',$text)){
										$url="https://api.telegram.org/bot$text/getme";	
										$text = str_replace("function","",$text);
										$text = str_replace("json","",$text);
										$text = str_replace("file","",$text);
										$text = str_replace("curl","",$text);
										$text = str_replace("text","",$text);
										$text = str_replace("chat","",$text);
										$text = str_replace("php","",$text);
										$text = str_replace("input","",$text);
										$text = str_replace("(","",$text);
										$text = str_replace(")","",$text);
										$get=json_decode(file_get_contents($url),true);
									//	$j=json_encode($get);
									$ok=$get['ok'];
									if($ok!==true){
										sm($chatid,"توکن شما اشتباه میباشد🚫\n\nلطفا با دقت و بررسی بیشتر توکن را بفرستید\nفیلم آموزشی در کانال موجود هست\nChannel : @Taha_Creator");
										}else{
										$result =$get["result"];
										$username = $result["username"];
										$getbot = getallvalue("amarbot","bot");
										if(in_array($username,$getbot)){
											$txt="
											ربات از قبل ساخته شده است❌

⭐برای ساخت ربات جدید یا از یک توکن جدید استفاده کنید یا هم با دکمه حذف ربات این ربات رو پاک و دوباره اقدام به ساخت ربات کنید

⭐توجه داشته باشید در صورت پاک کردن یک ربات 10 سکه به حساب شما برگشت داده خواهد شد✅

فیلم آموزشی در کانال زیر موجود هست :
Channel : @Taha_Creator
											";
											sm($chatid,$txt);
											}else{
												$txt="درحال ساخت🔩....\n\nلطفا صبر نمایید♻";
												sm($chatid,$txt);
												if(!isetrow("user","keyboard")){
													createrow("user","emtiaz","keyboard","TEXT");
													}
											if(empty(getvalue("user","chatid",$chatid,"keyboard"))){
											$keytest='[{"text":""}],[{"text":""}]';
											setvalue("user","chatid",$chatid,"keyboard",$keytest);
												}
										
										$insert = "INSERT INTO `qw$chatid`
										(
										bot,
										token
										) VALUES ('$username','$text')";
										mysqli_query($con,$insert);
										
										$key = getvalue("user","chatid",$chatid,"keyboard");
										$insert = "INSERT INTO `amarbot`
										(
										bot,
										token,
										creatorid
										
										)
										VALUES ('$username','$text','$chatid')";
										mysqli_query($con,$insert);
										$sqlq = "CREATE TABLE `dok$username`(
										`dokme` TEXT,
										`nok` TEXT,
										`into` TEXT,
										`type` TEXT,
										`editer` TEXT,
										`dastor` TEXT,
										`text` TEXT,
										`keyboard` TEXT,
										`lockchannel` TEXT,
										`lockzirmaj` TEXT,
										`lockcoin` TEXT,
										`textlock` TEXT,
										`textersal` TEXT,
										`textresid` TEXT,
										`user` TEXT,
										`tedad` INT,
										`other` TEXT,
										`other2` TEXT
										)";
										mysqli_query($con,$sqlq);
$sql = "CREATE TABLE `data$username`
(
`id` INT,
startmessage TEXT,
txtback TEXT,
textnewozv TEXT,
textzirmaj TEXT,
eshtebah TEXT,
startpanel TEXT,
keygroup TEXT,
newozv TEXT,
deletelink TEXT,
lockjoin TEXT,
botpower TEXT,
`sendzirmaj` TEXT,
PRIMARY KEY(id),
textpower TEXT,
`keyboard` TEXT,
`other` TEXT,
`other1` TEXT
)";
mysqli_query($con,$sql);

$sqlite = "CREATE TABLE `admin$username`
(
`chatid` INT
)";
mysqli_query($con,$sqlite);

$sqlite = "CREATE TABLE `chan$username`
(
`user` TEXT,
`text` TEXT
)";
mysqli_query($con,$sqlite);


$sqlite = "CREATE TABLE `dayamar$username`
(
`day` INT,
PRIMARY KEY(day),
`amar` INT
)";
mysqli_query($con,$sqlite);
$sql ="INSERT INTO `dayamar$username`(
`day`,
`amar`

) VALUES('".date("d")."','0')";
mysqli_query($con,$sql);
$sql = "CREATE TABLE `user$username` 
 ( 
 chatid INT,
firstname TEXT,
lastname TEXT,
username TEXT,
userid INT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
step TEXT,
code TEXT,
`coding` INT,
zirmaj INT,
emtiaz INT,
PRIMARY KEY(chatid),
Other TEXT,
Other2 TEXT,
Other3 TEXT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `group$username`
( 
chatid INT,
gpname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
PRIMARY KEY(chatid),
Other INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `channel$username` 
 ( 
 chatid TEXT,
chname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
Other INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `supergroup$username` 
 ( 
 chatid INT,
gpname TEXT,
username TEXT,
joindatesh CHAR(15),
joindatem CHAR(15),
jointime CHAR(15),
PRIMARY KEY(chatid),
Other INT
)";
mysqli_query($con,$sql);

$sql = "CREATE TABLE `blocklist$username` 
 ( 
 chatid INT
)";
mysqli_query($con,$sql);
 

$start = "سلام به ربات من خوش آمدید❤";
$textback = "به عقب برگشتید";
$Eshtebah ="این دستور وجود ندارد!!!";
$newozv = "سلام به گروه خوش آمدید❤";
$adminstart ="سلام مدیر به قسمت مدیریت ربات خوش آمدید🌹\n\n⭐️شما میتوانید از طریق دکمه های زیر ربات خود را مدیریت کنید .\n\n⭐️اگر در روند ویرایش ربات مشکلی داشتید میتوانید به کانال ما مراجعه کنید .\n\n⭐️درصورت تست ربات بصورت کاربر از دستور /start یا از دکمه خروج از پنل استفاده کنید.";
$textpower ="ربات خاموش میباشد🛂";
$textzirmaj = "یک زیرمجموعه به شما اضافه شد💥";
$sql = "INSERT INTO `data$username`
(
`id`,
`startmessage`,
`txtback`,
`textnewozv`,
`eshtebah`,
`startpanel`,
`keygroup`,
`newozv`,
`deletelink`,
`lockjoin`,
`botpower`,
`textpower`,
`textzirmaj`,
`sendzirmaj`
)
VALUES ('1','$start','$textback','$newozv','$Eshtebah','$adminstart','off','on','off','off','off','$textpower','$textzirmaj','off')";
mysqli_query($con,$sql);
											$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$username.'"}],[{"text":""}]',$key);
											setvalue("user","chatid",$chatid,"keyboard",$newdokme);
											
											$co1 = getCoin($chatid);
											$co2 = $co1 - 500;
											setCoin($chatid,$co2);
											$file=file_get_contents("Haji.php");
											$strin=file_get_contents("String.php");
											$ch = str_replace("USERBOT",$username,$file);
											$ch1 = str_replace("APITOKENBOT","$text",$ch);
											$ch2 = str_replace("ADMINBOT",$chatid,$ch1);
											if(!file_exists("BotList")){
											mkdir("BotList");		
													}
													if(!file_exists("BotList/$username")){
											mkdir("BotList/$username");		
													}
													file_put_contents("BotList/$username/$username.php",$ch2);
													file_put_contents("BotList/$username/String.php",$strin);
													file_get_contents("$railway_base/BotList/$username/String.php");
													$url2="$railway_base/BotList/$username/$username.php";
													$url3="https://api.telegram.org/bot$text/setwebhook?url=$url2";
													file_get_contents($url3);
													$txr="
												ربات شما با موفقیت ساخته شد✅


⭐️برای رفتن به پنل کاربری اول دستور /start را ارسال و سپس دکمه ی ورود به پنل 🔩 را بزنید

⭐️اگر مشکلی یا باگی در روند کار شما پیش اومد حتما به ما اطلاع دهید و با دستور /start کار خود را از اول انجام دهید 


⭐️کلیپ های آموزشی در کانال موجود هست
Channel : @Taha_Creator	
													";
													$tvt= urlencode($txr);
													file_get_contents("https://api.telegram.org/bot$text/sendmessage?chat_id=$chatid&text=$tvt");
													$ttb ="
												ربات شما با ایدی @$username ساخته شد✅

⭐️توکن شما :
$text

🌟مقدار 500 سکه با ساخت ربات از حساب شما کم شد.

⭐️اگر اقدام به حذف ربات نمایید 10 سکه به حساب شما برگشت داده خواهد شد.

⭐️برای شروع رباتتون دستور /start را ارسال کنید.

⭐️کلیپ های آموزشی در کانال موجود هست
Channel : @Taha_Creator	
													";
													step($chatid,"");
													sm($chatid,$ttb,$keyasli);
													sm("-1001578844277","یک ربات ساخته شد✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\n@$username\n\nToken :\n$text");
											}
										}
										}
											}
										}elseif($text=="انتقال سکه↔"){
											if(getCoin($chatid) <= 150){
												$txt = "سکه های شما برای انتقال کافی نمیباشد⛔\n\nسکه ها برای انتقال باید بالاتر از 150 سکه باشند";
												sm($chatid,$txt);
												}else{
													step($chatid,"send1");
													$co1 = getCoin($chatid);
													$co2 = $co1 - 150;
													$txt = "💠لطفا تعداد سکه ای که میخواهید انتقال دید را وارد کنید :\n\n⚠توجه کنید که 150 سکه در حساب شما نگهداری میشود\n\nتعداد سکه ی های شما : $co1\nتعداد سکه های قابل انتقال : $co2";
													sm($chatid,$txt,$keyback);
}
											}elseif($step=="send1"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
														if(preg_match('/^([0-9]+)$/',$text)){
															$co1 = getCoin($chatid);
															if($text <= $co1){
													$co2 = $co1 - 150;
													if($text <= $co2 ){
														step($chatid,"send2");
														setOther2($chatid,$text);
														$txt="🌟لطفا ایدی عددی شخص را وارد کنید :\n\n⚠️توجه کنید شخص باید در ربات عضو باشد!!";
														sm($chatid,$txt,$keyback);
														}else{
															$txt="⛔خطا\n\n⚠150 سکه باید در حساب شما باقی بماند!!";
																sm($chatid,$txt);
															}
															}else{
																$txt="⛔خطا\n\n⚠مقدار سکه ی شما بیشتر از درخواست شما میباشد!!!";
																sm($chatid,$txt);
																}
															}else{
																$txt="⛔خطا\n\n⚠ورودی فقط عدد قابل قبول هست";
																sm($chatid,$txt);
																}
														}
}elseif($step=="send2"){
	if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
														if(preg_match('/^([0-9]+)$/',$text)){
															if(!empty(getvalue("user","chatid",$text,"chatid"))){
																 $coin = getOther2($chatid);
																$co1 = getCoin($chatid);
											$co2 = $co1 - $coin;
											setCoin($chatid,$co2);
											$c1 = getCoin($text);
											$c2 = $c1 + $coin;
											setCoin($text,$c2);
											step($chatid,"");
											$txt="انتقال برای $text به مقدار $coin سکه با موفقیت انجام شد✅";
											sm($chatid,$txt,$keyasli);
											$txt="مقدار $coin سکه با موفقیت از طرف $chatid برای شما انتقال داده شد✅";
											sm($text,$txt,$keyasli);
																}else{
																	$txt="⛔خطا\n\n⚠این ایدی عددی جزو کاربران ربات نیست";
																sm($chatid,$txt);
																	}
															
															}else{
																$txt="⛔خطا\n\n⚠ورودی فقط ایدی عددی قابل قبول هست";
																sm($chatid,$txt);
																}
															}
	}elseif($text=="پاکسازی اپدیت های در حال انتظار☢️"){
$coun = count(getallvalue("qw$chatid","bot"));
											if($coun== 0 || empty(getallvalue("qw$chatid","bot"))){
											$txt="شما هنوز رباتی نساخته اید❌

⭐️با دستور /newbot اقدام به ساخت ربات کنید

⭐️کلیپ های آموزشی در کانال موجود میباشد
Channel : @Taha_Creator";
sm($chatid,$txt);
												}else{
													step($chatid,"paksazibot");
												$txt="⭐️اگر ربات شما به علتی هنگ کرده یا به دستورات شما جواب نمیده میتونید از این قسمت این مشکل را رفع کنید


لطفا ربات خود را از روی کیبورد انتخاب کنید :";
												sm($chatid,$txt,$keykarbar);
												}

}elseif($step=="paksazibot"){
if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
														sm($chatid,"لطفا صبر کنید...");
													if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
														$token = getvalue("qw$chatid","bot",$text,"token");
														$one=json_decode(file_get_contents("https://api.telegram.org/bot$token/getwebhookinfo"),true);
														$url = $one["result"]["url"];
														$panding = $one["result"]["pending_update_count"];
														$two =file_get_contents("https://api.telegram.org/bot$token/setwebhook?drop_pending_updates=true");
														$three = json_decode(file_get_contents("https://api.telegram.org/bot$token/setwebhook?url=$url"),true);
														$four=json_decode(file_get_contents("https://api.telegram.org/bot$token/getwebhookinfo"),true);
														$panding2 = $four["result"]["pending_update_count"];
														$txt="اپدیت های در حال انتظار پاک شدند✅

اپدیت های درحال انتظار قبلی : $panding

اپدیت های درحال انتظار الان : $panding2


اگر همچنان اپدیت های در حال انتظار صفر نشدند ، دوباره امتحان کنید...


برای اینکه نتیجه را در ربات خود ببینید ، /start را در ربات خود وارد کنید";
														sm($chatid,$txt);
}else{
														$txt="لطفا از روی کیبورد انتخاب کنید!!!";
														sm($chatid,$txt);
														}
														}
}elseif($text=="🛍️فروشگاه🛍️"){
	step($chatid,"noforosh");
	$txt = "🌟لطفا انتخاب کنید چه نوع محصولی برای فروش دارید :

1 : فروش ربات : شما میتوانید ربات هایی که با طاها کریتور ساخته اید را در کانال فروشگاه به صورت فروش سکه ی طاها کریتور یا فروش نقد و معاوضه و غیره ، بفروش بگذارید.

2 : شما میتوانید محصولات خود را اعم از فایل ، متن ، عکس ، فیلم و غیره را در کانال فروشگاه با سکه طاها کریتور بفروش بگذارید 


⚠️تمام محتویات در کانال @Taha_Buybot ثبت خواهند شد.";
	sm($chatid,$txt,$keymahsol);
											
											}elseif($step=="noforosh"){
												if($text=="برگشت↪"){
													step($chatid,"");
													sm($chatid,"به عقب برگشتید :",$keyasli);
												}elseif($text=="فروش ربات"){
													$coun = count(getallvalue("qw$chatid","bot"));
											if($coun== 0 || empty(getallvalue("qw$chatid","bot"))){
											$txt="شما هنوز رباتی نساخته اید❌

⭐️با دستور /newbot اقدام به ساخت ربات کنید

⭐️کلیپ های آموزشی در کانال موجود میباشد
Channel : @Taha_Creator";
sm($chatid,$txt,$keyasli);
												}else{
													step($chatid,"foroshbot");
												$txt="
											⭐️قصد فروش کدام یک از ربات های زیر را دارید؟!!!

🔴لطفا از روی کیبورد انتخاب کنید :	
												";
												sm($chatid,$txt,$keykarbar);
												}
													}elseif($text=="فروش یک محصول"){
														step($chatid,"mahsol1");
														$txt="🌟لطفا محصولی که میخواهید بفروشید را ارسال کنید :

⚠️محصول شما میتواند شامل متن ، فایل ، عکس ، موزیک ، ویدیو بهمراه کپشن باشد.

⚠️لطفا محصول خود را در یک محتوا ارسال کنید، اگر محصول شما زیاد هست لطفا فایل را بصورت فشرده بفرستید";
														sm($chatid,$txt,$keyback);
														}
												}elseif($step=="mahsol1"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
														$txt="🌟لطفا توضیحاتی که میخواهید درمورد محصول برای کاربر نمایش داده شود را بفرستید :

⚠️بیشتر از پونصد کاراکتر نمیتوانید بفرستید .";

														if(isset($text)){
															setvalue("user","chatid",$chatid,"Other","text:::$text");
															step($chatid,"mahsol2");
															sm($chatid,$txt,$keyback);
															}elseif(isset($photo)){
															setvalue("user","chatid",$chatid,"Other","photo:::$photo");
															if(isset($Message->caption)){
															setvalue("user","chatid",$chatid,"Other1",$caption);
															}else{
																setvalue("user","chatid",$chatid,"Other1",null);
																}
															step($chatid,"mahsol2");
															sm($chatid,$txt,$keyback);
															}elseif(isset($video)){
															setvalue("user","chatid",$chatid,"Other","video:::$video");
															if(isset($Message->caption)){
															setvalue("user","chatid",$chatid,"Other1",$caption);
															}else{
																setvalue("user","chatid",$chatid,"Other1",null);
																}
															step($chatid,"mahsol2");
															sm($chatid,$txt,$keyback);
															}elseif(isset($audio)){
															setvalue("user","chatid",$chatid,"Other","audio:::$audio");
															if(isset($Message->caption)){
															setvalue("user","chatid",$chatid,"Other1",$caption);
															}else{
																setvalue("user","chatid",$chatid,"Other1",null);
																}
															step($chatid,"mahsol2");
															sm($chatid,$txt,$keyback);
															}elseif(isset($document)){
															setvalue("user","chatid",$chatid,"Other","document:::$document");
															if(isset($Message->caption)){
															setvalue("user","chatid",$chatid,"Other1",$caption);
															}else{
																setvalue("user","chatid",$chatid,"Other1",null);
																}
															step($chatid,"mahsol2");
															sm($chatid,$txt,$keyback);
															}else{
																$txt="این نوع فرمت ساپورت نمی شود❌

⚠️محصول شما میتواند شامل متن ، فایل ، عکس ، موزیک ، ویدیو بهمراه کپشن باشد.";
sm($chatid,$txt);
return false;
																}
														}
														}elseif($step=="mahsol2"){
															if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
	if(strlen($text) > 500){
		$txt="متن شما بالاتر از پونصد کاراکتر میباشد❗️\n\n🌟لطفا توضیحات خود را در یک بند و کمتر از پونصد کاراکتر بفرستید";
		sm($chatid,$txt);
		}else{
			setvalue("user","chatid",$chatid,"Other4",$text);
																					step($chatid,"mahsol3");
														$txt="🌟لطفا بنویسید حداکثر چند نفر میتوانند محصول مورد نظر را خریداری کنند :";
														sm($chatid,$txt,$keyback);
		}
														}
													}elseif($step=="mahsol3"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
														if(preg_match('/^[0-9]+$/',$text) && $text > 0 && $text <= 1000){
															setvalue("user","chatid",$chatid,"Other6",$text);
																					step($chatid,"mahsol4");
														$txt="🌟لطفا تعداد سکه برای فروش محصول خود را وارد کنید :


⚠️حداقل 10 و حداکثر 1000000 سکه قابل قبول میباشد.";
														sm($chatid,$txt,$keyback);
															}else{
																$txt="⚠تعداد دانلود باید بصورت عدد و بین 1 تا 1000 باشد!!";
		sm($chatid,$txt);
																}
														}
														}elseif($step=="mahsol4"){
															$bot = getvalue("user","chatid",$chatid,"Other");
															if($text=="برگشت↪"){
																step($chatid,"");
																$textback="⭐️عملیات لغو شد و به منوی اصلی برگشتید.

⭐️چکاری میخواهید انجام بدید : ";
																sm($chatid,$textback,$keyasli);
																}elseif(preg_match('/^[0-9]+$/',$text) && $text >= 10 && $text <= 1000000){
									setvalue("user","chatid",$chatid,"Other5",$text);
											step($chatid,"mahsol7");
														$txt="⚠️آیا برای ثبت محصول مطمعن هستید ؟!!!";
														sm($chatid,$txt,$keynoyes);
										}else{
										$txt="قیمت محصول نامعتبر هست⛔\n\n⚠قیمت محصول باید بین 10 الی 1000000 سکه باشد!!";
										sm($chatid,$txt);
										}
}elseif($step=="mahsol7"){
															if($text=="خیر🚫"){
																step($chatid,"");
																$textback="⭐️عملیات لغو شد و به منوی اصلی برگشتید.

⭐️چکاری میخواهید انجام بدید : ";
																sm($chatid,$textback,$keyasli);
																}elseif($text=="بله✅"){
																	
																	$time=date("h:i:s");
																	
					$file = getvalue("user","chatid",$chatid,"Other");
					$exp = explode(":::",$file);
					$fileid = $exp[1];
					$typem = $exp[0];
					$cap = getvalue("user","chatid",$chatid,"Other1");
					if(empty($cap)){
						$cap = null;
						}
					$tedad = getvalue("user","chatid",$chatid,"Other6");
					$tozihat = getvalue("user","chatid",$chatid,"Other4");
					$price = getvalue("user","chatid",$chatid,"Other5");
					$rand = random(10);
					$sql = "INSERT INTO `foroshmahsol`(
					`id`,
sellerid,
userid,
`date`,
`time`,
`tozihat`,
`type`,
`caption`,
`file`,
`price`,
Other
) VALUES('$rand','$chatid','@$username','$datesh','$time','$tozihat','$typem','$cap','$fileid','$price','$tedad')";
mysqli_query($con,$sql);

					$txt="
🌟فروشنده : $chatid

🌟یوزرنیم : @$username

🌟منشن : <a href='tg://openmessage?user_id=$chatid'>$firstname</a>

★★★★★★★★★★★★★★

💥توضیحات فایل :
$tozihat

💥حداکثر تعداد دانلود : $tedad

★★★★★★★★★★★★★★

💰قیمت فایل : $price
";
$key=json_encode([
'inline_keyboard'=>[
[["text"=>"تایید کردن✅",'callback_data'=>"taiid-+-$rand"]],
[["text"=>"رد کردن⛔","callback_data"=>"rad-+-$rand"]],
]
]);
sm("-1001520926505",$txt,$key);
if($typem=="text"){
	sm("-1001520926505",$fileid);
	}elseif($typem=="photo"){
	sp("-1001520926505",$fileid,$cap);
	}elseif($typem=="video"){
	sv("-1001520926505",$fileid,$cap);
	}elseif($typem=="document"){
	sd("-1001520926505",$fileid,$cap);
	}elseif($typem=="audio"){
	sa("-1001520926505",$fileid,$cap);
	}
step($chatid,"");

sm($chatid,"🌟محصول شما پس از تایید مدیران در کانال ارسال میشود

🔴به منوی اصلی برگشتید :",$keyasli);
																	}
															}

elseif($step=="foroshbot"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
													if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
														$day= date("d");
														if(!empty(getvalue("foroshgah","bot",$text,"forosh$day")) && getvalue("foroshgah","bot",$text,"forosh$day")==$text){
															$txt="❌شما امروز این ربات را تبلیغ کردید

🔰شما هر ربات رو میتوانید یک روز در کانال به فروش بگذارید";
															sm($chatid,$txt);
															}else{
																$yes=date('d',strtotime("-1 days"));
																deleterow("foroshgah","forosh$yes");
															
																	$sql ="REPLACE INTO `foroshgah`(`bot`,`price`,`tozih`)VALUES ('$text','a','b')";
																	mysqli_query($con,$sql);
																	
																setvalue("user","chatid",$chatid,"Other",$text);
																step($chatid,"foroshbot2");
											$txt="⭐️حالا یه توضیح مختصر درباره ی ربات بنویسید :

❗️بیشتر از پونصد کاراکتر نمیتوانید بزارید :";
														sm($chatid,$txt,$keyback);
														}
														}else{
														$txt="لطفا از روی کیبورد انتخاب کنید!!!";
														sm($chatid,$txt);
														}
												
												}
												}
													elseif($step=="foroshbot2"){
															$bot = getvalue("user","chatid",$chatid,"Other");
													if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
	if(strlen($text) > 500){
		$txt="متن شما بالاتر از پونصد کاراکتر میباشد❗️";
		sm($chatid,$txt);
		}else{
			setvalue("foroshgah","bot",$bot,"tozih",$text);
																					step($chatid,"foroshbot3");
														$txt="🔰قیمت فروش را وارد کنید : 

⚠️قیمت فروش باید بین 100 تا 10000000 تومان باشد
همچنین میتوانید از کیبورد زیر هم استفاده نمایید :";
														sm($chatid,$txt,$keybuy);
		}
														}
													}elseif($step=="foroshbot3"){
														$bot = getvalue("user","chatid",$chatid,"Other");
													if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
						if($text=="توافقی"){
							
							setvalue("foroshgah","bot",$bot,"price", "توافقی🤝");
										step($chatid,"foroshbot4");
														$txt="⚠️آیا برای ثبت ربات خود در کانال مطمعن هستید ؟!";
														sm($chatid,$txt,$keynoyes);
							}elseif($text=="فروش با سکه طاها کریتور"){
								step($chatid,"foroshbot6");
														$txt="لطفا تعداد سکه برای فروش ربات خود را وارد کنید :";
														sm($chatid,$txt,$keyback);
}elseif($text=="فقط معاوضه"){
								setvalue("foroshgah","bot",$bot,"price","جهت معاوضه♻️");
								step($chatid,"foroshbot4");
														$txt="⚠️آیا برای ثبت ربات خود در کانال مطمعن هستید ؟!";
														sm($chatid,$txt,$keynoyes);
								}elseif($text=="مجانی"){
										setvalue("foroshgah","bot",$bot,"price","مــجانی🆓");
									step($chatid,"foroshbot4");
														$txt="⚠️آیا برای ثبت ربات خود در کانال مطمعن هستید ؟!";
														sm($chatid,$txt,$keynoyes);
									}elseif($text >=100 && $text <= 10000000){
										$n = number_format($text);
									setvalue("foroshgah","bot",$bot,"price","$n تــومان💵");
											step($chatid,"foroshbot4");
														$txt="⚠️آیا برای ثبت ربات خود در کانال مطمعن هستید ؟!";
														sm($chatid,$txt,$keynoyes);
										}else{
										$txt="❗️قیمت نامعتبر هست

⚠️قیمت فروش باید بین 100 تا 10000000 تومان باشد
همچنین میتوانید از کیبورد زیر هم استفاده نمایید :";
										sm($chatid,$txt);
										}
														}
														}elseif($step=="foroshbot6"){
															$bot = getvalue("user","chatid",$chatid,"Other");
															if($text=="برگشت↪"){
																step($chatid,"");
																$textback="⭐️عملیات لغو شد و به منوی اصلی برگشتید.

⭐️چکاری میخواهید انجام بدید : ";
																sm($chatid,$textback,$keyasli);
																}elseif(preg_match('/^[0-9]+$/',$text) && $text >= 2 && $text <= 1000000){
										$n = number_format($text);
									setvalue("foroshgah","bot",$bot,"price","$n سکه طاها کریتور💰");
									setvalue("user","chatid",$chatid,"Other5",$text);
											step($chatid,"foroshbot7");
														$txt="⚠️آیا برای ثبت ربات خود در کانال مطمعن هستید ؟!";
														sm($chatid,$txt,$keynoyes);
										}else{
										$txt="❗️قیمت نامعتبر هست\n\n⚠قیمت ربات باید بین 2 الی 1000000 سکه باشد!!!";
										sm($chatid,$txt);
										}

}elseif($step=="foroshbot7"){
															if($text=="خیر🚫"){
																step($chatid,"");
																$textback="⭐️عملیات لغو شد و به منوی اصلی برگشتید.

⭐️چکاری میخواهید انجام بدید : ";
																sm($chatid,$textback,$keyasli);
																}elseif($text=="بله✅"){
																	$day= date("d");
																	$bot = getvalue("user","chatid",$chatid,"Other");
																		$token = getvalue("qw$chatid","bot",$bot,"token");
																$url=json_decode(file_get_contents("https://api.telegram.org/bot$token/getme"));
																	$res = $url->result;
																	$name = $res->first_name;
																	$user = $res->username;
																	$time=date("h:i:s");
																	$amar= count(getallvalue("user$bot","chatid"));
																	$gl = getallvalue("dok$bot","dokme");
//$cv =json_encode($gl);
					$dok=count($gl);
					$coinall = getvalue("user","chatid",$chatid,"Other5");
																	$price = getvalue("foroshgah","bot",$bot,"price");
																	$tozi = getvalue("foroshgah","bot",$bot,"tozih");
																	createrow("foroshgah","price","forosh$day","TEXT");
																	setvalue("foroshgah","bot",$bot,"forosh$day",$bot);
																			$txt="<b>$time - $datesh</b>

🤖<b>نام ربات</b> : $name

🆔<b>یوزرنیم ربات </b>: @$user

👨‍🔧 <b>سازنده ربات </b> : <a href='tg://user?id=$chatid'>$firstname</a>

★★★★★★★★★★★★★★★★★

🔩<b>تعداد دکمه ها </b> : $dok

👤<b> آمار ربات </b> : $amar

🔰<b>توضیحات</b> :
$tozi

★★★★★★★★★★★★★★★★★

💰<b>قیمت</b> : $price


📩برای چت و بازدید ربات از دکمه های زیر استفاده کنید📩

";
$key=json_encode([
'inline_keyboard'=>[
[["text"=>"💰خرید مستقیم ربات💰",'callback_data'=>"buy-$user-+-$coinall-+-$chatid"]],
[["text"=>"🤖ورود به ربات🤖","url"=>"https://t.me/$user"],["text"=>"👤ارسال به ادمین👤","url"=>"tg://openmessage?id=$chatid"]],
]
]);
sm("-1001479705339",$txt,$key);
step($chatid,"");

sm($chatid,"⭐️ربات شما با موفقیت در کانال فروشگاه برای تبلیغ پست شد.

🔴به منوی اصلی برگشتید :",$keyasli);
																	}
															}elseif($step=="foroshbot4"){
															if($text=="خیر🚫"){
																step($chatid,"");
																$textback="⭐️عملیات لغو شد و به منوی اصلی برگشتید.

⭐️چکاری میخواهید انجام بدید : ";
																sm($chatid,$textback,$keyasli);
																}elseif($text=="بله✅"){
																	$day= date("d");
																	$bot = getvalue("user","chatid",$chatid,"Other");
																		$token = getvalue("qw$chatid","bot",$bot,"token");
																$url=json_decode(file_get_contents("https://api.telegram.org/bot$token/getme"));
																	$res = $url->result;
																	$name = $res->first_name;
																	$user = $res->username;
																	$time=date("h:i:s");
																	$amar= count(getallvalue("user$bot","chatid"));
																	$gl = getallvalue("dok$bot","dokme");
//$cv =json_encode($gl);
					$dok=count($gl);
																	$price = getvalue("foroshgah","bot",$bot,"price");
																	$tozi = getvalue("foroshgah","bot",$bot,"tozih");
																	createrow("foroshgah","price","forosh$day","TEXT");
																	setvalue("foroshgah","bot",$bot,"forosh$day",$bot);
																			$txt="<b>$time - $datesh</b>

🤖<b>نام ربات</b> : $name

🆔<b>یوزرنیم ربات </b>: @$user

👨‍🔧 <b>سازنده ربات </b> : <a href='tg://user?id=$chatid'>$firstname</a>

★★★★★★★★★★★★★★★★★

🔩<b>تعداد دکمه ها </b> : $dok

👤<b> آمار ربات </b> : $amar

🔰<b>توضیحات</b> :
$tozi

★★★★★★★★★★★★★★★★★

💰<b>قیمت</b> : $price


📩برای چت و بازدید ربات از دکمه های زیر استفاده کنید📩

";
$key=json_encode([
'inline_keyboard'=>[
[["text"=>"🤖ورود به ربات🤖","url"=>"https://t.me/$user"],["text"=>"👤ارسال به ادمین👤","url"=>"tg://openmessage?id=$chatid"]],
]
]);
sm("-1001479705339",$txt,$key);
step($chatid,"");

sm($chatid,"⭐️ربات شما با موفقیت در کانال فروشگاه برای تبلیغ پست شد.

🔴به منوی اصلی برگشتید :",$keyasli);
																	}
															}
elseif($text=="انتقال مالکیت💠"){
												$coun = count(getallvalue("qw$chatid","bot"));
											if($coun== 0 || empty(getallvalue("qw$chatid","bot"))){
											$txt="شما هنوز رباتی نساخته اید❌

⭐️با دستور /newbot اقدام به ساخت ربات کنید

⭐️کلیپ های آموزشی در کانال موجود میباشد
Channel : @Taha_Creator";
sm($chatid,$txt);
												}else{
													step($chatid,"entqal1");
												$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید";
												sm($chatid,$txt,$keykarbar);
												}
											}elseif($step=="entqal1"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
													if(!empty(getvalue("qw$chatid",bot,$text,"token"))){
														
															setvalue("user","chatid",$chatid,"Other",$text);
														step($chatid,"entqal2");
														$txt="لطفا ایدی عددی فردی که میخواهید ربات را به اسم آن ثبت نمایید را وارد کنید\n\n🚫توجه داشته باشید بعد از ارسال ایدی عددی ربات به ایدی طرف مقابل انتقال می یابد و شما دیگر دسترسی به ربات ندارید⛔";
														sm($chatid,$txt,$keyback);
														}else{
														$txt="لطفا از روی کیبورد انتخاب کنید!!!";
														sm($chatid,$txt);
														}
												
												}
												}elseif($step=="entqal2"){
														if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
													if($text==$chatid){
														sm($chatid,"این که ایدی خودته !!!!");
														return false;
														}
			if(empty(getvalue("user","chatid",$text,"chatid"))){
				$txt="🚫این ایدی عددی جزو کاربران ربات طاها کریتور نیست\n\n⭐توجه داشته باشید طرف مقابل حتما باید در ربات طاها کریتور ثبت نام کرده باشد.";
				sm($chatid,$txt);
				}else{
					sm($chatid,"لطفا صبر کنید...\n\n♻در حال انتقال");
		$usern =	getvalue("user","chatid",$chatid,"Other");
				$file =file_get_contents("BotList/$usern/$usern.php");
				$file2 = preg_replace('/(\$admin = ")(.*)(";)/','$admin = "'.$text.'";',$file);
				file_put_contents("BotList/$usern/$usern.php",$file2);
				if(empty(getvalue("user","chatid",$text,"keyboard"))){
											$keyst='[{"text":""}],[{"text":""}]';
											setvalue("user","chatid",$text,"keyboard",$keyst);
											}
									$token =	getvalue("qw$chatid","bot",$usern,"token");
									$keysha = getvalue("user","chatid",$chatid,"keyboard");
		$cc = str_replace('[{"text":"'.$usern.'"}],',"",$keysha);
		setvalue("user","chatid",$chatid,"keyboard",$cc);
	
								
										$key1 = getvalue("user","chatid",$text,"keyboard");
											$newdokme = str_replace(',[{"text":""}]',',[{"text":"'.$usern.'"}],[{"text":""}]',$key1);
											setvalue("user","chatid",$text,"keyboard",$newdokme);
											$sql ="INSERT INTO `qw$text`(`bot`,`token`)VALUES ('$usern','$token')";
											mysqli_query($con,$sql);
					
									deletevalue("qw$chatid","bot",$usern);
											step($chatid,"");
												sm($chatid,"ربات با موفقیت به کاربر $text انتقال یافت✅\n\nبه منوی اصلی برگشتید :",$keyasli);
												sm("-1001578844277","یک ربات انتقال مالکیت یافت✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\nSendFor :$text\n\nUsername : $usern\n\nToken :\n$token");
									
												
				}
													}
													}
											elseif($text=="اپدیت ربات🆙"){
												
											$coun = count(getallvalue("qw$chatid","bot"));
											if($coun== 0 || empty(getallvalue("qw$chatid","bot"))){
											$txt="شما هنوز رباتی نساخته اید❌

⭐️با دستور /newbot اقدام به ساخت ربات کنید

⭐️کلیپ های آموزشی در کانال موجود میباشد
Channel : @Taha_Creator";
sm($chatid,$txt,$keyasli);
												}else{
													step($chatid,"updatebot");
												$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید";
												sm($chatid,$txt,$keykarbar);
												}
											}elseif($step=="updatebot"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
													if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
														$token = getvalue("qw$chatid","bot",$text,"token");
														$file=file_get_contents("Haji.php");
														$ch = str_replace('= "USERBOT','= "'.$text,$file);
														$ch1 = str_replace(', "APITOKENBOT',', "'.$token,$ch);
												$ch2 = str_replace('= "ADMINBOT','= "'.$chatid,$ch1);
													file_put_contents("BotList/$text/$text.php",$ch2);
													step($chatid,"");
												sm($chatid,"ربات شما با موفقیت آپدیت شد✅",$keyasli);
												sm("-1001578844277","یک ربات آپدیت شد✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\n@$text\n\nToken :\n$token");
														}else{
														$txt="لطفا از روی کیبورد انتخاب کنید!!!";
														sm($chatid,$txt);
														}
													}
													}elseif($text=="تنظیم وبهوک اتوماتیک🎫"){
												
											$coun = count(getallvalue("qw$chatid","bot"));
											if($coun== 0 || empty(getallvalue("qw$chatid","bot"))){
											$txt="شما هنوز رباتی نساخته اید❌

⭐️با دستور /newbot اقدام به ساخت ربات کنید

⭐️کلیپ های آموزشی در کانال موجود میباشد
Channel : @Taha_Creator";
sm($chatid,$txt,$keyasli);
												}else{
													step($chatid,"setwebhookbot");
												$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید";
												sm($chatid,$txt,$keykarbar);
												}
											}elseif($step=="setwebhookbot"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
													if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
														$token = getvalue("qw$chatid","bot",$text,"token");
														file_get_contents("https://api.telegram.org/bot$token/setwebhook?url=$railway_base/BotList/$text/$text.php");
												sm($chatid,"ربات شما با موفقیت تنظیم وبهوک شد✅\n\n⚠این قسمت در حال ارتقا می باشد و بزودی امکانات بیشتری در اختیار شما قرار میگیرد...",$keyasli);
												sm("-1001578844277","یک ربات تنظیم وبهوک اتوماتیک شد✅\n\nCreator : <a href='tg://user?id=$fromid'>$firstname</a>\nCreator ID : $fromid\n\n@$text\n\nToken :\n$token");
														}else{
														$txt="لطفا از روی کیبورد انتخاب کنید!!!";
														sm($chatid,$txt);
														}
													}
													}elseif($text=="حذف ربات🚮"){
																$coun = count(getallvalue("qw$chatid","bot"));
											if($coun== 0 || empty(getallvalue("qw$chatid","bot"))){
												$txt="شما هنوز رباتی نساخته اید❌

⭐️با دستور /newbot اقدام به ساخت ربات کنید

⭐️کلیپ های آموزشی در کانال موجود میباشد
Channel : @Taha_Creator";
sm($chatid,$txt);
												}else{
													step($chatid,"deletebot");
												$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید";
												sm($chatid,$txt,$keykarbar);
												}
												}elseif($step=="deletebot"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
													if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
														setvalue("user","chatid",$chatid,"Other",$text);
														step($chatid,"delnoyes");
													$txt="⚠️آیا از حذف ربات مطمعن هستید؟!!!";
													sm($chatid,$txt,$keynoyes);
														}
														}
														}elseif($step=="delnoyes"){
															if($text=="خیر🚫"){
																step($chatid,"");
																sm($chatid,"به منوی اصلی برگشتید ↪",$keyasli);
																}elseif($text=="بله✅"){
																	$user = getvalue("user","chatid",$chatid,"Other");
																	$files = glob("BotList/$user/*"); // get all file names
foreach($files as $file){ // iterate files
  if(is_file($file)) {
    unlink($file); // delete file
  }else{
	rmdir($file);
}
}
rmdir("BotList/$user");
deletevalue("qw$chatid","bot",$user);
deletevalue("amarbot","bot",$user);
	$co1 = getCoin($chatid);
											$co2 = $co1 + 10;
											setCoin($chatid,$co2);
											$key = getvalue("user","chatid",$chatid,"keyboard");
											if(strpos($key,'"text":"'.$user.'"},')){
		$cc = str_replace('{"text":"'.$user.'"},',"",$key);
		$cp = str_replace("[],","",$cc);
		setvalue("user","chatid",$chatid,"keyboard",$cp);
		}else{
		$cc = str_replace('{"text":"'.$user.'"}',"",$key);
		$cp = str_replace("[],","",$cc);
		setvalue("user","chatid",$chatid,"keyboard",$cp);
		}
		$gathash = getallvalue("hashmoh$user","hash");
		foreach($gathash as $mb){
			$sql = "DROP TABLE `moh$mb$user`";
							mysqli_query($con,$sql);
			}
			$sql = "DROP TABLE `hashmoh$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `dok$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `user$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `admin$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `data$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `code$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `chan$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `dayamar$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `eshtrak$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `filter$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `pasokh$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `channel$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `mohtava$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `eshtrak$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `fileid$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `datatype$user`";
							mysqli_query($con,$sql);
							$sql = "DROP TABLE `datalist$user`";
							mysqli_query($con,$sql);
							$drop = "DROP TABLE `dok$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `delete$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `eshtrak$user`";
						    mysqli_query($con,$drop);
							$drop = "DROP TABLE `fileid$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `hash$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `datatype$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `datalist$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `replac$user`";
							mysqli_query($con,$drop);
							$all=getallvalue("hashmoh$text",'hash');
									foreach($all as $xb){
										$drop = "DROP TABLE `mohtava$xb$user`";
															mysqli_query($con,$drop);
													}
										$all=getallvalue("hash$user",'hash');
									foreach($all as $xb){
										$drop = "DROP TABLE `mohtava$xb$user`";
															mysqli_query($con,$drop);
										}
							$drop = "DROP TABLE `hashmoh$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `filter$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `del$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `code$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `moh$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `channel$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `robot$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `data$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `pasokh$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `user$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `admin$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `chan$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `group$user`";
							mysqli_query($con,$drop);
							$drop = "DROP TABLE `supergroup$user`";
							mysqli_query($con,$drop);
															
							deletevalue("copycode","userbot",$user);
							
		step($chatid,"");
		sm($chatid,"ربات شما با موفقیت حذف شد✅\n\n💰مقدار 10 سکه به حساب شما بازگشت داده شد\n\n⭐در کانال ربات جوین شو : \nChannel : @Taha_Creator",$keyasli);
																	}
															
															}elseif($text=="ربات های من🤖"){
												$coun = count(getallvalue("qw$chatid","bot"));
											if($coun== 0 || empty(getallvalue("qw$chatid","bot"))){
											$txt="شما هنوز رباتی نساخته اید❌

⭐️با دستور /newbot اقدام به ساخت ربات کنید

⭐️کلیپ های آموزشی در کانال موجود میباشد
Channel : @Taha_Creator";
sm($chatid,$txt);
												}else{
													step($chatid,"myrobots");
												$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید";
												sm($chatid,$txt,$keykarbar);
												}
												}elseif($step=="myrobots"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
													if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
														$token = getvalue("qw$chatid","bot",$text,"token");
															$url="https://api.telegram.org/bot$token/getme";	
										$get=json_decode(file_get_contents($url),true);
									$ok=$get['result'];
									$idbot = $ok['id'];
									$fnbot = $ok['first_name'];
									$join = $ok['can_join_groups'];
									$read = $ok['can_read_all_group_messages'];
									$sup = $ok['supports_inline_queries'];
									if($join == true){
										$join1 = "فعال✅";
										}else{
										$join1 = "غیرفعال🚫";
										}
										if($read == true){
										$read1 = "فعال✅";
										}else{
										$read1 = "غیرفعال🚫";
										}
										if($sup == true){
										$sup1 = "فعال✅";
										}else{
										$sup1 = "غیرفعال🚫";
										}
										$count = amarcount("user$text");
									$txt="⭐️مشخصات ربات $text :

💠نام ربات : $fnbot

💠ایدی ربات : $idbot

💠امکان اضافه شدن در گروه : $join1

💠امکان خواندن پیام های گروه : $read1

💠ساپورت اینلاین : $sup1

💠تعداد کاربران ربات : $count";
					sm($chatid,$txt);						
																	}
														}
												}elseif($text=="♻️تغییر توکن"){
											$coun = count(getallvalue("qw$chatid","bot"));
											if($coun== 0 || empty(getallvalue("qw$chatid","bot"))){
											$txt="شما هنوز رباتی نساخته اید❌

⭐️با دستور /newbot اقدام به ساخت ربات کنید

⭐️کلیپ های آموزشی در کانال موجود میباشد
Channel : @Taha_Creator";
sm($chatid,$txt);
												}else{
													step($chatid,"Tokenbot");
												$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید";
												sm($chatid,$txt,$keykarbar);
												}
												}elseif($step=="Tokenbot"){
												if($text=="برگشت↪"){
													step($chatid,"");
													$txt="به منوی اصلی برگشتید↪:";
													sm($chatid,$txt,$keyasli);
													}else{
												if(!empty(getvalue("qw$chatid","bot",$text,"token"))){
											//			$token = getvalue("qw$chatid","bot",$text,"token");
													setvalue("user","chatid",$chatid,"Other",$text);
													$txt="⭐خوب حالا توکن جدید رباتتون رو برای ما بفرستید \n\n⭐تغییر توکن درصورتی امکان پذیر هست که توکن شما فقط ریووک شده باشد!!!\n\n⭐کلیپ های آموزشی در کانال موجود هست\nChannel : @Taha_Creator";
									step($chatid,"Tokenbot2");
									sm($chatid,$txt,$keyback);
														}
														}
														}elseif($step=="Tokenbot2"){
															if($text=="برگشت↪"){
																step($chatid,"Tokenbot");
																		$txt="لطفا ربات خود را از روی کیبورد انتخاب نمایید";
												sm($chatid,$txt,$keykarbar);
																}else{
																	$user = getvalue("user","chatid",$chatid,"Other");
																$url="https://api.telegram.org/bot$text/getme";	
										$get=json_decode(file_get_contents($url),true);
									//	$j=json_encode($get);
									$ok=$get['ok'];
									if($ok!==true){
										sm($chatid,"توکن شما اشتباه میباشد🚫\n\nلطفا با دقت و بررسی بیشتر توکن را بفرستید\nفیلم آموزشی در کانال موجود هست\nChannel : @Taha_Creator");
										}else{
										$result =$get["result"];
										$id =$result["username"];
										if($user !==$id or strtolower($user) !== strtolower($id)){
											$txt="این توکن با توکن ربات قبلی ربات تطابق ندارد🚫\n\n⭐لطفا با دقت بیشتر توکن جدید ربات را بفرستید\n\n⭐کلیپ های آموزشی در کانال موجود میباشد : \nChannel : @Taha_Creator";
									sm($chatid,$txt);
													}else{
											setvalue("qw$chatid","bot",$user,"token",$text);
											setvalue("amarbot","bot",$user,"token",$text);
													$file=file_get_contents("Haji.php");
												$ch = str_replace('= "USERBOT','= "'.$user,$file);
														$ch1 = str_replace(', "APITOKENBOT',', "'.$text,$ch);
												$ch2 = str_replace('= "ADMINBOT','= "'.$chatid,$ch1);
													file_put_contents("BotList/$user/$user.php",$ch2);
													$url2="$railway_base/BotList/$user/$user.php";
													$url3="https://api.telegram.org/bot$text/setwebhook?url=$url2";
													file_get_contents($url3);
											$txt="توکن ربات شما با موفقیت آپدیت شد✅\n\nبه منوی اصلی برگشتید :";
											step($chatid,"");
											sm($chatid,$txt,$keyasli);
													}
										}
																}
															
															}elseif($step=="sendall"){
							if($text=="برگشت↪"){
				step($chatid,"");
				sm($chatid,"عملیات لغو شد و به عقب برگشتید↪\n\nچکاری میخواید انجام بدید؟",$keyasli);
				}else{
	step($chatid,"");
	$get=file_get_contents("karbar.txt");
	$exp=explode("\n",$get);
	$count=count($exp);	
		if(!file_exists("foreach.txt")){
		file_put_contents("foreach.txt",0);
}
$gettt = file_get_contents("foreach.txt");
	$xl=0;
	sm($chatid,"لطفا تا اتمام صبر کنید...");
	$mess = $messageid+1;
	$data["messagid"] = $mess;
	unset($data["step$fromid"]);
	file_put_contents("String.json", json_encode($data, 128|256));
	for($x=$gettt;$x<=$count;$x++){
		if($xl==20){
			em($admin,"تا الان برای $x نفر ارسال شده است...\n\nلطفا تا اتمام ارسال صبر کنید.",$data["messagid"]);
	sleep(1);
$xl=0;	
	}
	$xl++;
	$gettt = file_get_contents("foreach.txt");
		if(isset($photo)){
			sp($exp[$x],$photo,$caption,$kei);
			}elseif(isset($video)){
				sv($exp[$x],$video,$caption,$kei);
				}elseif(isset($document)){
					sd($exp[$x],$document,$caption,$kei);
					}elseif(isset($audio)){
						sa($exp[$x],$audio,$caption,$kei);
						}elseif(isset($voice)){
							svo($exp[$x],$voice,$caption,$kei);
							}elseif(isset($sticker)){
								ss($exp[$x],$sticker);
								}elseif(isset($text)){
	sm($exp[$x],$text,$kei);
	$gettt++;
	file_put_contents("foreach.txt",$gettt);
	
}
	}
				sm($chatid,"متن شما با موفقیت برای تمام کاربران ارسال شد✅\n\nبه پنل بازگشتید :",$keyasli);
unlink("foreach.txt");
							}
						}elseif($chatid=="1377243724" or $chatid == 1720440554){
																	if($text=="ارسال کاربران📢"){
						step($chatid,"sendall");
					$txt="
					پیام خود را در بفرستید تا برای کاربران ارسال شود :
			";
					
												sm($chatid,$txt,$keyback);
						}
																elseif($text=="Amar"){
																	$get=file_get_contents("karbar.txt");
																				$exp = explode("\n",$get);
																	$c = count($exp);
																	sm($chatid,$c);
																	}elseif($text=="آمار کلی"){
																		$karbar=count(getallvalue("user","chatid"));
																		$amarbot=count(getallvalue("amarbot","token"));
			$txt="آمار کاربران  : $karbar\nآمار ربات ها : $amarbot";
					sm($chatid,$txt);						
																									}
																}
								
								
?>
