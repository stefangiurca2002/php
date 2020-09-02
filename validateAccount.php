<?php 
require 'DatabaseConnection.php';

$postUser = file_get_contents("php://input");
$data = json_decode($postUser);
$userName = $data->userName;
//$password = $data->password;
$result = array(
    'valid' => true
);
$sql = "SELECT userName FROM users";
$users = $conn->query($sql);
while($row = $users->fetch()){
    if($row['userName'] === $userName){
        $result['valid']=false;
    break;
    }
}
    echo json_encode($result);