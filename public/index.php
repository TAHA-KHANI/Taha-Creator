<?php
namespace Taha;
require_once __DIR__.'/../app/runtime/bootstrap.php';
try {
 $path=parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH);
 if($path==='/healthz'){
  tc_db();$beat=tc_value("SELECT value FROM tc_meta WHERE name='worker_heartbeat'");
  $ok=$beat && time()-(int)$beat<180;
  http_response_code($ok?200:503);header('Content-Type: application/json');echo json_encode(['ok'=>$ok,'version'=>TC_VERSION]);exit;
 }
 if(in_array($path,['/captha.php','/chart.php'],true)){require __DIR__.'/../app/runtime/images.php';exit;}
 if($path==='/'){header('Content-Type: text/plain; charset=utf-8');echo 'Taha Creator is running.';exit;}
 $route=match($path){'/Pedar.php'=>'creator','/bot.php'=>'manager',default=>null};
 if(!$route && preg_match('#^/BotList/([A-Za-z0-9_]{5,32})/\1\.php$#D',$path,$m))$route=$m[1];
 if(!$route){http_response_code(404);exit;}
 if(($_SERVER['REQUEST_METHOD']??'')!=='POST'){http_response_code(405);exit;}
 $token=tc_route_token($route);
 if(!$token||!hash_equals(tc_secret($token),$_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN']??'')){http_response_code(403);exit;}
 $raw=file_get_contents('php://input',false,null,0,1048577);
 if(strlen($raw)>1048576){http_response_code(413);exit;}
 $u=json_decode($raw,true);
 if(!is_array($u)||!isset($u['update_id'])||!is_int($u['update_id'])){http_response_code(400);exit;}
 // A committed queue entry is required before Telegram receives HTTP 200.
 tc_stmt('INSERT IGNORE INTO tc_queue(route,update_id,payload) VALUES (?,?,?)',[$route,$u['update_id'],$raw]);
 header('Content-Type: application/json');echo '{"ok":true}';
}catch(\Throwable $e){error_log('Webhook failure: '.get_class($e));http_response_code(503);}
