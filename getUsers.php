<?php 
require 'DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$limit = $data->limit;
$offset = $data->offset;
$userName = $data->userName;
$searchUser = $data->search;
$result = [];
$friends = [];
if(true){
try{
    if(isset($searchUser)){
       $sql = "SELECT userName, ProfileImage, locale FROM users WHERE userName like '$searchUser%' LIMIT $limit OFFSET $offset";
       $searchedUsers = $conn->query($sql);
       while($row = $searchedUsers->fetch()){
           array_push($result, $row);
       }
       $offset += $limit; 
    }else{
//     $country = $conn->query("SELECT country FROM users WHERE userName='$userName'")->fetch();
//     $country = $country['country'];
// $sql = "SELECT userFriend FROM usersFriendsIstoric WHERE userName='$userName'";
// $usersViewed = $conn->query($sql);
// $arrayOfUsersViewed = [];
// while($row = $usersViewed->fetch()){
//     array_push($arrayOfUsersViewed, $row['userFriend']);
// }
// while(count($result)<$limit){
//     $sql = "SELECT userName, country, ProfileImage FROM users WHERE country='$country' ORDER BY id DESC LIMIT 1 OFFSET $offset";
//     $userDetails = $conn->query($sql)->fetch();

//     if(!$userDetails)
//     break;

//     for($i=0; $i<count($arrayOfUsersViewed); $i++){
//         if($arrayOfUsersViewed[$i] == $userDetails['userName'] || $userName == $userDetails['userName']){
//         break;
//         }
//     }
//     if($i == count($arrayOfUsersViewed)){
//         array_push($result,[
//           'userName' => $userDetails['userName'],
//           'ProfileImage' => $userDetails['ProfileImage'],
//           'country' => $userDetails['country']
//         ]);
//         $userFriendName = $userDetails['userName'];
//         $sql = "INSERT INTO usersFriendsIstoric (userName, userFriend) VALUES ('$userName', '$userFriendName')";
//         $conn->exec($sql);
//     }
//        $offset++;
// }

$sql = "SELECT friendName FROM friends WHERE userName='$userName'";
$information = $conn->query($sql);
$i = 0;
while($row = $information->fetch()){
   $friends[$i++] = $row['friendName'];
}
$ok = true;
while(count($result) < $limit && $ok){
$ok = false;
$sql = "SELECT userName, ProfileImage, locale FROM users ORDER BY id DESC LIMIT $limit OFFSET $offset";
$users = $conn->query($sql);
while($row = $users->fetch()){
    $ok = true;
    for($i = 0; $i<count($friends); $i++){
       if($friends[$i] === $row['userName']){
       break;
       }
    }
    if($i == count($friends)){
        array_push($result,$row);
    }
}
if($ok)
$offset += $limit;
}
    }
}
catch(PDOexception $e){
    $result = ['message' => $e->getMessage()];
}
echo json_encode([
    'result' => $result,
    'offset' => $offset
]);
}