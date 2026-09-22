<?php
namespace Taha;
// Run only against an isolated test database, never a live database.
require_once __DIR__.'/../app/runtime/bootstrap.php';
if(tc_env('TC_TEST_DATABASE')!=='true')throw new \RuntimeException('Set TC_TEST_DATABASE=true with a disposable database.');
function verify($condition,$message){if(!$condition)throw new \RuntimeException($message);}
tc_install();$uid=tc_env('OWNER_ID');$log=[];
$GLOBALS['TC_TRANSPORT']=function($token,$method,$data)use(&$log){
 $log[]=[$token,$method,$data];
 if($method==='getme')return (object)['ok'=>true,'result'=>(object)['id'=>555555,'is_bot'=>true,'username'=>'TestChildBot','first_name'=>'Test']];
 return (object)['ok'=>true,'result'=>true];
};
tc_stmt('UPDATE `user` SET emtiaz=1000 WHERE chatid=?',[$uid]);
$bot=tc_new_bot($uid,'555555:'.str_repeat('z',35));
verify($bot==='TestChildBot','Bot creation');verify((int)tc_value('SELECT emtiaz FROM `user` WHERE chatid=?',[$uid])===500,'Creation fee exactly once');
verify(is_file(tc_root().'/BotList/'.$bot.'/'.$bot.'.php'),'Generated bot');
$dup=false;try{tc_new_bot($uid,'555555:'.str_repeat('z',35));}catch(\Throwable $e){$dup=true;}
verify($dup,'Duplicate bot rejected');verify((int)tc_value('SELECT emtiaz FROM `user` WHERE chatid=?',[$uid])===500,'No double fee');
tc_insert('pasokh'.$bot,'`pasokh`,`javab`',["O'Reilly",'{"text":"سلام 🌱"}']);verify(tc_value('SELECT pasokh FROM '.tc_ident('pasokh'.$bot).' WHERE pasokh=?',["O'Reilly"])==="O'Reilly",'Quoted Unicode roundtrip');
$payload=json_encode(['update_id'=>123,'message'=>['text'=>'/start']]);
for($i=0;$i<2;$i++)tc_stmt('INSERT IGNORE INTO tc_queue(route,update_id,payload) VALUES (?,?,?)',['creator',123,$payload]);
verify((int)tc_value('SELECT COUNT(*) FROM tc_queue WHERE route=? AND update_id=?',['creator',123])===1,'Webhook duplicate suppression');
$unauthorized=(object)['message'=>(object)['from'=>(object)['id'=>999999],'chat'=>(object)['id'=>999999,'type'=>'private'],'text'=>'setallmoney']];
$before=count($log);tc_control('manager',$unauthorized);verify(count($log)===$before,'Manager rejects non-owner');
tc_remove_bot($bot,$uid);verify(!tc_value('SELECT bot FROM amarbot WHERE bot=?',[$bot]),'Bot removed from registry');verify((int)tc_value('SELECT emtiaz FROM `user` WHERE chatid=?',[$uid])===510,'Refund once');
echo "Database integration checks passed.\n";
