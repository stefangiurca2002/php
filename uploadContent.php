<?php 
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

require 'DatabaseConnection.php';
$response = array();
$server_url = 'http://192.168.1.8';
$userCode = key($_FILES);
$userCode = base64_decode(str_replace('_','=',$userCode));
try{
$user = $conn->query("SELECT userName FROM users WHERE code='$userCode'")->fetch();
$userName = $user['userName'];
$country = $user['locale'];
}catch(PDOexception $e){
    die($e->getMessage());
}
$userName = str_replace('=','_',base64_encode($userName));
if(!file_exists('uploads/'.$userName)){
        if(!mkdir('uploads/'.$userName, 0777)){
            $response = array(
                "status" => "error",
                "error" => true,
                "message" => "nu mere diri",
                'diriName' => $userName
            );
        }
}
  if($_FILES[key($_FILES)] && !$response)
{
    $upload_dir = 'uploads/'.$userName.'/';

    $image_name = $_FILES[key($_FILES)]["name"];
    $image_tmp_name = $_FILES[key($_FILES)]["tmp_name"];
    $error = $_FILES[key($_FILES)]["error"];
    $imageSize = $_FILES[key($_FILES)]["size"];
        if($error > 0){
        $response = array(
            "status" => "error",
            "error" => true,
            "message" => "Error uploading the file dada!"
        );
    }
        $extension = explode('.', $image_name);
        $random_name = date("Y-m-d-H-i-s").'.'.$extension[count($extension) - 1];
        $upload_name = $upload_dir.strtolower($random_name);
        $upload_name = preg_replace('/\s+/', '-', $upload_name);

        if(move_uploaded_file($image_tmp_name , $upload_name)) {
            $sql = "INSERT INTO Posts (userName, country, PostName, likes, dislikes, score, rate) VALUES ('$userName', '$country', '$random_name', 0, 0, 0, 0)";
            $conn -> exec($sql);
            $response = array(
                "status" => "success",
                "error" => false,
                "message" => "File uploaded successfully",
                "fileName" => strtolower($random_name),
                'country' => $country,
                'user' => $user,
                'code' => $userCode
              );
        }else
        {
            $response = array(
                "status" => "cannot upload",
                "error" => $error,
                "message" => "Error uploading the file!",
                "iamgeName" => $image_name,
                "image_tmp_name" => $image_tmp_name,
                "upload_name" => $upload_name,
                "image_size" => $imageSize
            );
        }
    }
else if(!$response){
    $response = array(
        "status" => "error",
        "error" => true,
        "message" => "No file was sent!",
        "file" => $_FILES[key($_FILES)]
    );
}
echo json_encode($response);