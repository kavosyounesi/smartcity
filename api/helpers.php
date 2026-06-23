<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

function start_admin_session() {
  if (session_status() === PHP_SESSION_ACTIVE) { session_write_close(); }
  session_name(SESSION_NAME);
  session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'samesite' => 'Lax']);
  session_start();
}
function is_admin() {
  start_admin_session(); $result = !empty($_SESSION['admin']); session_write_close(); return $result;
}
function require_admin() {
  if (!is_admin()) json_response(['error' => 'دسترسی غیرمجاز.'], 401);
}

function start_staff_session() {
  if (session_status() === PHP_SESSION_ACTIVE) { session_write_close(); }
  session_name(STAFF_SESSION_NAME);
  session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'samesite' => 'Lax']);
  session_start();
}
function current_staff() {
  start_staff_session();
  $s = isset($_SESSION['staff']) ? $_SESSION['staff'] : null;
  session_write_close(); return $s;
}
// مدیر وبسایت OR فرماندار OR شهردار هر کدام می‌توانند با نقش مشخص وارد شوند
function require_staff_role($roles) {
  // $roles: array of allowed roles, e.g. ['admin','governor'] or ['governor']
  // admin session is also checked
  if (is_admin()) return ['role' => 'admin', 'name' => 'مدیر سامانه'];
  $staff = current_staff();
  if ($staff && in_array($staff['role'], $roles)) return $staff;
  json_response(['error' => 'دسترسی غیرمجاز. ابتدا وارد حساب کاربری خود شوید.'], 401);
}

function start_user_session() {
  if (session_status() === PHP_SESSION_ACTIVE) { session_write_close(); }
  session_name(USER_SESSION_NAME);
  session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'samesite' => 'Lax']);
  session_start();
}
function current_user() {
  start_user_session(); $user = isset($_SESSION['user']) ? $_SESSION['user'] : null;
  session_write_close(); return $user;
}
function require_user() {
  $user = current_user();
  if (!$user) json_response(['error' => 'برای این کار باید وارد حساب کاربری خود شوید.'], 401);
  return $user;
}

function json_response($data, $code = 200) {
  http_response_code($code);
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function get_json_body() {
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  return is_array($data) ? $data : [];
}

function clean_str($v, $maxLen = 2000) {
  $v = is_string($v) ? trim($v) : '';
  return mb_substr($v, 0, $maxLen);
}
