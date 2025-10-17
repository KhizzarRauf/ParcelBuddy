<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'Models/DeliveryPoint.php';

if(isset($_POST['update'])){
    $parcel_model = new DeliveryPoint();
    $result = $parcel_model->update();
    header("Location: AdminDashboard.php");;
}else{
    include 'Views/update.phtml';
}

