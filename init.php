<?php
$con=railway_mysql();
if(!$con || mysqli_connect_errno()){
    fwrite(STDERR,"Database initialization failed: ".mysqli_connect_error()."\n");
    exit(1);
}
mysqli_query($con,"SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION'");

$base=railway_base_url();
if($base!==''){
    $hooks=[
        railway_token('CREATOR_TOKEN','')=>'/Pedar.php',
        railway_token('MANAGER_TOKEN','')=>'/bot.php',
    ];
    foreach($hooks as $token=>$path){
        if($token==='')continue;
        $url='https://api.telegram.org/bot'.$token.'/setWebhook?'.http_build_query([
            'url'=>$base.$path,
            'drop_pending_updates'=>'true'
        ]);
        $result=@file_get_contents($url);
        if($result===false)error_log('Webhook setup failed for '.$path);
    }
}
error_log('Taha Creator original Railway init completed.');
