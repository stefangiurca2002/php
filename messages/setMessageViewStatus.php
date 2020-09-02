<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userCode = $data->user1;
$user2 = $data->user2;
try{
    $sql = "SELECT userName FROM users WHERE code='$userCode'";
    $user1 = $conn->query($sql)->fetch();
    $user1 = $user1['userName'];
    $sql = "UPDATE messages SET view=1 WHERE user1='$user2' AND user2='$user1'";
    $conn->exec($sql);
    echo json_encode(['succes' => true]);
}
catch(PDOexception $e){
    echo json_encode(['error' => $e->getMessage()]);
}