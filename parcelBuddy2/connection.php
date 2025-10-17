<?php

$db_host = '127.0.0.1:3308';
$db_name = 'ParcelBuddy';
$db_user = 'root';
$db_password = '';

global $connection;
try {
  $connection = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_password);
} catch (PDOException $e) {
  echo "Connection Failed. ";
  die();
}