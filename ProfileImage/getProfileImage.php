<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userName = $data->userName;
$sql = "SELECT ProfileImage FROM users WHERE userName='$userName'";

$image = $conn->query($sql);
$result = null;

while ($row = $image->fetch()) {
    $result = $row['ProfileImage'];
}

echo json_encode($result);
