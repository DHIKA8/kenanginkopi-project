<?php
session_start();


if (!isset($_SESSION['user']) && isset($_COOKIE['remember_user_id'])) {
    require_once __DIR__ . '/db.php';
    $uid = $_COOKIE['remember_user_id'];

    $stmt = $mysqli->prepare("SELECT UserID, FullName, UserName, UserEmail, UserRole FROM users WHERE UserID = ?");
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($user = $res->fetch_assoc()) {
        $_SESSION['user'] = $user;
    }
}