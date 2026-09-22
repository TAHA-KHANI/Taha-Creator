<?php
namespace Taha;
require_once __DIR__.'/../app/runtime/bootstrap.php';
$lock=fopen(tc_root().'/worker.lock','c');if(!flock($lock,LOCK_EX|LOCK_NB))exit(1);
// A previous worker may have stopped mid-operation: don't replay automatically.
tc_stmt("UPDATE tc_queue SET status='uncertain',finished=NOW(),payload=NULL WHERE status='running'");
$lastSetup=0;$lastClean=0;$running=true;
if(function_exists('pcntl_async_signals')){pcntl_async_signals(true);pcntl_signal(SIGTERM,function()use(&$running){$running=false;});}
while($running){
 try{
  tc_stmt("REPLACE INTO tc_meta(name,value) VALUES ('worker_heartbeat',?)",[time()]);
  if(tc_base()!=='' && time()-$lastSetup>120){
   $stamp=hash('sha256',tc_base().tc_env('CREATOR_TOKEN').tc_env('MANAGER_TOKEN').tc_env('APP_SECRET'));
   if(tc_value("SELECT value FROM tc_meta WHERE name='webhook_stamp'")!==$stamp){
    $me=tc_checked(tc_api(tc_env('CREATOR_TOKEN'),'getMe'))->result;
    tc_stmt("REPLACE INTO tc_meta(name,value) VALUES ('creator_username',?)",[$me->username]);
    tc_checked(tc_webhook(tc_env('CREATOR_TOKEN'),'creator'));tc_checked(tc_webhook(tc_env('MANAGER_TOKEN'),'manager'));
    foreach(tc_rows('SELECT * FROM amarbot') as $b)tc_checked(tc_webhook($b['token'],$b['bot']));
    tc_stmt("REPLACE INTO tc_meta(name,value) VALUES ('webhook_stamp',?)",[$stamp]);error_log('Webhooks configured.');
   }$lastSetup=time();
  }
  if(time()-$lastClean>3600){tc_stmt("DELETE FROM tc_queue WHERE status IN ('done','discarded') AND finished < NOW()-INTERVAL 7 DAY");$lastClean=time();}
  $job=tc_rows("SELECT id FROM tc_queue WHERE status='pending' ORDER BY id LIMIT 1")[0]??null;
  if(!$job){usleep(300000);continue;}
  tc_stmt("UPDATE tc_queue SET status='running' WHERE id=? AND status='pending'",[$job['id']]);
  $p=proc_open([PHP_BINARY,__DIR__.'/dispatch.php',(string)$job['id']],[0=>['file','/dev/null','r'],1=>['file','/dev/null','w'],2=>STDERR],$pipes);
  if(!is_resource($p)){tc_stmt("UPDATE tc_queue SET status='failed',payload=NULL,finished=NOW() WHERE id=?",[$job['id']]);continue;}
  $started=time();while(($st=proc_get_status($p))['running']){
   tc_stmt("REPLACE INTO tc_meta(name,value) VALUES ('worker_heartbeat',?)",[time()]);
   if(!$running||time()-$started>(int)tc_env('JOB_TIMEOUT','900')){proc_terminate($p);sleep(1);if(proc_get_status($p)['running'])proc_terminate($p,9);break;}usleep(500000);
  }proc_close($p);
  tc_stmt("UPDATE tc_queue SET status='uncertain',payload=NULL,finished=NOW() WHERE id=? AND status='running'",[$job['id']]);
 }catch(\Throwable $e){error_log('Worker: '.get_class($e).'. Check database and token configuration.');$lastSetup=time();sleep(5);}
}
