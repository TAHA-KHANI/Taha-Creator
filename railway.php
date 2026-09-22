<?php
function railway_env($key,$default=''){
    $value=getenv($key);
    return $value===false || $value==='' ? $default : $value;
}
function railway_base_url(){
    $base=railway_env('BASE_URL');
    if($base===''){
        $domain=railway_env('RAILWAY_PUBLIC_DOMAIN');
        if($domain!=='')$base='https://'.$domain;
    }
    return rtrim($base,'/');
}
function railway_mysql(){
    static $con=null;
    if($con!==null)return $con;
    mysqli_report(MYSQLI_REPORT_OFF);
    $con=mysqli_connect(
        railway_env('MYSQLHOST','178.162.159.1'),
        railway_env('MYSQLUSER','tahacrea_taharobot'),
        railway_env('MYSQLPASSWORD','taha13831383'),
        railway_env('MYSQLDATABASE','tahacrea_taharobot'),
        (int)railway_env('MYSQLPORT','3306')
    );
    if($con)mysqli_set_charset($con,'utf8mb4');
    return $con;
}
function railway_token($key,$fallback){
    return railway_env($key,$fallback);
}
function railway_url($path=''){
    return railway_base_url().$path;
}
