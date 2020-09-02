<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);

$limit = $data->limit;
$offset = $data->offset;
$postName = $data->postName;
$result = [];

try{
$sql = "SELECT comment , userName FROM comments WHERE postName='$postName' LIMIT $limit OFFSET $offset";
$comments = $conn->query($sql);

while($row = $comments->fetch()){
    array_push($result,[
        'userName' => $row['userName'],
        'comment' => $row['comment'],
        'postName' => $postName
    ]);
}
}
catch(PDOexception $e){
    $result = ['message' => $e->getMessage()];
}

echo json_encode($result);