<?php

class DeliveryStatus{
    function getAllStatusList()
    {
        include 'connection.php';
        global  $connection;
        return $connection->query("SELECT * FROM delivery_status");
    }
}