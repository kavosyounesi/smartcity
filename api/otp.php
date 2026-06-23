<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST') json_response(['error' => 'متد نامعتبر است.'], 405);

$body = get_json_body();
$action = $body['action'] ?? '';

/* -------- ارسال کد OTP -------- */
if ($action === 'send') {
  $phone = preg_replace('/[^0-9]/', '', $body['phone'] ?? '');
  if (strlen($phone) !== 11 || $phone[0] !== '0') {
    json_response(['error' => 'شماره موبایل معتبر نیست (فرمت: 09xxxxxxxxx).'], 422);
  }

  // جلوگیری از ارسال مکرر (حداکثر یک OTP هر ۲ دقیقه)
  $recent = $pdo->prepare("SELECT id FROM otp_codes WHERE phone = ? AND created_at > DATE_SUB(NOW(), INTERVAL 2 MINUTE) AND used = 0");
  $recent->execute([$phone]);
  if ($recent->fetch()) {
    json_response(['error' => 'کد قبلی هنوز معتبر است. لطفاً چند دقیقه صبر کنید.'], 429);
  }

  $code = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
  $expires = date('Y-m-d H:i:s', time() + OTP_EXPIRE_MINUTES * 60);

  // پاک‌سازی کدهای قدیمی این شماره
  $pdo->prepare("DELETE FROM otp_codes WHERE phone = ?")->execute([$phone]);

  $pdo->prepare("INSERT INTO otp_codes (phone, code, expires_at) VALUES (?, ?, ?)")->execute([$phone, $code, $expires]);

  $sent = send_sms_otp($phone, $code);

  if (!$sent && SMS_PROVIDER !== 'disabled') {
    json_response(['error' => 'ارسال پیامک با خطا مواجه شد. لطفاً بعداً تلاش کنید یا با ایمیل ثبت‌نام کنید.'], 500);
  }

  // در حالت disabled (آزمایشی)، کد را در پاسخ برمی‌گردانیم تا توسعه‌دهنده بتواند تست کند
  $resp = ['ok' => true, 'expires_in' => OTP_EXPIRE_MINUTES * 60];
  if (SMS_PROVIDER === 'disabled') $resp['_dev_code'] = $code; // فقط برای تست - در محیط واقعی حذف کنید
  json_response($resp);
}

/* -------- تایید کد و ورود/ثبت‌نام -------- */
if ($action === 'verify') {
  $phone = preg_replace('/[^0-9]/', '', $body['phone'] ?? '');
  $code  = clean_str($body['code'] ?? '', 10);
  $name  = clean_str($body['name'] ?? '', 200);

  $stmt = $pdo->prepare("SELECT * FROM otp_codes WHERE phone = ? AND code = ? AND used = 0 AND expires_at > NOW()");
  $stmt->execute([$phone, $code]);
  $otp = $stmt->fetch();
  if (!$otp) {
    json_response(['error' => 'کد وارد شده نادرست یا منقضی شده است.'], 401);
  }

  // علامت‌گذاری به‌عنوان استفاده‌شده
  $pdo->prepare("UPDATE otp_codes SET used = 1 WHERE id = ?")->execute([$otp['id']]);

  // بررسی اینکه آیا کاربر با این شماره قبلاً ثبت‌نام کرده است
  $existing = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $existing->execute([$phone . '@sms.local']);
  $user = $existing->fetch();

  if (!$user) {
    if ($name === '') $name = 'کاربر ' . substr($phone, -4);
    $hash = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phone . '@sms.local', $hash]);
    $id = $pdo->lastInsertId();
    $user = ['id' => $id, 'name' => $name, 'email' => $phone . '@sms.local', 'phone' => $phone];
  } else {
    $user['phone'] = $phone;
  }

  start_user_session();
  $_SESSION['user'] = ['id' => intval($user['id']), 'name' => $user['name'], 'email' => $user['email'], 'phone' => $phone];
  json_response(['user' => $_SESSION['user']]);
}

json_response(['error' => 'عملیات نامعتبر است.'], 400);

/* -------- تابع ارسال پیامک -------- */
function send_sms_otp($phone, $code) {
  if (SMS_PROVIDER === 'disabled') return true;

  $message = urlencode("کد تایید لنده هوشمند: {$code}\nاین کد " . OTP_EXPIRE_MINUTES . " دقیقه معتبر است.");

  if (SMS_PROVIDER === 'kavenegar') {
    // کاوه‌نگار REST API
    $url = "https://api.kavenegar.com/v1/" . SMS_API_KEY . "/sms/send.json";
    $params = "receptor={$phone}&sender=" . SMS_SENDER . "&message={$message}";
    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $params,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = json_decode($response, true);
    return $httpCode === 200 && isset($result['return']['status']) && $result['return']['status'] === 200;
  }

  if (SMS_PROVIDER === 'melipayamak') {
    // ملی پیامک
    $url = "https://rest.payamak-panel.com/api/SendSMS/SendSMS";
    $params = json_encode([
      'username' => SMS_API_KEY,
      'password' => defined('SMS_PASSWORD') ? SMS_PASSWORD : '',
      'to' => $phone,
      'from' => SMS_SENDER,
      'text' => urldecode($message),
      'isflash' => false
    ]);
    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $params,
      CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 10,
      CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $httpCode === 200;
  }

  return false;
}
