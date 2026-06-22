<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = db();

function send_welcome_email($email, $name) {
  $subject = '=?UTF-8?B?' . base64_encode('خوش آمدید به لنده هوشمند') . '?=';
  $message = "سلام " . $name . "،\n\nثبت‌نام شما در سامانه شهر هوشمند لنده با موفقیت انجام شد.\n\nبا تشکر\nتیم لنده هوشمند";
  $headers = "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
  $headers .= "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
  // ارسال ایمیل به‌صورت بی‌صدا انجام می‌شود؛ در صورت عدم پشتیبانی هاست از mail()، روند ثبت‌نام مختل نخواهد شد.
  @mail($email, $subject, $message, $headers);
}

if ($method === 'GET') {
  if (isset($_GET['admin'])) {
    require_admin();
    $stmt = $pdo->query("SELECT id, name, email, created_at FROM users ORDER BY id DESC");
    json_response($stmt->fetchAll());
  }
  json_response(['user' => current_user()]);
}

if ($method === 'DELETE') {
  require_admin();
  $id = intval($_GET['id'] ?? 0);
  if ($id <= 0) json_response(['error' => 'شناسه نامعتبر است.'], 400);
  $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
  $stmt->execute([$id]);
  json_response(['ok' => true]);
}

if ($method === 'POST') {
  $body = get_json_body();
  $action = $body['action'] ?? 'login';

  if ($action === 'logout') {
    start_user_session();
    $_SESSION = [];
    session_destroy();
    json_response(['ok' => true]);
  }

  if ($action === 'register') {
    $name = clean_str($body['name'] ?? '', 200);
    $email = strtolower(clean_str($body['email'] ?? '', 200));
    $password = is_string($body['password'] ?? null) ? $body['password'] : '';

    if ($name === '' || $email === '' || strlen($password) < 6) {
      json_response(['error' => 'نام، ایمیل و رمز عبور (حداقل ۶ کاراکتر) را کامل وارد کنید.'], 422);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      json_response(['error' => 'ایمیل وارد شده معتبر نیست.'], 422);
    }

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);
    if ($check->fetch()) {
      json_response(['error' => 'این ایمیل قبلاً ثبت‌نام شده است. وارد حساب کاربری خود شوید.'], 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hash]);
    $id = $pdo->lastInsertId();

    send_welcome_email($email, $name);

    start_user_session();
    $_SESSION['user'] = ['id' => $id, 'name' => $name, 'email' => $email];
    json_response(['user' => $_SESSION['user']], 201);
  }

  // action === login
  $email = strtolower(clean_str($body['email'] ?? '', 200));
  $password = is_string($body['password'] ?? null) ? $body['password'] : '';

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $row = $stmt->fetch();

  if (!$row || !password_verify($password, $row['password_hash'])) {
    json_response(['error' => 'ایمیل یا رمز عبور نادرست است.'], 401);
  }

  start_user_session();
  $_SESSION['user'] = ['id' => $row['id'], 'name' => $row['name'], 'email' => $row['email']];
  json_response(['user' => $_SESSION['user']]);
}

json_response(['error' => 'متد نامعتبر است.'], 405);
