<?php
function sanitize($s): string {
  return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}
function onlyAlphaSpaces($s) { return preg_match('/^[A-Za-z ]+$/', $s); }
function onlyAlpha($s) { return preg_match('/^[A-Za-z]+$/', $s); }
function validEmailKenangin($email) {
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
  if (preg_match('/(^[.@])|([.@]$)|(@@)|(\.\.)|(@\.)|(\.@)/', $email)) return false;
  return true;
}
function flash($type, $msg) {
  $_SESSION['flash'][] = ['type'=>$type, 'msg'=>$msg];
}
function getFlash() {
  $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f;
}