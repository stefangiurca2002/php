<?php 
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userName = $data->userName;
$user = $data->user;
$action = $data->action;
$limit = $data->limit;
$offset = $data->offset;
$result = [];

try{
    if($action === 'follow'){
    $sql = "SELECT friendName FROM friends WHERE userName='$userName' LIMIT $limit OFFSET $offset";
    $index = 'friendName';
}
else{
    $sql="SELECT userName FROM friends WHERE friendName='$userName' LIMIT $limit OFFSET $offset";
    $index = 'userName';
}
$friends = $conn->query($sql);
// get explorer friends
$explorerFriends = $conn->query("SELECT friendName FROM friends WHERE userName='$user'");
$explorerFriendsResult = [];
while($row = $explorerFriends->fetch()){
     array_push($explorerFriendsResult,$row['friendName']);
}
while($row = $friends->fetch()){
  $follower = $row[$index];
  $userDetails = $conn->query("SELECT * FROM users WHERE userName='$follower'");
  $userDetails = $userDetails->fetch();
  for($i=0; $i<count($explorerFriendsResult); $i++){
      if($explorerFriendsResult[$i] === $follower){
        array_push($result, [
                   'friend' => true,
                   'name' => $follower,
                   'country' => $userDetails['country'],
                   'profileImage'=> $userDetails['ProfileImage']
                  ]);
        break;
      }
  }
  if($i == count($explorerFriendsResult)){
    array_push($result, [
               'friend' => false,
               'name' => $follower,
               'country' => $userDetails['country'],
               'profileImage'=> $userDetails['ProfileImage']
              ]);
  }
}
echo json_encode($result);
}
catch(PDOexception $e){
    echo json_encode(['err'=>$e->getMessage()]);
}