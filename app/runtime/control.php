<?php
namespace Taha;
function tc_say($token,$uid,$text,$keyboard=null){$p=['chat_id'=>$uid,'text'=>$text];if($keyboard!==null)$p['reply_markup']=json_encode($keyboard,JSON_UNESCAPED_UNICODE);return tc_api($token,'sendMessage',$p);}
function tc_state($scope,$uid,$new=null){$key=$scope.':'.$uid;if($new!==null){tc_stmt('REPLACE INTO tc_meta(name,value) VALUES (?,?)',[$key,json_encode($new)]);return $new;}return json_decode(tc_value('SELECT value FROM tc_meta WHERE name=?',[$key])??'{}',true);}
function tc_register_user($u){
 $m=$u->message??null;if(!$m||($m->chat->type??'')!=='private')return;
 tc_stmt('INSERT IGNORE INTO `user` (`chatid`,`userid`,`firstname`,`lastname`,`username`,`emtiaz`,`zirmaj`,`Other3`,`step`,`keyboard`) VALUES (?,?,?,?,?,?,?,?,?,?)',[(string)$m->from->id,(string)$m->from->id,$m->from->first_name??'',$m->from->last_name??'',$m->from->username??'',20,0,1,'','[{"text":""}],[{"text":""}]']);
}
function tc_owner_bots($uid){return tc_rows('SELECT bot,token,creatorid FROM amarbot WHERE creatorid=? ORDER BY bot',[$uid]);}
function tc_refresh_keyboard($uid){
 tc_db()->query('CREATE TABLE IF NOT EXISTS '.tc_ident('qw'.$uid).' (`bot` TEXT, `token` TEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
 tc_stmt('DELETE FROM '.tc_ident('qw'.$uid));$rows=[];
 foreach(tc_owner_bots($uid) as $b){$rows[]=[['text'=>$b['bot']]];tc_stmt('INSERT INTO '.tc_ident('qw'.$uid).' (bot,token) VALUES (?,?)',[$b['bot'],$b['token']]);}
 $rows[]=[['text'=>'']];$serialized=implode(',',array_map(fn($row)=>json_encode($row,JSON_UNESCAPED_UNICODE),$rows));
 tc_stmt('UPDATE `user` SET keyboard=? WHERE chatid=?',[$serialized,$uid]);
}
function tc_generate($bot,$token,$owner){
 if(!preg_match('/^[A-Za-z0-9_]{5,32}$/D',$bot))throw new \RuntimeException('Invalid bot username');
 $dir=tc_root().'/BotList/'.$bot;if(!is_dir($dir)&&!mkdir($dir,0700,true))throw new \RuntimeException('Volume is not writable');
 $code=file_get_contents(TC_APP.'/legacy/Haji.php');
 $code=str_replace(['USERBOT','APITOKENBOT','ADMINBOT'],[$bot,$token,(string)$owner],$code);
 tc_write($dir.'/'.$bot.'.php',$code);
}
function tc_checked($r){if(empty($r->ok))throw new \RuntimeException('Telegram rejected the request ('.($r->error_code??'unknown').').');return $r;}
function tc_new_bot($uid,$token){
 if(in_array($token,[tc_env('CREATOR_TOKEN'),tc_env('MANAGER_TOKEN')],true))throw new \RuntimeException('توکن دو ربات اصلی را نمی‌توان به‌عنوان ربات جدید ثبت کرد.');
 $me=tc_checked(tc_api($token,'getMe'))->result;$bot=$me->username;
 if(tc_value('SELECT COUNT(*) FROM amarbot WHERE bot=?',[$bot]))throw new \RuntimeException('این ربات قبلاً ثبت شده است.');
 tc_schema($bot);tc_generate($bot,$token,$uid);
 $db=tc_db();$db->begin_transaction();
 try{
  $coin=(int)tc_value('SELECT emtiaz FROM `user` WHERE chatid=? FOR UPDATE',[$uid]);
  $cost=(int)tc_env('BOT_COST','500');if($coin<$cost)throw new \RuntimeException('سکه کافی نیست. موجودی لازم: '.$cost);
  tc_stmt('INSERT INTO amarbot (bot,token,creatorid,userid,username) VALUES (?,?,?,?,?)',[$bot,$token,$uid,$me->id,$bot]);
  tc_stmt('DELETE FROM tc_disabled WHERE bot=?',[$bot]);
  tc_checked(tc_webhook($token,$bot));
  tc_stmt('UPDATE `user` SET emtiaz=emtiaz-? WHERE chatid=?',[$cost,$uid]);$db->commit();
 }catch(\Throwable $e){$db->rollback();@unlink(tc_root().'/BotList/'.$bot.'/'.$bot.'.php');throw $e;}
 tc_refresh_keyboard($uid);return $bot;
}
function tc_remove_bot($bot,$uid,$force=false){
 $b=tc_rows('SELECT * FROM amarbot WHERE bot=?',[$bot])[0]??null;
 if(!$b||(!$force&&(string)$b['creatorid']!==(string)$uid))throw new \RuntimeException('ربات متعلق به شما نیست.');
 tc_checked(tc_api($b['token'],'deleteWebhook',['drop_pending_updates'=>true]));
 tc_stmt('REPLACE INTO tc_disabled(bot) VALUES (?)',[$bot]);
 // Database contents are retained for restore; this prevents an accidental destructive loss.
 tc_stmt('DELETE FROM amarbot WHERE bot=?',[$bot]);
 tc_stmt('UPDATE `user` SET emtiaz=COALESCE(emtiaz,0)+10 WHERE chatid=?',[$b['creatorid']]);
 @unlink(tc_root().'/BotList/'.$bot.'/'.$bot.'.php');tc_refresh_keyboard($b['creatorid']);
}
function tc_control($route,$u){
 $m=$u->message??null;$uid=(string)($m->from->id??$u->callback_query->from->id??'');
 $text=$m->text??'';$private=($m->chat->type??'')==='private';
 $token=tc_route_token($route);if(!$token)return true;
 if($route==='manager'){
  if($uid!==tc_env('OWNER_ID')||!$private)return true;
  return tc_manager($token,$uid,$text,$u);
 }
 if($route!=='creator'){
  // Old global bot-creation buttons are kept but routed to the central creator.
  $creation=['دکمه ساخت ربات🤖','دکمه اپدیت ربات♻','دکمه حذف ربات🚯'];
  if($text==='/id' && $private){tc_say($token,$uid,$uid);return true;}
  return false;
 }
 if(!$private && !isset($u->callback_query))return true;
 if(tc_env('PUBLIC_CREATOR','false')!=='true' && $uid!==tc_env('OWNER_ID') && !in_array($uid,array_filter(explode(',',tc_env('ALLOWED_CREATORS'))),true)){
  if($private)tc_say($token,$uid,'این ربات‌ساز فعلاً خصوصی است. شناسهٔ شما: '.$uid);return true;
 }
 if(isset($u->callback_query) && str_starts_with($u->callback_query->data??'', 'buy-'))return tc_market_purchase($token,$u->callback_query);
 if($private)tc_register_user($u);
 if($text==='/id'){tc_say($token,$uid,$uid);return true;}
 if($text==='/help'){tc_say($token,$uid,"/start — منوی اصلی\n/newbot — ساخت ربات\n/mycoin — سکه\n/mylink — لینک دعوت\n/buycoin — ارتباط با پشتیبانی\nربات‌های من، آپدیت، انتقال مالکیت، حذف و تغییر توکن از منوی اصلی در دسترس‌اند.");return true;}
 if($text==='/buycoin'||$text==='💲خرید سکه💲'){tc_say($token,$uid,'برای دریافت سکه با @'.ltrim(tc_env('SUPPORT_USERNAME',tc_env('CREATOR_USERNAME')),'@').' تماس بگیرید. پرداخت خودکار در این نسخه وجود ندارد.');return true;}
 $st=tc_state('creator',$uid);
 if(in_array($text,['/start','/cancel','برگشت↪'],true)){
  tc_state('creator',$uid,[]);tc_stmt('UPDATE `user` SET step=? WHERE chatid=?',['',$uid]);
  if($text!=='/start'){$u->message->text='/start';$GLOBALS['TC_UPDATE']=$u;}return false;
 }
 $actions=['/newbot'=>'new','ساخت ربات🔩'=>'new','اپدیت ربات🆙'=>'update','♻️تغییر توکن'=>'token','انتقال مالکیت💠'=>'transfer','تنظیم وبهوک اتوماتیک🎫'=>'webhook','پاکسازی اپدیت های در حال انتظار☢️'=>'clear','حذف ربات🚮'=>'delete','ربات های من🤖'=>'list'];
 if(isset($actions[$text])){
  $act=$actions[$text];
  if($act==='new'){
   if(!tc_required_channels($token,$uid))return true;
   $cost=(int)tc_env('BOT_COST','500');
   if((int)tc_value('SELECT emtiaz FROM `user` WHERE chatid=?',[$uid])<$cost){tc_say($token,$uid,'سکه کافی نیست. ساخت ربات '.$cost.' سکه لازم دارد.');return true;}
   tc_state('creator',$uid,['act'=>'new']);tc_say($token,$uid,'توکن ربات جدید را از BotFather بگیر و بفرست. برای لغو /cancel');return true;
  }
  $bots=tc_owner_bots($uid);if(!$bots){tc_say($token,$uid,'هنوز رباتی ثبت نکرده‌ای. /newbot');return true;}
  if($act==='list'){tc_say($token,$uid,implode("\n",array_map(fn($b)=>'@'.$b['bot'],$bots)));return true;}
  tc_state('creator',$uid,['act'=>$act,'phase'=>'choose']);tc_say($token,$uid,'یوزرنیم ربات را انتخاب کن. برای لغو /cancel',['keyboard'=>array_merge(array_map(fn($b)=>[['text'=>$b['bot']]],$bots),[[['text'=>'برگشت↪']]]),'resize_keyboard'=>true]);return true;
 }
 if(!isset($st['act']))return false;
 try{
  $act=$st['act'];
  if($act==='new'){
   $bot=tc_new_bot($uid,trim($text));tc_state('creator',$uid,[]);
   if(isset($m->message_id))tc_api($token,'deleteMessage',['chat_id'=>$uid,'message_id'=>$m->message_id]);
   tc_say($token,$uid,'ربات @'.$bot." ثبت شد. وارد آن شو و /start و سپس /panel را بفرست.\nبازگشت به منو: /start");return true;
  }
  if(($st['phase']??'')==='choose'){
   $bot=ltrim(trim($text),'@');$b=tc_rows('SELECT * FROM amarbot WHERE bot=? AND creatorid=?',[$bot,$uid])[0]??null;
   if(!$b){tc_say($token,$uid,'یکی از ربات‌های خودت را انتخاب کن.');return true;}
   if($act==='update'||$act==='webhook'){
    tc_generate($bot,$b['token'],$uid);tc_checked(tc_webhook($b['token'],$bot));tc_say($token,$uid,'انجام شد. /start');tc_state('creator',$uid,[]);return true;
   }
   $st['bot']=$bot;$st['phase']='value';tc_state('creator',$uid,$st);
   tc_say($token,$uid,match($act){'token'=>'توکن جدید همین ربات را بفرست.','transfer'=>'شناسهٔ عددی مالک جدید را بفرست. مالک جدید باید قبلاً ربات‌ساز را استارت کرده باشد.','delete'=>'ربات متوقف و از فهرستت حذف می‌شود؛ داده‌ها برای بازیابی باقی می‌مانند و ۱۰ سکه برمی‌گردد. برای تأیید بفرست: بله✅','clear'=>'پیام‌های پردازش‌نشدهٔ تلگرام و صف محلی این ربات حذف می‌شوند. برای تأیید بفرست: بله✅',default=>'ادامه؟'});return true;
  }
  $bot=$st['bot'];$b=tc_rows('SELECT * FROM amarbot WHERE bot=? AND creatorid=?',[$bot,$uid])[0]??null;
  if(!$b)throw new \RuntimeException('مالکیت ربات تغییر کرده است. /start');
  if($act==='token'){
   $new=trim($text);$me=tc_checked(tc_api($new,'getMe'))->result;
   if(strcasecmp($me->username,$bot)!==0)throw new \RuntimeException('این توکن مربوط به ربات انتخاب‌شده نیست.');
   tc_checked(tc_webhook($new,$bot));tc_stmt('UPDATE amarbot SET token=? WHERE bot=? AND creatorid=?',[$new,$bot,$uid]);tc_generate($bot,$new,$uid);tc_refresh_keyboard($uid);
   tc_api($token,'deleteMessage',['chat_id'=>$uid,'message_id'=>$m->message_id]);
  }elseif($act==='transfer'){
   if(($st['confirm']??false)!==true){
    if(!preg_match('/^[0-9]+$/D',$text)||$text===$uid||!tc_value('SELECT chatid FROM `user` WHERE chatid=?',[$text]))throw new \RuntimeException('شناسهٔ مالک جدید معتبر نیست یا هنوز /start نزده است.');
    $st['newowner']=$text;$st['confirm']=true;tc_state('creator',$uid,$st);tc_say($token,$uid,'مالکیت @'.$bot.' به '.$text.' منتقل شود؟ برای تأیید بفرست: بله✅');return true;
   }
   if($text!=='بله✅'){tc_say($token,$uid,'برای تأیید بله✅ و برای لغو /cancel');return true;}
   $new=$st['newowner'];tc_stmt('UPDATE amarbot SET creatorid=? WHERE bot=? AND creatorid=?',[$new,$bot,$uid]);tc_generate($bot,$b['token'],$new);tc_refresh_keyboard($uid);tc_refresh_keyboard($new);tc_say($token,$new,'مالکیت @'.$bot.' به شما منتقل شد.');
  }elseif($act==='delete'||$act==='clear'){
   if($text!=='بله✅'){tc_say($token,$uid,'برای تأیید بله✅ و برای لغو /cancel');return true;}
   if($act==='delete')tc_remove_bot($bot,$uid);else{tc_checked(tc_webhook($b['token'],$bot,true));tc_stmt("UPDATE tc_queue SET status='discarded',payload=NULL,finished=NOW() WHERE route=? AND status='pending'",[$bot]);}
  }
  tc_state('creator',$uid,[]);tc_say($token,$uid,'انجام شد. /start');
 }catch(\Throwable $e){tc_say($token,$uid,'عملیات انجام نشد: '.tc_brand($e->getMessage())."\nدوباره تلاش کن یا /cancel بفرست.");}
 return true;
}
function tc_manager($token,$uid,$text,$u){
 $s=tc_state('manager',$uid);
 if(in_array($text,['/start','/help','/cancel','/back','/finish'],true)){
  tc_state('manager',$uid,[]);tc_say($token,$uid,"پنل مدیریت مرکزی\n/id: شناسه شما\namar: تعداد کاربران\namarkoli: آمار مجموع ربات‌ها\ngetalllistbot: فهرست ربات‌ها\naddcoin: افزایش/کاهش سکه یک کاربر\nsetallmoney: تعیین سکه همه\nupdate: ثبت توضیحات نسخه\nupdateallbot: بازسازی همه ربات‌ها\ndeletingbot: توقف یک ربات\ndeleteup: پاک‌کردن تاریخچه نسخه\ndeletetable: حذف یک نوع جدول از همه ربات‌ها (با تأیید)\ngetall: فهرست شناسه‌ها\ngetstep / setstep / reset / check / isset: بررسی مرحله کاربر\nlink: لینک شروع\ndice: انیمیشن توپ\n/status: سلامت صف\n/clearfailed: پاک‌کردن متن پیام‌های ناموفق\n/cancel: لغو");return true;
 }
 try{
  if($text==='/id'){tc_say($token,$uid,$uid);return true;}
  if($text==='/status'){$r=tc_rows('SELECT status,COUNT(*) AS n FROM tc_queue GROUP BY status');tc_say($token,$uid,'نسخه: '.TC_VERSION."\n".json_encode($r,JSON_UNESCAPED_UNICODE));return true;}
  if($text==='/clearfailed'){tc_stmt("UPDATE tc_queue SET payload=NULL WHERE status IN ('failed','uncertain')");tc_say($token,$uid,'متن پیام‌های ناموفق پاک شد؛ وضعیت برای بررسی باقی ماند.');return true;}
  if(isset($s['action'])){
   $a=$s['action'];
   if($a==='addcoin'&&!isset($s['id'])){
    if(!preg_match('/^\d+$/D',$text)||!tc_value('SELECT chatid FROM `user` WHERE chatid=?',[$text]))throw new \RuntimeException('کاربر باید ربات‌ساز را استارت کرده باشد.');
    $s['id']=$text;tc_state('manager',$uid,$s);tc_say($token,$uid,'مقدار سکه را بفرست؛ عدد منفی برای کسر.');return true;
   }
   if($a==='addcoin'){
    if(!preg_match('/^-?\d{1,9}$/D',$text))throw new \RuntimeException('عدد صحیح بفرست.');
    tc_stmt('UPDATE `user` SET emtiaz=GREATEST(0,COALESCE(emtiaz,0)+?) WHERE chatid=?',[$text,$s['id']]);
   }elseif($a==='setallmoney'){
    if(!isset($s['amount'])){
     if(!preg_match('/^\d{1,9}$/D',$text))throw new \RuntimeException('عدد نامنفی بفرست.');
     $s['amount']=$text;tc_state('manager',$uid,$s);tc_say($token,$uid,'موجودی همه برابر '.$text.' شود؟ تأیید: بله✅');return true;
    }if($text!=='بله✅')throw new \RuntimeException('تأیید: بله✅؛ لغو: /cancel');tc_stmt('UPDATE `user` SET emtiaz=?',[$s['amount']]);
   }elseif($a==='update'){
    if(!isset($s['version'])){$s['version']=$text;tc_state('manager',$uid,$s);tc_say($token,$uid,'توضیحات نسخه را بفرست. این دستور فقط خبر نسخه ثبت می‌کند؛ کد را از GitHub آپدیت کن.');return true;}
    tc_stmt('INSERT INTO `update` (`noskhe`,`update`) VALUES (?,?)',[$s['version'],$text]);
   }elseif($a==='deleteup'){
    if($text!=='بله✅')throw new \RuntimeException('تأیید: بله✅');tc_stmt('DELETE FROM `update`');
   }elseif($a==='deletingbot'){
    if(!isset($s['bot'])){
     $b=ltrim($text,'@');if(!tc_value('SELECT bot FROM amarbot WHERE bot=?',[$b]))throw new \RuntimeException('ربات ثبت‌شده پیدا نشد.');
     $s['bot']=$b;tc_state('manager',$uid,$s);tc_say($token,$uid,'توقف @'.$b.'؟ تأیید: بله✅');return true;
    }if($text!=='بله✅')throw new \RuntimeException('تأیید: بله✅');tc_remove_bot($s['bot'],$uid,true);
   }elseif($a==='deletetable'){
    $schema=json_decode(file_get_contents(__DIR__.'/schema.json'),true)['child'];
    if(!isset($s['prefix'])){
     if(!isset($schema[$text]))throw new \RuntimeException('نام نوع جدول معتبر نیست.');
     $s['prefix']=$text;tc_state('manager',$uid,$s);tc_say($token,$uid,'این کار دادهٔ جدول '.$text.' را در همهٔ ربات‌ها پاک می‌کند. تأیید: بله✅');return true;
    }if($text!=='بله✅')throw new \RuntimeException('تأیید: بله✅');
    foreach(tc_rows('SELECT bot FROM amarbot') as $b)tc_stmt('DELETE FROM '.tc_ident($s['prefix'].$b['bot']));
   }elseif(in_array($a,['getstep','setstep','reset','check','isset'],true)){
    if(!isset($s['id'])){
     if(!preg_match('/^\d+$/D',$text))throw new \RuntimeException('شناسهٔ عددی بفرست.');
     if($a==='setstep'){$s['id']=$text;tc_state('manager',$uid,$s);tc_say($token,$uid,'نام مرحله را بفرست.');return true;}
     if($a==='reset')tc_stmt('UPDATE `user` SET step=? WHERE chatid=?',['',$text]);
     $r=tc_rows('SELECT chatid,step FROM `user` WHERE chatid=?',[$text]);tc_say($token,$uid,json_encode($r,JSON_UNESCAPED_UNICODE));
    }else tc_stmt('UPDATE `user` SET step=? WHERE chatid=?',[$text,$s['id']]);
   }
   tc_state('manager',$uid,[]);tc_say($token,$uid,'انجام شد. /help');return true;
  }
  if(in_array($text,['addcoin','setallmoney','update','deleteup','deletingbot','deletetable','getstep','setstep','reset','check','isset'],true)){
   tc_state('manager',$uid,['action'=>$text]);tc_say($token,$uid,match($text){'addcoin','getstep','setstep','reset','check','isset'=>'شناسهٔ عددی کاربر را بفرست.','setallmoney'=>'موجودی جدید همهٔ کاربران؟','update'=>'نام/شماره نسخه؟','deleteup'=>'تاریخچه نسخه پاک شود؟ تأیید: بله✅','deletingbot'=>'یوزرنیم ربات؟','deletetable'=>'نام نوع جدول؟ مثلاً pasokh. این عملیات حذف داده است.',default=>'ورودی؟'});return true;
  }
  if($text==='amar')tc_say($token,$uid,'کاربران: '.tc_value('SELECT COUNT(*) FROM `user`'));
  elseif($text==='getall'){foreach(array_chunk(tc_rows('SELECT chatid FROM `user`'),150) as $rows)tc_say($token,$uid,implode("\n",array_column($rows,'chatid')));}
  elseif(in_array($text,['amarkoli','getalllistbot'],true)){
   $lines=[];$total=0;foreach(tc_rows('SELECT bot FROM amarbot') as $b){$n=(int)tc_value('SELECT COUNT(*) FROM '.tc_ident('user'.$b['bot']));$total+=$n;$lines[]='@'.$b['bot'].' — '.$n;}
   if($text==='amarkoli')tc_say($token,$uid,'ربات‌ها: '.count($lines).'؛ مجموع عضویت‌ها: '.$total);else foreach(array_chunk($lines,40) as $chunk)tc_say($token,$uid,implode("\n",$chunk));
  }elseif($text==='updateallbot'){
   $n=0;foreach(tc_rows('SELECT * FROM amarbot') as $b){tc_schema($b['bot']);tc_generate($b['bot'],$b['token'],$b['creatorid']);$n++;}tc_say($token,$uid,$n.' ربات از قالب فعلی بازسازی شد.');
  }elseif($text==='link')tc_say($token,$uid,'https://t.me/'.tc_env('CREATOR_USERNAME').'?start='.base64_encode('/start'));
  elseif($text==='dice')tc_api($token,'sendDice',['chat_id'=>$uid,'emoji'=>'⚽']);
  else tc_say($token,$uid,'دستور ناشناخته است. /help');
 }catch(\Throwable $e){tc_say($token,$uid,'خطا: '.tc_brand($e->getMessage()).'؛ لغو: /cancel');}
 return true;
}
function tc_market_purchase($token,$q){
 $uid=(string)$q->from->id;preg_match('/^buy-([A-Za-z0-9_]+)-\+-([0-9]+)-\+-([0-9]+)$/D',$q->data,$m);
 if(!$m)return false;$bot=$m[1];$price=$m[2];$seller=$m[3];$db=tc_db();
 try{
  if($uid===$seller)throw new \RuntimeException('ربات متعلق به خود شماست.');
  $db->begin_transaction();
  $b=tc_rows('SELECT * FROM amarbot WHERE bot=? FOR UPDATE',[$bot])[0]??null;
  $offer=tc_rows('SELECT * FROM foroshgah WHERE bot=? FOR UPDATE',[$bot])[0]??null;
  if(!$b||!$offer||(string)$b['creatorid']!==$seller||(string)$offer['price']!==$price)throw new \RuntimeException('آگهی دیگر معتبر نیست.');
  $bal=tc_value('SELECT emtiaz FROM `user` WHERE chatid=? FOR UPDATE',[$uid]);
  if($bal===null||(int)$bal<(int)$price)throw new \RuntimeException('ابتدا ربات‌ساز را استارت کنید؛ سکهٔ کافی لازم است.');
  tc_stmt('UPDATE `user` SET emtiaz=emtiaz-? WHERE chatid=?',[$price,$uid]);
  tc_stmt('UPDATE `user` SET emtiaz=COALESCE(emtiaz,0)+? WHERE chatid=?',[$price,$seller]);
  tc_stmt('UPDATE amarbot SET creatorid=? WHERE bot=?',[$uid,$bot]);tc_stmt('DELETE FROM foroshgah WHERE bot=?',[$bot]);$db->commit();
  tc_generate($bot,$b['token'],$uid);tc_refresh_keyboard($uid);tc_refresh_keyboard($seller);
  tc_api($token,'answerCallbackQuery',['callback_query_id'=>$q->id,'text'=>'خرید و انتقال مالکیت انجام شد.','show_alert'=>true]);
  tc_say($token,$seller,'ربات @'.$bot.' با '.$price.' سکه منتقل شد.');
 }catch(\Throwable $e){$db->rollback();tc_api($token,'answerCallbackQuery',['callback_query_id'=>$q->id,'text'=>tc_brand($e->getMessage()),'show_alert'=>true]);}
 return true;
}
