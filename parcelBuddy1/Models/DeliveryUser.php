<?php

class DeliveryUser
{
    function login()
    {
        include 'connection.php';
        global $connection;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $password = sha1(filter_var($_POST['password'], FILTER_SANITIZE_STRING));
        //get user
        $users = $connection->query("SELECT * FROM delivery_users WHERE delivery_users.username = '{$username}' AND delivery_users.password = '{$password}'");
        if ($users->rowCount()  == 0) {
            //user not found
            return  false;
        } else {
            //user found
            $user = $users->fetch();
            return $user;
        }
    }

    function getDeliverers()
    {
        include 'connection.php';
        global  $connection;
        return $connection->query("SELECT * FROM delivery_users WHERE userType = 2");
    }

}
