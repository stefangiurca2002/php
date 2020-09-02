<?php
require '../DatabaseConnection.php';
$info = file_get_contents("php://input");
$info = json_decode($info);
$limit = $info->limit;
$offset = $info->offset;
$userName = $info->userName;
$ok = true;
$result = [];
while($ok){
    try{
    $sql = "SELECT * FROM Posts ORDER BY score DESC LIMIT $limit OFFSET $offset";
    $content = $conn->query($sql);
    $post = $content->fetch();

    $postName = $post['userName'].'/'.$post['PostName'];
    $sql = "SELECT * FROM usersIstoric";
    $istoric = $conn->query($sql);
    while($row = $istoric->fetch()){
        if($row['userName'] === $userName && $row['PostName'] === $postName){
        break;
        }
    }
    if(!$istoric->fetch() && isset($userName) && $postName !== '/'){
        $ok = false;
        $sql = "INSERT INTO usersIstoric (userName, PostName) VALUES ('$userName', '$postName')";
        $conn->exec($sql);
    }
    else
    $offset++;
}
catch(PDOexception $e){
    echo json_encode(['message'=> $e->getMessage()]);
}
}
$result = [
  'userName' => $post['userName'],
  'postName' => $post['PostName'],
  'url' => $post['userName'].'/'.$post['PostName'],
  'likes' => $post['likes'],
  'dislikes' => $post['dislikes'],
  'score' => $post['score'],
  'rate' => $post['rate'],
  'limit' => $limit,
  'offset' => $offset,
  'info' => $info
];

echo json_encode($result);