<?php 
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userName = $data->userName;
$follow = 0;
$followers = 0;

try{
    $sql = "SELECT * FROM friends WHERE userName='$userName' OR friendName='$userName'";
    $frineds = $conn->query($sql);
    while($row = $frineds->fetch()){
        array_push($arr, $row);
        if($row['userName'] === $userName){
            $follow++;
        }
        else{
          $followers++;
        }
    }
    echo json_encode([
       'followers' => $followers,
       'follow' => $follow
    ]);
}
catch(PDOexception $e){
    echo json_encode(['error' => $e->getMessage()]);
}