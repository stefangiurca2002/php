<?php
require 'DatabaseConnection.php';
$encodedData = file_get_contents("php://input");
$data = json_decode($encodedData);

$PostName = $data->images;
$user = $data->user;

$result = [];
try {
  for($i=0; $i<count($PostName); $i++){
      $post = $PostName[$i]->url;
      $sql = "SELECT * FROM usersRate WHERE user='$user' AND PostName='$post'";
    $PostData = $conn->query($sql);
      $rate = 0;
      $likes = 0;
      $rating = 0;
      $userLiked = false;
      $userDisliked = false;
       while($row = $PostData->fetch()){
          if($row['user'] == $user){
              if($row['likes'] == 1){
                  $userLiked = true;
              }
              if($row['dislikes']){
                  $userDisliked = true;
              }
          }
       }

      array_push($result, array(
          'like'=>$userLiked,
          'dislike'=>$userDisliked,
          'PostName'=>$post
      ));
  }
  echo json_encode($result);
}
catch(PDOException $e) {
    //echo  $e->getMessage();
    echo json_encode([
        'PostsUrl' => $PostName,
        'dbName'=> $user,
        'msg'=> $e->getMessage()
    ]);
  }