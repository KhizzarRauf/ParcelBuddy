<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'Views/UserDashboard.phtml';
