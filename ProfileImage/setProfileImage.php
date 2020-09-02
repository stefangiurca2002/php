<?php
require '../DatabaseConnection.php';
$result = [];
$data = file_get_contents("php://input");
$data = json_decode($data);
$image = $data->image;
//$imageUserName = $data->userName;
$code = $data->userCode;
try{
  $imageUserName = $conn->query("SELECT userName FROM users WHERE code = '$code'")->fetch();
  $imageUserName = $imageUserName['userName'];
  $imageUserName .= date("Y-m-d-H-i-s");
  $userName = base64_decode(str_replace('_','=',$imageUserName));
 }catch(PDOexception $e){
     die($e->getMessage());
 }
if(isset($image)){
  $image = explode(',',$image);
$myfile = fopen('../uploads/ProfileImages/'.$imageUserName, "w") or die(json_encode(['msg' => 'unable to open',
                                                    'userName'=> $imageUserName]));
fwrite($myfile, base64_decode($image[1]));
fclose($myfile);
$sql = "UPDATE users SET ProfileImage='$imageUserName' WHERE userName='$userName'";
$conn->exec($sql);
echo json_encode(['msg'=>'ok',
                  'userName'=>$imageUserName]);
}
else{
$userCode = key($_FILES);
$userCode = str_replace('=',"_",base64_decode($userCode));
try{
    $sql = "SELECT userName FROM users WHERE code='$userCode'";
    $userName = $conn->query($sql)->fetch();
    $userName = $userName['userName'];
    $userName = str_replace('=','_',base64_encode($userName));
}catch(PDOexception $e){
    die($e->getMessage());
}
$upload_dir = '../uploads/ProfileImages/';
$image_name = $_FILES[key($_FILES)]["name"];
$image_tmp_name = $_FILES[key($_FILES)]["tmp_name"];
$error = $_FILES[key($_FILES)]["error"];
$imageSize = $_FILES[key($_FILES)]["size"];
$uploadName = $upload_dir.$userName.$image_name;
$postName = $userName.$image_name;
if($error>0){
    $result = [
       'error' => true
    ];
}
else{
if(move_uploaded_file($image_tmp_name, $uploadName)){
     try{
    // delete old profile image
    $sql = "SELECT ProfileImage FROM users WHERE userName='$userNameDecoded'";
    $image = $conn->query($sql);
    $image = $image->fetch();
    unlink('../uploads/ProfileImages/'.$image['ProfileImage']);
    $sql = "UPDATE users SET ProfileImage='$postName' WHERE userName='$userNameDecoded'";
    $conn->exec($sql);
    $result = [
        'userName' => $postName
    ];}
    catch(PDOException $e){
        $result = [
           'errorMessage' => $e->getMessage()
        ];
    }
}
else{
    $result = [
        'data' => $data,
        'error' => "can't upload",
        'uploadName' => $uploadName,
        'userName'=> $userName,
        'fileName'=> $image_name,
        'fielTmp'=>$image_tmp_name
    ];
}
}
echo json_encode($result);
}
