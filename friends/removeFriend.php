<?php 
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userName = $data->userName;
$friendName = $data->friend;
if($data){
try{
    $sql = "DELETE FROM friends WHERE userName='$userName' AND friendName='$friendName'";
    $conn->exec($sql);
    echo json_encode(['message' => 'friend removed succsessfuly']);
}
catch(PDOexception $e){
    die(json_encode(['message' => $e->getMessage()]));
}
}