<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$limit = $data->limit;
$offset = $data->offset;
$userCode = $data->userCode;
$result = [];
if(isset($data)){
try{
  $userName = $conn->query("SELECT userName FROM users WHERE code='$userCode'")->fetch();
  $userName = $userName['userName'];
  if($userName){
  $sql = "SELECT * FROM loby WHERE user1='$userName' OR user2='$userName' LIMIT $limit OFFSET $offset ";
  $lobys = $conn->query($sql);
  while($row = $lobys->fetch()){
      $user = $row['user1'] === $userName ? $row['user2'] : $row['user1'];
      $sql = "SELECT ProfileImage FROM users WHERE userName='$user'";
      $ProfileImage = $conn->query($sql);
      $ProfileImage = $ProfileImage->fetch();
      $sql = "SELECT message FROM messages WHERE user1='$user' OR user2='$user'ORDER BY date DESC LIMIT 1";
      $lastMessage = $conn->query($sql);
      $lastMessage = $lastMessage->fetch();
      $sql = "SELECT COUNT(message) FROM messages WHERE user2='$userName' AND user1='$user' AND view=0";
      $messagesNr = $conn->query($sql);
      $messagesNr = $messagesNr->fetch();
      array_push($result, [
        'userName'=>$user,
        'profileImage'=>$ProfileImage['ProfileImage'],
        'lastMessage'=>$lastMessage['message'],
        'messagesNr' => $messagesNr[0]
        ]);
  }
  echo json_encode($result);
}else{
  echo json_encode([
    'code' => $userCode,
    'userName' => $userName
  ]);
}
}
catch(PDOexception $e){
   $result = ['err'=>$e->getMesasage()];
}
}