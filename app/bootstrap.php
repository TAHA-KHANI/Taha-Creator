<?php
namespace Taha;
const TC_APP = __DIR__.'/..';
const TC_VERSION = '6.0-railway';
function tc_env($key,$default=''){ $v=getenv($key); return $v===false ? $default : $v; }
define('Taha\\TC_OWNER_ID', tc_env('OWNER_ID'));
define('Taha\\TC_LOG_CHAT', tc_env('LOG_CHAT_ID',tc_env('OWNER_ID')));
define('Taha\\TC_REVIEW_CHAT', tc_env('REVIEW_CHAT_ID',tc_env('OWNER_ID')));
date_default_timezone_set('Asia/Tehran');
ini_set('display_errors','0');
ini_set('log_errors','1');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING & ~E_NOTICE);
function tc_db(){
 static $db=null;
 if($db!==null) return $db;
 \mysqli_report(MYSQLI_REPORT_OFF);
 $candidate=@new \mysqli(tc_env('MYSQLHOST'),tc_env('MYSQLUSER'),tc_env('MYSQLPASSWORD'),tc_env('MYSQLDATABASE'),(int)tc_env('MYSQLPORT','3306'));
 $db=$candidate;
 if($db->connect_errno){
  $code=$db->connect_errno;
  $msg=$db->connect_error;
  $db=null;
  throw new \RuntimeException('Database unavailable: '.$code.' '.$msg);
 }
 $db->set_charset('utf8mb4');
 // Old nullable counters require permissive coercion; all IDs are BIGINT.
 $db->query("SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION'");
 return $db;
}
function tc_stmt($sql,$args=[]){
 $s=tc_db()->prepare($sql);
 if(!$s)throw new \RuntimeException('Database prepare failed: '.tc_db()->errno.' '.tc_db()->error);
 if($args){$types=str_repeat('s',count($args));$s->bind_param($types,...$args);}
 if(!$s->execute())throw new \RuntimeException('Database execution failed: '.$s->errno.' '.$s->error);
 return $s;
}
function tc_rows($sql,$args=[]){return tc_stmt($sql,$args)->get_result()->fetch_all(MYSQLI_ASSOC);}
function tc_value($sql,$args=[]){$r=tc_rows($sql,$args);return $r?array_values($r[0])[0]:null;}
function tc_ident($s){if(!preg_match('/^[A-Za-z0-9_]{1,64}$/D',(string)$s))throw new \InvalidArgumentException('Invalid database identifier');return '`'.$s.'`';}
function tc_sql_value($s){return tc_db()->real_escape_string((string)($s??''));}
function tc_sql_fragment($s){
 $s=(string)($s??'');
 if($s==='' || preg_match('/^-?[A-Za-z0-9_]+$/D',$s))return $s;
 // Legacy column lists are limited to identifiers, commas and backticks.
 if(preg_match('/^`?[A-Za-z0-9_]+`?(?:\s*,\s*`?[A-Za-z0-9_]+`?)*$/D',$s))return $s;
 throw new \InvalidArgumentException('Invalid SQL identifier or column list');
}
function tc_sql_values($s){return (string)$s;}
function tc_query($db,$sql){
 // Benign duplicate schema/registration attempts are present in the original.
 $sql=preg_replace('/\bCREATE TABLE\s+(?!IF NOT EXISTS)/i','CREATE TABLE IF NOT EXISTS ',$sql);
 $sql=preg_replace('/\b(chatid|userid|creatorid|sellerid)\s+INT\b/i','$1 BIGINT',$sql);
 $r=$db->query($sql);
 if($r===false && !in_array($db->errno,[1050,1060,1062,1091,1146,1054],true))error_log('Legacy SQL error '.$db->errno);
 return $r;
}
function tc_fetch_array($r){return $r instanceof \mysqli_result ? $r->fetch_array() : null;}
function tc_num_rows($r){return $r instanceof \mysqli_result ? $r->num_rows : 0;}
function tc_update(){return $GLOBALS['TC_UPDATE']??json_decode('{}');}
function tc_root(){return rtrim(tc_env('DATA_DIR','/data'),'/');}
function tc_base(){return rtrim(tc_env('BASE_URL',tc_env('RAILWAY_PUBLIC_DOMAIN')?'https://'.tc_env('RAILWAY_PUBLIC_DOMAIN'):''),'/');}
function tc_secret($token){return hash_hmac('sha256',$token,tc_env('APP_SECRET'));}
function tc_config_check(){
 foreach(['CREATOR_TOKEN','MANAGER_TOKEN','OWNER_ID','APP_SECRET','MYSQLHOST','MYSQLUSER','MYSQLPASSWORD','MYSQLDATABASE'] as $k)
  if(tc_env($k)==='')throw new \RuntimeException('Missing variable: '.$k);
 if(!preg_match('/^[0-9]{1,20}$/D',tc_env('OWNER_ID')))throw new \RuntimeException('OWNER_ID must be numeric.');
 if(strlen(tc_env('APP_SECRET'))<32)throw new \RuntimeException('APP_SECRET requires at least 32 characters.');
 if(tc_env('CREATOR_TOKEN')===tc_env('MANAGER_TOKEN'))throw new \RuntimeException('The two primary tokens must differ.');
 foreach(['CREATOR_TOKEN','MANAGER_TOKEN'] as $k)if(!preg_match('/^\d+:[A-Za-z0-9_-]{20,}$/D',tc_env($k)))throw new \RuntimeException('Invalid '.$k);
 if(tc_base()!=='' && !preg_match('#^https://[A-Za-z0-9.-]+$#D',tc_base()))throw new \RuntimeException('BASE_URL must be an HTTPS domain without a path.');
}
function tc_schema($bot=''){
 static $done=[];
 if(isset($done[$bot]))return;
 $all=json_decode(file_get_contents(__DIR__.'/schema.json'),true);
 $scope=$bot===''?'central':'child';
 foreach($all[$scope] as $prefix=>$columns){
  $table=$prefix.$bot; $defs=[];
  $primaryKey=null;
  if(in_array($prefix,['user','data','dayamar','fileid','eshtrak'],true)){
   $primaryKey= $prefix==='user'||$prefix==='fileid'?'chatid':($prefix==='dayamar'?'day':'id');
  }
  foreach($columns as $c=>$t){
   if($prefix==='amarbot' && $c==='bot')$t='VARCHAR(64)';
   $defs[]=tc_ident($c).' '.$t.($c===$primaryKey?' NOT NULL':' NULL');
  }
  if($primaryKey!==null && isset($columns[$primaryKey]))$defs[]='PRIMARY KEY ('.tc_ident($primaryKey).')';
  if($prefix==='amarbot')$defs[]='UNIQUE KEY bot_unique (`bot`)';
  if(!tc_db()->query('CREATE TABLE IF NOT EXISTS '.tc_ident($table).' ('.implode(',',$defs).') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'))throw new \RuntimeException('Schema creation failed '.$table.': '.tc_db()->errno.' '.tc_db()->error);
  $existing=[];foreach(tc_rows('SHOW COLUMNS FROM '.tc_ident($table)) as $r)$existing[strtolower($r['Field'])]=true;
  foreach($columns as $c=>$t)if(!isset($existing[strtolower($c)])){
   if(!tc_db()->query('ALTER TABLE '.tc_ident($table).' ADD '.tc_ident($c).' '.$t.' NULL'))throw new \RuntimeException('Column migration failed '.$table.'.'.$c.': '.tc_db()->errno.' '.tc_db()->error);
  }
 }
 if($bot!==''){
  tc_stmt('INSERT IGNORE INTO '.tc_ident('data'.$bot)." (`id`,`startmessage`,`txtback`,`eshtebah`,`startpanel`,`botpower`,`keyboard`,`keygroup`,`newozv`,`deletelink`,`lockjoin`,`sendzirmaj`) VALUES (1,?,?,?,?,?,?,?,?,?,?,?)",['سلام! به ربات خوش آمدید.','به عقب برگشتید.','این دستور وجود ندارد.','به پنل مدیریت خوش آمدید.','off','[{"text":""}],[{"text":""}]','off','on','off','off','off']);
 }
 $done[$bot]=true;
}
function tc_install(){
 tc_schema();
 tc_db()->query("CREATE TABLE IF NOT EXISTS tc_queue (id BIGINT AUTO_INCREMENT PRIMARY KEY, route VARCHAR(100) NOT NULL, update_id BIGINT NOT NULL, payload LONGTEXT NULL, status VARCHAR(16) NOT NULL DEFAULT 'pending', created TIMESTAMP DEFAULT CURRENT_TIMESTAMP, finished TIMESTAMP NULL, UNIQUE KEY uq(route,update_id), KEY pending(status,id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
 tc_db()->query("CREATE TABLE IF NOT EXISTS tc_meta (name VARCHAR(100) PRIMARY KEY, value LONGTEXT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
 tc_db()->query("CREATE TABLE IF NOT EXISTS tc_disabled (bot VARCHAR(64) PRIMARY KEY) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
 tc_stmt('INSERT IGNORE INTO `user` (`chatid`,`userid`,`firstname`,`emtiaz`,`zirmaj`,`captha`,`step`) VALUES (?,?,?,?,?,?,?)',[tc_env('OWNER_ID'),tc_env('OWNER_ID'),'مدیر',0,0,'true','']);
 if(!tc_value('SELECT COUNT(*) FROM `update`'))tc_stmt('INSERT INTO `update` (`noskhe`,`update`) VALUES (?,?)',[TC_VERSION,'نسخهٔ Railway؛ اتصال امن وبهوک، متغیرهای محیطی، صف پیام و اصلاحات سازگاری.']);
}
function tc_route_token($route){
 if($route==='creator')return tc_env('CREATOR_TOKEN');
 if($route==='manager')return tc_env('MANAGER_TOKEN');
 if(!preg_match('/^[A-Za-z0-9_]{5,32}$/D',$route))return null;
 if(tc_value('SELECT bot FROM tc_disabled WHERE bot=?',[$route]))return null;
 return tc_value('SELECT token FROM amarbot WHERE bot=?',[$route]);
}
function tc_webhook($token,$route,$drop=false){
 $path=$route==='creator'?'/Pedar.php':($route==='manager'?'/bot.php':'/BotList/'.$route.'/'.$route.'.php');
 return tc_api($token,'setWebhook',['url'=>tc_base().$path,'secret_token'=>tc_secret($token),'allowed_updates'=>json_encode(['message','edited_message','channel_post','edited_channel_post','callback_query','inline_query','my_chat_member']),'drop_pending_updates'=>$drop,'max_connections'=>1]);
}
function tc_brand($s){
 $s=str_replace(['https://creator.invalid','http://creator.invalid'],tc_base(),$s);
 $main=tc_env('CREATOR_USERNAME','YourCreatorBot');
 $channel=ltrim(tc_env('ANNOUNCEMENT_CHANNEL',$main),'@');
 $shop=ltrim(tc_env('SHOP_CHANNEL',$channel),'@');
 $support=ltrim(tc_env('SUPPORT_USERNAME',$main),'@');
 $s=str_replace(['Taha_Creator_Robot','Taha_Creator','Taha_Buybot','tahakhaniBot','tahakhani','Black_Lotus_team'],[$main,$channel,$shop,$support,$support,$channel],$s);
 $s=preg_replace('/\b\d{6,}:[A-Za-z0-9_-]{20,}\b/','[توکن مخفی شد]',$s);
 return $s;
}
function tc_api($token,$method,$data=[]){
 if(!preg_match('/^\d+:[A-Za-z0-9_-]{20,}$/D',(string)$token))return (object)['ok'=>false,'description'=>'Invalid bot token'];
 $method=strtolower($method);
 if(isset($data['chat_id']) && (string)$data['chat_id']==='-1001578844277')return (object)['ok'=>false];
 if(!preg_match('/^[a-z]+$/D',$method))throw new \InvalidArgumentException('Invalid API method');
 if(isset($data['chat_id']) && ($data['chat_id']==='' || $data['chat_id']===null))return (object)['ok'=>false];
 if(in_array($method,['kickchatmember'],true))$method='banchatmember';
 if($method==='setwebhook'){
  if(!empty($data['url']))$data['url']=str_replace('https://creator.invalid',tc_base(),$data['url']);
  $data['secret_token']=tc_secret($token);
 }
 foreach(['text','caption'] as $k)if(isset($data[$k]))$data[$k]=tc_brand((string)$data[$k]);
 foreach(['photo','document','video','audio','voice'] as $k)if(isset($data[$k])&&is_string($data[$k]))$data[$k]=str_replace('https://creator.invalid',tc_base(),$data[$k]);
 if(isset($data['reply_markup'])){
  $key=is_string($data['reply_markup'])?json_decode($data['reply_markup'],true):$data['reply_markup'];
  if(is_array($key)){
   if(isset($key['hide_keyboard'])){$key['remove_keyboard']=true;unset($key['hide_keyboard']);}
   foreach(['keyboard','inline_keyboard'] as $part)if(isset($key[$part])){
    $rows=[];foreach($key[$part] as $row){$row=array_values(array_filter($row,fn($b)=>isset($b['text'])&&trim($b['text'])!==''));if($row)$rows[]=$row;}
    if($rows)$key[$part]=$rows;else{unset($key[$part]);if($part==='keyboard')$key['remove_keyboard']=true;}
   }
   $data['reply_markup']=tc_brand(json_encode($key,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
  }else unset($data['reply_markup']);
 }
 if($method==='senddice' && ($data['emoji']??'')==='🎰')return (object)['ok'=>false,'description'=>'Unsupported animation'];
 // Test transport: never makes network requests.
 if(isset($GLOBALS['TC_TRANSPORT']))return ($GLOBALS['TC_TRANSPORT'])($token,$method,$data);
 for($try=0;$try<3;$try++){
  $ch=curl_init('https://api.telegram.org/bot'.$token.'/'.$method);
  curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>http_build_query($data),CURLOPT_CONNECTTIMEOUT=>10,CURLOPT_TIMEOUT=>40,CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS]);
  $raw=curl_exec($ch);$err=curl_errno($ch);curl_close($ch);
  if($err)throw new \RuntimeException('Telegram transport failed '.$err); // don't repeat mutations on uncertain transport
  $result=json_decode($raw);
  if(!$result)throw new \RuntimeException('Telegram returned invalid JSON');
  if(($result->error_code??0)===429){sleep(min(30,max(1,(int)($result->parameters->retry_after??2))));continue;}
  if(empty($result->ok))error_log('Telegram '.$method.' error '.($result->error_code??0));
  return $result;
 }
 return $result;
}
function tc_required_channels($token,$uid){
 if((string)$uid===tc_env('OWNER_ID'))return true;
 foreach(array_filter(array_map('trim',explode(',',tc_env('REQUIRED_CHANNELS')))) as $channel){
  $r=tc_api($token,'getChatMember',['chat_id'=>$channel,'user_id'=>$uid]);
  if(!in_array($r->result->status??'',['creator','administrator','member'],true)){
   tc_api($token,'sendMessage',['chat_id'=>$uid,'text'=>'ابتدا عضو '.$channel.' شوید و سپس /start را بفرستید.']);return false;
  }
 }
 return true;
}
function tc_http($url){
 $p=parse_url($url);
 if(!isset($p['host'])||!in_array($p['scheme']??'',['https','http'],true)||isset($p['user'])||isset($p['pass']))throw new \RuntimeException('Only public HTTP(S) URLs are supported');
 $ips=gethostbynamel($p['host']);
 if(!$ips)throw new \RuntimeException('URL DNS lookup failed');
 foreach($ips as $ip)if(!filter_var($ip,FILTER_VALIDATE_IP,FILTER_FLAG_NO_PRIV_RANGE|FILTER_FLAG_NO_RES_RANGE))throw new \RuntimeException('Private network URL blocked');
 $port=$p['port']??($p['scheme']==='https'?443:80);
 if(!in_array($port,[80,443],true))throw new \RuntimeException('Unsupported URL port');
 $ch=curl_init($url);$body='';
 curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_CONNECTTIMEOUT=>8,CURLOPT_TIMEOUT=>25,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_PROTOCOLS=>CURLPROTO_HTTP|CURLPROTO_HTTPS,CURLOPT_RESOLVE=>[$p['host'].':'.$port.':'.$ips[0]],CURLOPT_WRITEFUNCTION=>function($c,$s)use(&$body){if(strlen($body)+strlen($s)>5*1024*1024)return 0;$body.=$s;return strlen($s);}]);
 curl_exec($ch);$err=curl_errno($ch);$status=curl_getinfo($ch,CURLINFO_RESPONSE_CODE);curl_close($ch);
 if($err||$status>=400)throw new \RuntimeException('External service request failed');
 return $body;
}
function tc_fetch($path,...$args){
 if($path==='php://input')return json_encode(tc_update());
 if(preg_match('#^https?://api.telegram.org/bot([^/]+)/([A-Za-z]+)(?:\?(.*))?$#s',$path,$m)){
  parse_str($m[3]??'',$params);return json_encode(tc_api($m[1],$m[2],$params));
 }
 if(str_starts_with($path,'https://creator.invalid/search.php')){parse_str(parse_url($path,PHP_URL_QUERY)??'',$q);return json_encode(tc_search($q['username']??'',$q['search']??''));}
 if(preg_match('#^https?://#',$path))return tc_http(str_replace('https://creator.invalid',tc_base(),$path));
 if(!is_file($path))return '';
 return file_get_contents($path,...$args);
}
function tc_write($path,$content,$flags=0){
 // Only write beneath the persistent runtime directory, never the public root.
 $parent=realpath(dirname($path));$root=realpath(tc_root());
 if(!$parent||!$root||!str_starts_with($parent.'/',$root.'/'))throw new \RuntimeException('Invalid output path');
 if(str_ends_with($path,'.php')){
  if(!preg_match('#/BotList/([A-Za-z0-9_]{5,32})/\1\.php$#',$parent.'/'.basename($path)))throw new \RuntimeException('Invalid generated bot filename');
 }
 $tmp=$path.'.tmp.'.bin2hex(random_bytes(4));
 if($flags & FILE_APPEND)return file_put_contents($path,$content,FILE_APPEND|LOCK_EX);
 $n=file_put_contents($tmp,$content,LOCK_EX);if($n===false||!rename($tmp,$path))throw new \RuntimeException('File write failed');chmod($path,0600);return $n;
}
function tc_curl_exec($ch){
 $url=curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);
 if(str_contains($url,'rextester.com')){
  // Original remote-code engine is optional, never evaluated on the bot server.
  if(tc_env('ENABLE_REMOTE_PHP')!=='true')return json_encode(['Errors'=>'اجرای PHP از راه دور فعال نیست.','Result'=>'اجرای PHP از راه دور فعال نیست.']);
 }
 if(!str_starts_with($url,'https://api.telegram.org/') && !str_starts_with($url,'https://rextester.com/'))return tc_http($url);
 curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);curl_setopt($ch,CURLOPT_TIMEOUT,30);
 return curl_exec($ch);
}
function tc_search($username,$query){
 if(!preg_match('/^[A-Za-z0-9_]{5,32}$/D',$username))return ['ok'=>false,'result'=>[],'count_result'=>0];
 $html=tc_http('https://t.me/s/'.$username.'?q='.rawurlencode($query));$out=[];
 preg_match_all('#data-post="'.preg_quote($username,'#').'/([0-9]+)"#',$html,$m);
 foreach(array_reverse(array_unique($m[1])) as $id)$out[]=['channel'=>$username,'message_id'=>$id];
 return ['ok'=>(bool)$out,'result'=>$out,'count_result'=>count($out)];
}
require_once __DIR__.'/control.php';

function tc_count($v){return is_countable($v)?count($v):0;}
function tc_insert($table,$cols,$values){
 if(!is_array($values))throw new \InvalidArgumentException('Expected parameter array');
 $cols=array_map(fn($x)=>tc_ident(trim($x," `\t\n")),explode(',',$cols));
 if(count($cols)!==count($values))throw new \InvalidArgumentException('Column/value mismatch');
 return tc_stmt('INSERT INTO '.tc_ident($table).' ('.implode(',',$cols).') VALUES ('.implode(',',array_fill(0,count($values),'?')).')',$values);
}

// Namespaced compatibility wrappers used by the legacy media helpers.
function get_headers($url){
 $data=tc_http($url);$f=new \finfo(FILEINFO_MIME_TYPE);return ['Content-Type: '.$f->buffer($data)];
}
