<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_GET['delete'])) {
    include_once('Models/DeliveryPoint.php');

    $deleteModel = new DeliveryPoint();
    $deleteModel->delete();
    header("Location: AdminDashboard.php");
}
include 'Views/AdminDashboard.phtml';
