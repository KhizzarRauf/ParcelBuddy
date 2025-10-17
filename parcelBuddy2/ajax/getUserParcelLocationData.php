<?php
include '../connection.php';
global $connection;
session_start();

$userid = $_SESSION['user_id'];

global $connection;
// get data
$query = "SELECT delivery_point.*, delivery_status.status_text 
            FROM delivery_point
            JOIN delivery_status ON delivery_point.status = delivery_status.id
            WHERE 
                delivery_point.deliverer = :userid";

//prepare
$stmt = $connection->prepare($query);
$stmt->bindParam(':userid', $userid);

$stmt->execute();

if ($stmt->errorCode() !== '00000') {
    $errorInfo = $stmt->errorInfo();
    echo "SQL error: {$errorInfo[2]}";
}


$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
// get users data with limit and offset
$locations = array();
$k = 0;
foreach ($res as $row) {

    $stmt2 = $connection->prepare("SELECT * FROM delivery_users WHERE userid = :deliverer");
    $stmt2->bindValue(':deliverer', $row['deliverer']);
    $stmt2->execute();

    $deliverer = $stmt2->fetch(); 

    $temp_array = [
        $row['id'],
        (int)$row['lat'],
        (int)$row['lng'],
        $row['address_1'],
        $row['address_2'],
        $deliverer['username'],
        $row['status_text'],
        $row['name'],


        $k
    ];
    array_push($locations, $temp_array);
    $k++;
}
echo json_encode($locations);
?>