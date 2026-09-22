<?php
namespace Taha;
putenv('APP_SECRET='.str_repeat('s',64));putenv('BASE_URL=https://example.test');putenv('CREATOR_USERNAME=ExampleCreatorBot');
require_once __DIR__.'/../app/runtime/bootstrap.php';
function check($ok,$message){if(!$ok)throw new \RuntimeException($message);}
check(strlen(tc_secret('123:example'))===64,'Webhook secret length');
check(!hash_equals(tc_secret('123:example'),tc_secret('124:example')),'Separate token secrets');
check(tc_ident('dataExampleBot')==='`dataExampleBot`','Identifier quote');
foreach(['x;DROP TABLE user','../evil','a b','a`b'] as $bad){$blocked=false;try{tc_ident($bad);}catch(\InvalidArgumentException $e){$blocked=true;}check($blocked,'Unsafe identifier accepted');}
check(tc_count(null)===0,'Nullable legacy arrays');
check(str_contains(tc_brand('https://creator.invalid/a'),'https://example.test/a'),'Domain replacement');
$log=[];$GLOBALS['TC_TRANSPORT']=function($token,$method,$data)use(&$log){$log[]=[$method,$data];return (object)['ok'=>true,'result'=>true];};
$token='123456789:'.str_repeat('x',35);
tc_api($token,'sendMessage',['chat_id'=>1,'text'=>'Hello','reply_markup'=>'{"keyboard":[[{"text":""}],[{"text":"A"}],[{"text":""}]]}']);
$key=json_decode($log[0][1]['reply_markup'],true);check(count($key['keyboard'])===1 && $key['keyboard'][0][0]['text']==='A','Empty legacy keyboard rows');
tc_webhook($token,'ExampleChildBot');$v=$log[1][1];check($v['url']==='https://example.test/BotList/ExampleChildBot/ExampleChildBot.php','Child webhook path');check($v['secret_token']===tc_secret($token),'Authenticated webhook');
check(str_contains(tc_brand($token),'مخفی'),'Token masking in messages');
$root=sys_get_temp_dir().'/taha-test-'.bin2hex(random_bytes(4));mkdir($root,0700,true);putenv('DATA_DIR='.$root);mkdir($root.'/BotList/ExampleChildBot',0700,true);
tc_write($root.'/BotList/ExampleChildBot/ExampleChildBot.php','<?php // test');check(is_file($root.'/BotList/ExampleChildBot/ExampleChildBot.php'),'Atomic child write');
$blocked=false;try{tc_write('/tmp/not-allowed.txt','x');}catch(\RuntimeException $e){$blocked=true;}check($blocked,'Write outside data root');
unlink($root.'/BotList/ExampleChildBot/ExampleChildBot.php');rmdir($root.'/BotList/ExampleChildBot');rmdir($root.'/BotList');rmdir($root);
echo "Runtime unit checks passed.\n";
