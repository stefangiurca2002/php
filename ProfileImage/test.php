<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userName = $data->userName;
$image = $data->image;
die(json_encode([
'data' => $data,
'image' => $image,
'userName'=>$userName
]));