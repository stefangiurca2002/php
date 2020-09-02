<?php
require '../DatabaseConnection.php'; 
$data = file_get_contents("php://input");
$data = json_decode($data);
$email = $data->email;
$token = $data->token;
$result = [];
if($data){
    try{
        $sql = "SELECT userName FROM divaces WHERE email='$email'";
        $userName = $conn->query($sql)->fetch();
        $userName = $userName['userName'];
        if($userName){
           $result=[
               'isValid' => true,
               'userName'=>$userName
            ];
           $sql = "INSERT INTO divaces (userName, token, email) VALUES ('$userName','$token', '$email')";
        }
        else{
            $result = [
                 'isValid' => false,
                 'userName' => null
            ];
        }
    }
    catch(PDOexception $e){
      $result = ['err' => $e->getMessage()];
    }
}
echo json_encode($result);