<?php 
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userName = $data->userName;
$friendName = $data->friend;
if($data){
try{
    $sql = "INSERT INTO friends (userName, friendName) VALUES('$userName', '$friendName')";
    $conn->exec($sql);
    echo json_encode(['message' => 'friend added succsessfuly']);
}
catch(PDOexception $e){
    die(json_encode(['message' => $e->getMessage()]));
}
}