<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = db();

if ($method === 'GET') {
  // بررسی نشست کارمند (برای حفظ ورود هنگام رفرش)
  json_response(['staff' => current_staff()]);
}

if ($method === 'POST') {
  $body = get_json_body();
  $action = $body['action'] ?? 'login';

  if ($action === 'logout') {
    start_staff_session();
    $_SESSION = [];
    session_destroy();
    json_response(['ok' => true]);
  }

  // login
  $username = clean_str($body['username'] ?? '', 100);
  $password = is_string($body['password'] ?? null) ? $body['password'] : '';

  $stmt = $pdo->prepare("SELECT * FROM staff WHERE username = ?");
  $stmt->execute([$username]);
  $row = $stmt->fetch();

  if (!$row || !password_verify($password, $row['password_hash'])) {
    json_response(['error' => 'نام کاربری یا رمز عبور نادرست است.'], 401);
  }

  start_staff_session();
  $_SESSION['staff'] = [
    'id'   => intval($row['id']),
    'username' => $row['username'],
    'role' => $row['role'],
    'name' => $row['name'],
  ];
  json_response(['staff' => $_SESSION['staff']]);
}

json_response(['error' => 'متد نامعتبر است.'], 405);
