<?php
function requireLogin() {
  if (!isset($_SESSION['user'])) {
    header('Location: login.php?msg=Please+login'); exit;
  }
}

function requireRole($roles = []) {
  if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['UserRole'], $roles)) {
    header('Location: index.php?msg=Unauthorized'); exit;
  }
}

function currentUser() {
  global $mysqli;
  if (!isset($mysqli)) return $_SESSION['user'] ?? null; 

  $uid = $_SESSION['user']['UserID'] ?? null;
  if (!$uid) return null;

  $stmt = $mysqli->prepare("SELECT UserID, FullName, UserName, UserEmail, UserRole FROM users WHERE UserID = ?");
  $stmt->bind_param('s', $uid);
  $stmt->execute();
  return $stmt->get_result()->fetch_assoc();
}



function isAdmin() {
  return (currentUser()['UserRole'] ?? '') === 'Admin';
}

function isUser() {
  return (currentUser()['UserRole'] ?? '') === 'User';
}