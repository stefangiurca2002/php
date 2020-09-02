<?php
require '../DatabaseConnection.php';
$encodedData = file_get_contents("php://input");
$data = json_decode($encodedData);
$token = $data->token;
if($token){
try{
$sql = "SELECT userName FROM divaces WHERE token='$token'";
$userName = $conn->query($sql);
$userName = $userName->fetch();
$userName = $userName['userName'];
if($userName){
echo json_encode([
    'valid' => true,
    'userName' => $userName
]);
}
else{
    echo json_encode([
        'valid' => false,
        'userName' => null
    ]);
}
}
catch(PDOeception $e){
    echo json_encode([
        'valid' => false,
        'err' => $e->getMessage()
        ]);
}
}
else{
    echo json_encode([
        'vlaid' => false,
        'userName' => null,
        'err' => 'token is null'
    ]);
}