<?php

require 'DatabaseConnection.php'; 
$postUser = file_get_contents("php://input");

if(isset($postUser) && !empty($postUser))
{
  $request = json_decode($postUser);
  $userName = $request->userName;
  $token = $request->token;
  $code = $request->code;
  $userCode = $userName.$code;
  $conn->exec("UPDATE users SET code='$userCode', userName='$userName' WHERE code='$code'");
// if(!$conn->query("SELECT * FROM users WHERE userName='$userName'")->fetch())
//  {
//    $sql = "INSERT INTO users (userName, profileImage) VALUES ('$userName','defaultImage/default.jpg')";
//    $conn->exec($sql);
//  }
if(isset($token)) {
   $sql = "INSERT INTO divaces (userName, token) VALUES ('$userName', '$token')";
    $conn->exec($sql);
  }
    echo json_encode(['succes' => true]);
}