<?php 
require 'DatabaseConnection.php';
$sql = "SELECT userName FROM users";
$users = $conn->query($sql);

$postUser = file_get_contents("php://input");
$userName = json_decode($postUser);
$userName = $userName->userName;

while($row = $users->fetch()){
    if($row['userName'] === $userName){
        echo json_encode(['valid'=>false]);
    break;
    }
}

if(!$users->fetch()){
    echo json_encode(['valid'=>true]);
}
