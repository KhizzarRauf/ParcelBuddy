<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'Models/DeliveryUser.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $loginModel = new DeliveryUser();
    $user = $loginModel->login($_POST['username'], $_POST['password']);

    if (!$user) {
        $_SESSION['login_error'] = "Email or Password Did not Matched!";
        require_once('Views/index.phtml');
    } else {
        // Set session 
        $_SESSION['loggedIn'] = true;
        $_SESSION['username'] = $_POST['username'];
        $_SESSION['user_id'] = $user['userid'];
        $_SESSION['role'] = $user['userType'];

        // Redirect based on user type
        if ($user['userType'] == 1) {
            header("Location: AdminDashboard.php");
            exit();
        } else {
            header("Location: UserDashboard.php");
            exit();
        }
    }
} else {
    require_once('Views/index.phtml');
}

