<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'Models/DeliveryPoint.php';
if (isset($_POST['store'])) {
    $parcel_model = new DeliveryPoint();
    $result = $parcel_model->store();
    if($result){
       header("Location:AdminDashboard.php");
    }else{
        echo "Something Went Wrong";
        include("Location:AdminDashboard.php");
    }
}

include 'Views/create.phtml';
