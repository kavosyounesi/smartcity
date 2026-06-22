<?php
/**
 * این فایل فقط برای ساخت رمز عبور هش‌شده‌ی جدید استفاده می‌شود.
 * پس از استفاده، حتماً این فایل را از روی هاست حذف کنید.
 *
 * نحوه استفاده: آدرس زیر را در مرورگر باز کنید
 * https://yourdomain.com/api/generate_password.php?password=رمز-جدید-من
 */
header('Content-Type: text/plain; charset=utf-8');

$password = $_GET['password'] ?? '';
if ($password === '') {
  echo "رمز عبور موردنظر را به‌صورت ?password=YOUR_PASSWORD در آدرس وارد کنید.";
  exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
echo "رمز هش‌شده‌ی جدید شما:\n\n" . $hash . "\n\n";
echo "این مقدار را در فایل api/config.php جای‌گزین مقدار ADMIN_PASSWORD_HASH کنید،\n";
echo "سپس همین فایل (generate_password.php) را از روی هاست حذف کنید.";
