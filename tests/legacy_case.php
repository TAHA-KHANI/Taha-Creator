<?php
namespace Taha;
if(getenv('TC_TEST_DATABASE')!=='true')exit(2);
require_once __DIR__.'/../app/runtime/bootstrap.php';
$caseRoute=$argv[1]??'creator';$caseText=$argv[2]??'/start';$caseUid=$argv[3]??tc_env('OWNER_ID');
putenv('CREATOR_TOKEN=111111111:'.str_repeat('c',35));putenv('MANAGER_TOKEN=222222222:'.str_repeat('m',35));putenv('CREATOR_USERNAME=ExampleCreatorBot');
$messages=[];
$GLOBALS['TC_TRANSPORT']=function($token,$method,$data)use(&$messages,$caseRoute){
 if($method==='getme')return (object)['ok'=>true,'result'=>(object)['id'=>555555,'username'=>$caseRoute==='creator'?'ExampleCreatorBot':'TestChildBot','first_name'=>'Test']];
 if($method==='getchatmember')return (object)['ok'=>true,'result'=>(object)['status'=>'member']];
 $messages[]=[$method,$data];return (object)['ok'=>true,'result'=>(object)['message_id'=>101,'id'=>123456789,'username'=>'tester','first_name'=>'Test']];
};
register_shutdown_function(function()use(&$messages){$last=error_get_last();if($last && in_array($last['type'],[E_ERROR,E_PARSE,E_COMPILE_ERROR],true))fwrite(STDERR,json_encode($last)."\n");echo json_encode($messages,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n";});
$GLOBALS['TC_UPDATE']=json_decode(json_encode(['update_id'=>1000,'message'=>['message_id'=>100,'date'=>time(),'text'=>$caseText,'from'=>['id'=>(int)$caseUid,'first_name'=>'Test','username'=>'tester','is_bot'=>false],'chat'=>['id'=>(int)$caseUid,'type'=>'private']]]));
tc_schema();chdir(tc_root());
if($caseRoute==='setup'){
 tc_schema('TestChildBot');tc_generate('TestChildBot','555555:'.str_repeat('z',35),tc_env('OWNER_ID'));
 tc_stmt('INSERT IGNORE INTO amarbot(bot,token,creatorid) VALUES (?,?,?)',['TestChildBot','555555:'.str_repeat('z',35),tc_env('OWNER_ID')]);tc_stmt('DELETE FROM tc_disabled WHERE bot=?',['TestChildBot']);exit;
}
if(!tc_control($caseRoute,$GLOBALS['TC_UPDATE'])){
 if($caseRoute==='creator')require TC_APP.'/legacy/Pedar.php';
 else{chdir(tc_root().'/BotList/TestChildBot');require 'TestChildBot.php';}
}
