<?php
namespace Taha;
require_once __DIR__.'/../app/runtime/bootstrap.php';
try{
 tc_config_check();$root=tc_root();if(!is_dir($root))mkdir($root,0700,true);if(!is_writable($root))throw new \RuntimeException('DATA_DIR is not writable');
 if(!is_dir($root.'/BotList'))mkdir($root.'/BotList',0700,true);
 // Refresh shared template aliases every deploy; generated bots are refreshed from registry.
 foreach(['Haji.php','Function.php'] as $name){$p=$root.'/'.$name;if(is_link($p))unlink($p);if(!file_exists($p))symlink(TC_APP.'/legacy/'.$name,$p);}
 $ok=false;for($i=0;$i<30;$i++){try{tc_install();$ok=true;break;}catch(\Throwable $e){sleep(2);}}
 if(!$ok)throw new \RuntimeException('Database initialization failed; verify variables and DB service.');
 foreach(tc_rows('SELECT * FROM amarbot') as $b)tc_generate($b['bot'],$b['token'],$b['creatorid']);
 tc_stmt("REPLACE INTO tc_meta(name,value) VALUES ('worker_heartbeat',?)",[time()]);
 error_log('Taha Creator '.TC_VERSION.' initialized.');
}catch(\Throwable $e){fwrite(STDERR,$e->getMessage()."\n");exit(1);}
