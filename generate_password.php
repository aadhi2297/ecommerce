<?php
$password = '123'; // Your chosen password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;
?>