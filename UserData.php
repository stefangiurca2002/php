<?php
require 'DatabaseConnection.php';
$encodedData = file_get_contents("php://input");
$data = json_decode($encodedData);
$limit = $data->limit;
$offset = $data->offset;
$userName = $data->userName;
$result = [];

try{
$sql = "SELECT PostName, likes, dislikes, rate FROM Posts WHERE userName='$userName' ORDER BY date DESC LIMIT $limit OFFSET $offset";
$posts = $conn->query($sql);
while($row = $posts->fetch()){
    array_push($result,[
        'url'=>$row['PostName'],
        'likes'=>$row['likes'],
        'dislikes'=>$row['dislikes'],
        'Rating'=>$row['rate']
        ]);
}
}
catch(PDOexception $e){
$result = ['err' => $e->getMessage()];
}
echo(json_encode($result));