<?php
require '../DatabaseConnection.php';
$encodedData = file_get_contents("php://input");
$data = json_decode($encodedData);
$token = $data->token;
$userName = $data->userName;
if(isset($data)){
try{
    $allreadyExist = $conn->query("SELECT * FROM usersToken WHERE token='$token' AND userName='$userName'");
if(!$allreadyExist->fetch())
{
$sql = "INSERT INTO usersToken (token, userName) VALUES ('$token', '$userName')";
$conn->exec($sql);
echo json_encode(['succses' => true]);
}
else json_encode([
    'data' => $data,
    'from db' => $allreadyExist
]);
}
catch(PDOexception $e){
    echo json_encode(['err'=>$e->getMessage()]);
}
}