<?php
require '../DatabaseConnection.php';
$data = file_get_contents("php://input");
$data = json_decode($data);
$userName = $data->userName;

$sql = "SELECT rate FROM Posts WHERE userName='$userName'";
$rateData = $conn->query($sql);
$lenght = 0;
$rate = 0;
while($row = $rateData->fetch()){
    $lenght++;
    $rate += $row['rate'];
}
if($lenght)
$rate /= $lenght;

$response = array(
    'rate' => $rate,
    'NOposts' => $lenght
);

echo json_encode($response);
