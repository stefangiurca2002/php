<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$code = $data->code;
try{
$conn->exec("DELETE FROM users WHERE code='$code'");
}
catch(PDOexception $e){
    echo $e->getMessage();
}