<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userCode = $data->user1;
$user2 = $data->user2;
$message = $data->msg;
$date = $data->date;
if(isset($data)){
try{
    $sql = "SELECT userName FROM users WHERE code='$userCode'";
    $user1 = $conn->query($sql)->fetch();
    $user1 = $user1['userName'];
    if($user1){
     $sql = "SELECT * FROM loby WHERE user1='$user1' AND user2='$user2' OR user1='$user2' AND user2='$user1'";
     $ExistChat = $conn->query($sql);
     if(!$ExistChat->fetch()){
         $conn->exec("INSERT INTO loby (user1, user2) VALUES ('$user1', '$user2')");
     }
     $sql = "INSERT INTO messages (user1, user2, message, view, date) VALUES ('$user1','$user2', '$message', 0, '$date')";
     $conn->exec($sql);
     echo json_encode(['succes'=>'daa']);
    }
    else{
        echo json_encode([
            'user' => $user1,
            'code' => $userCode
        ]);
    }
}
catch(PDOexception $e){
    echo json_encode(['error' => $e->getMessage()]);
}
}