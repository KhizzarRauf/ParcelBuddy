<?php

class DeliveryUsersType{
    function getUserTypeName($id){
        include 'connection.php';
        global  $connection;
        $ress = $connection->query("SELECT * FROM delivery_usertype WHERE id = $id");
        if ($ress->rowCount()  == 0) {
            return  '-';
        } else {
            //return user if user found
            $res = $ress->fetch();
            return $res['userTypeName'];
        }
    }
}
