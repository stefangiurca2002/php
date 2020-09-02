<?php
 require 'DatabaseConnection.php';
$encodedData = file_get_contents("php://input");
$data = json_decode($encodedData);

$like = $data->like;
$dislike = $data->dislike;
$PostName = $data->postName;
$user = $data->userName;
$userName = $data->userRated;

try {
  $sql = "SELECT user FROM usersRate WHERE user = '$user' AND PostName = '$PostName'";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
   $ok = false;
 while($row = $stmt->fetch()){
     $ok = true;
 break;
 }
  if($ok){
      $sql = "UPDATE usersRate SET likes=$like, dislikes=$dislike WHERE user = '$user' AND PostName = '$PostName'";
  }
  else{
      $sql = "INSERT INTO usersRate (user, userName, PostName, likes, dislikes) VALUES ('$user', '$userName', '$PostName', '$like', '$dislike')";
  }
  $conn->exec($sql);

  $sql = "SELECT likes, dislikes FROM usersRate WHERE userName='$userName' AND PostName='$PostName'";

  $usersRate = $conn->query($sql);
  $feedback = 0;
  $usersLikes = 0;
  $rate = 0;
  $score = 0;
  while($row = $usersRate->fetch()){
     if($row['likes'] == 1){
         $usersLikes++;
         $feedback++;
     }
     else if($row['dislikes'] == 1)
    { 
      $feedback++;
    }
  }
  if($feedback){
    $rate = $usersLikes/$feedback;
    if($rate === 1){
      $score = 10*$usersLikes;
    }else{
      $score = 5*$rate*$usersLikes;
    }
  }
  $usersDislikes = $feedback - $usersLikes;
  $rate *= 5;
  $sql = "UPDATE Posts SET likes=$usersLikes, dislikes=$usersDislikes, score=$score, rate='$rate' WHERE PostName='$PostName' AND userName='$userName'";
  $conn->exec($sql);
  $sql = "SELECT rate FROM Posts WHERE userName='$userName'";
  $posts = $conn->query($sql);
  $NOPosts = 0;
  $newRate = 0;
  while($row = $posts->fetch()){
      $newRate += $row['rate'];
      $NOPosts++;
  }
  if($NOPosts)
  $newRate /= $NOPosts;
  echo json_encode([
    'newRate' => $newRate,
    'rate' => $rate,
   'NOlikes' => $usersLikes,
   'NOdislikes' => $feedback - $usersLikes,
   'score' => $score]);
} catch(PDOException $e) {
  echo json_encode([
      'like' => $like,
      'NOlikes' => $usersLikes,
      'NOdislikes' => $feedback - $usersLikes,
      'dislike' => $dislike,
      'PostName' => $PostName,
      'dbName'=> $dbname,
       'data'=>$data,
      'msg'=> $e->getMessage()
  ]);
}