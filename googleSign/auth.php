<?php
require 'vendor/autoload.php';
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$token = $data->token;
$divaceToken = $data->divaceToken;
$code = bin2hex(random_bytes(10));
if($token){
$clientID = '518963214604-cslceeunq8bhpvsgm0m67a57q3cf6m6v.apps.googleusercontent.com';
$clientSecret = 'BKce0XpvYiE8kZjINyhmJbFB';
//$redirectUri = 'http://192.168.1.8/mediaApp-BackEnd/googleSign/auth.php';

$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
// $client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");
$client->setAccessToken($token);

 $google_oauth = new Google_Service_Oauth2($client);
 try{
    $google_oauth->userinfo->get();
     $google_account_info = $google_oauth->userinfo->get();
     $email = $google_account_info->email;
     $locale = $google_account_info->locale ? $google_account_info->locale : null;
     $name = $google_account_info->name;
     try{
        $sql = "SELECT userName, code FROM users WHERE email='$email'";
        $user = $conn->query($sql)->fetch();
        $userName = $user['userName'];
        if($userName){
            $code = $user['code'];
            if($divaceToken){
            try{
                 $conn->exec("INSERT INTO divaces (userName, token) VALUES ('$userName','$divaceToken')");
            }
            catch(PDOexception $e){
                die($e->getMessage());
            }
        }
        }
        else{
            $conn->exec("INSERT INTO users (userName,email,googleName,locale,code,ProfileImage) VALUES ('','$email','$name','$locale','$code','defaultImage/download.jfif')");
        }
     }
     catch(PDOexception $e){
         die($e->getMessage());
     }
 }
 catch(Exception $e){
     die($e);
 }
 echo json_encode([
    'userName' => $userName,
    'code' => $code
]);
}