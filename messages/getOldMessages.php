<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$limit = $data->limit;
$offset = $data->offset;
$userCode = $data->user1;
$user2 = $data->user2;
$unSeenResult = [];
$result = [];
try{
  $user1 = $conn->query("SELECT userName FROM users WHERE code='$userCode'")->fetch();
  $user1 = $user1['userName'];
    if(!$offset){
    $offset = $conn->query("SELECT id FROM messages ORDER BY id DESC LIMIT 1");
    $offset = $offset->fetch();
    $offset = $offset['id'] + 1;
  }
  $sql = "SELECT * FROM messages WHERE user1='$user2' AND user2='$user1' AND view=0 AND id<$offset ORDER BY date DESC";
  $unSeenMessages = $conn->query($sql);
  while($row = $unSeenMessages->fetch()){
    $offset = $row['id'];
    array_push($unSeenResult,[
      'user1' => $user1,
      'user2' => $user2,
      'you' => null,
      'user' => $row['message'],
      'offset' => $offset,
      'date' => $row['date']
    ]);
  }
$sql = "SELECT * FROM messages WHERE (user1='$user1' AND user2='$user2' OR user1='$user2' AND user2='$user1') AND id < $offset ORDER BY date DESC LIMIT $limit";
$messages = $conn->query($sql);
while($row = $messages->fetch()){
   $offset = $row['id'];
   if($user1 == $row['user1']){
       array_push($result,[
         'you' => $row['message'],
         'user' => null,
         'offset' => $offset,
         'date' => $row['date']
       ]);
   }
   else{
    array_push($result,[
        'you' => null,
        'user' => $row['message'],
        'offset' => $offset,
        'date' => $row['date']
      ]);
   }
} 
}
catch(PDOexception $e){
      $result = ['error' => $e->getMessage(), 'offset'=>$offset];
}
echo json_encode([
  'result' => $result,
  'unSeenResult' => $unSeenResult
  ]);