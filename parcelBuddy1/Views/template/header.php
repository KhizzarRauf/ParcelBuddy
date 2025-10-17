<?php
include 'connection.php';

if (isset($_GET['logout'])) {
    session_destroy();
?>
    <script type="text/javascript">
        window.location.href = 'index.php';
    </script>
<?php
}
include_once('Models/DeliveryUsersType.php');

$roleModel = new DeliveryUsersType();

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Parcel Buddy</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>

<body style="background-image: url(images/bg.png); background-size: cover;">

    <div style="background-color: darkslateblue;height:130px" class=" p-3 text-center">
        <img src="images/logo.png" style="position:absolute;left:0;top:0" height="120" alt="">
            <h2 class="text-white p-2">ParcelBuddy <?= isset($_SESSION['loggedIn']) ? 'Dashboard' : '' ?></h2>

            <span style="position:absolute;right:20px;top:40px;" class="text-white"><?php 
                if(isset( $_SESSION['username']) && isset($_SESSION['role'])){
                    echo "Logged in as : ".  $_SESSION['username']. " - ".$roleModel->getUserTypeName($_SESSION['role'])."";
                }
            ?></span>
    </div>

    <div class="d-flex mt-2 nav justify-content-end">
        <?php if (isset($_SESSION['loggedIn'])) : ?>
            <?php if ($_SESSION['role'] == 1) : ?>
                <a href='AdminDashboard.php' class="btn btn-info">All Parcels</a>
                <a href='CreateController.php' class="btn btn-info">Add</a>
            <?php elseif ($_SESSION['role'] == 2) : ?>
                <a href='UserDashboard.php' class="btn btn-info">All Parcels</a>
            <?php endif; ?>
            <a href='?logout=true' class="btn btn-info">Logout</a>
        <?php endif; ?>
    </div>