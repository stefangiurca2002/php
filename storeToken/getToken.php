<?php
require '../DatabaseConnection.php';
$code = bin2hex(random_bytes(40));
echo $code.'/n'.strlen($code);