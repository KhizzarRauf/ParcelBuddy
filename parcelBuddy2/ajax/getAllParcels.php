<?php
$limit = $_GET['limit'];
$offset = $_GET['offset'];

include '../connection.php';

// read request
$parcel_id = $_GET['parcel_id'] ?? '';
$postal_code = $_GET['postcode'] ?? '';
$name = $_GET['name'] ?? '';
$address_1 = $_GET['address_1'] ?? '';
$address_2 = $_GET['address_2'] ?? '';
$sort = $_GET['sort'] ?? 'id';
$limit = 100; // You can adjust the limit as needed

global $connection;

// query to get all records and filter
$stmt = $connection->prepare("SELECT delivery_point.*, delivery_status.status_text 
FROM delivery_point
JOIN delivery_status ON delivery_point.status = delivery_status.id
WHERE (
    delivery_point.id LIKE :parcel_id 
    AND delivery_point.postcode LIKE :postal_code 
    AND delivery_point.name LIKE :name 
    AND delivery_point.address_1 LIKE :address_1 
    AND delivery_point.address_2 LIKE :address_2
) 
ORDER BY $sort 
LIMIT $limit OFFSET $offset");

// bind params and execute
$stmt->bindValue(':parcel_id', '%' . $parcel_id . '%');
$stmt->bindValue(':postal_code', '%' . $postal_code . '%');
$stmt->bindValue(':name', '%' . $name . '%');
$stmt->bindValue(':address_1', '%' . $address_1 . '%');
$stmt->bindValue(':address_2', '%' . $address_2 . '%');

$stmt->execute();

$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get users data with limit and offset
$points = [];
foreach ($res as $row) {
    $temp = [
        'parcel_id' => $row['id'],
        'name' => $row['name'],
        'address_1' => $row['address_1'],
        'address_2' => $row['address_2'],
        'postcode' => $row['postcode'],
        'lat' => $row['lat'],
        'lng' => $row['lng'],
        'deliverer' => $row['deliverer'],
        'del_photo' => $row['del_photo'],
        'status_text' => $row['status_text'],
    ];
    array_push($points, $temp);
}

echo json_encode($points);
?>
