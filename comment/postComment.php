<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userName = $data->userName;
$postName = $data->postName;
$comment = $data->comment;
$result = [];
if(isset($data)){
$sql = "INSERT INTO comments (userName, postName, comment) VALUES ('$userName','$postName','$comment')";
$conn->exec($sql);

$result = [
    'comment' => $comment
];
}
else{
    echo json_encode('empty data');
}
echo json_encode($result);