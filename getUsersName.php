<?php 
require 'DatabaseConnection.php';
$sql = "SELECT userName FROM users";
    
$data = $conn->query($sql);
$nededData = [];
   while($row = $data->fetch()){
       array_push($nededData, $row);
   }
   echo json_encode($nededData);


?>