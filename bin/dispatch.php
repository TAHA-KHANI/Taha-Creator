<?php
namespace Taha;
require_once __DIR__.'/../app/runtime/bootstrap.php';
$id=$argv[1]??'';if(!ctype_digit($id))exit(2);
$job=tc_rows("SELECT * FROM tc_queue WHERE id=? AND status='running'",[$id])[0]??null;if(!$job)exit(2);
$finished=false;
register_shutdown_function(function()use($id,&$finished){
 if($finished)return;
 $error=error_get_last();$fatal=$error && in_array($error['type'],[E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR],true);
 tc_stmt('UPDATE tc_queue SET status=?,payload=NULL,finished=NOW() WHERE id=?',[$fatal?'failed':'done',$id]);
 if($fatal)error_log('Job '.$id.' fatal at '.basename($error['file']).':'.$error['line']);
});
try{
 tc_schema();
 $username=tc_value("SELECT value FROM tc_meta WHERE name='creator_username'");if($username)putenv('CREATOR_USERNAME='.$username);
 $GLOBALS['TC_UPDATE']=json_decode($job['payload']);$route=$job['route'];
 if(!tc_control($route,$GLOBALS['TC_UPDATE'])){
  $con=tc_db();chdir(tc_root());
  if($route==='creator')require TC_APP.'/legacy/Pedar.php';
  else{
   $b=tc_rows('SELECT * FROM amarbot WHERE bot=?',[$route])[0]??null;
   if($b){tc_schema($route);$file=tc_root().'/BotList/'.$route.'/'.$route.'.php';if(!is_file($file))tc_generate($route,$b['token'],$b['creatorid']);chdir(dirname($file));require $file;}
  }
 }
 $finished=true;tc_stmt("UPDATE tc_queue SET status='done',payload=NULL,finished=NOW() WHERE id=?",[$id]);
}catch(\Throwable $e){
 $finished=true;error_log('Job '.$id.' failed: '.get_class($e).' at '.basename($e->getFile()).':'.$e->getLine());
 tc_stmt("UPDATE tc_queue SET status='failed',payload=NULL,finished=NOW() WHERE id=?",[$id]);
 // No automatic replay of a financial operation after a partially applied request.
 $u=$GLOBALS['TC_UPDATE'];$uid=$u->message->chat->id??$u->callback_query->from->id??null;
 if($uid)try{tc_say(tc_route_token($job['route']),$uid,'عملیات کامل نشد. شناسهٔ خطا: '.$id.'؛ با /start به منو برگردید.');}catch(\Throwable $ignored){}
 exit(1);
}
