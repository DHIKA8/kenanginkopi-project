<?php
require_once '../config/session.php';

$_SESSION = [];
session_unset();
session_destroy();


if (isset($_COOKIE['remember_user_id'])) {
    setcookie('remember_user_id', '', time() - 3600, '/'); 
}


header('Location: login.php');
exit;