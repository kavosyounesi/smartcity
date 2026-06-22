<?php
require_once __DIR__ . '/helpers.php';
start_admin_session();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  // بررسی وضعیت ورود (برای حفظ نشست هنگام رفرش صفحه)
  json_response(['admin' => is_admin()]);
}

if ($method === 'POST') {
  $body = get_json_body();
  $action = $body['action'] ?? 'login';

  if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    json_response(['ok' => true]);
  }

  // action === login
  $username = clean_str($body['username'] ?? '', 100);
  $password = is_string($body['password'] ?? null) ? $body['password'] : '';

  if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
    $_SESSION['admin'] = true;
    json_response(['ok' => true]);
  }

  json_response(['error' => 'نام کاربری یا رمز عبور نادرست است.'], 401);
}

json_response(['error' => 'متد نامعتبر است.'], 405);
