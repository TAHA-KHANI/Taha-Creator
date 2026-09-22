<?php
$con=railway_mysql();
header('Content-Type: application/json');
if(!$con || mysqli_connect_errno()){
    http_response_code(503);
    echo json_encode(['ok'=>false,'error'=>'db']);
    exit;
}
echo json_encode(['ok'=>true,'version'=>'original-railway']);
