<?php
include '../connection.php';

$id = $_GET['id'];
$status = $_GET['status'];

$stmt = $connection->prepare("UPDATE delivery_point SET status = :status WHERE id = :id");
$stmt->bindParam(':status', $status, PDO::PARAM_INT);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

$stmt->execute();

return true; 
?>
