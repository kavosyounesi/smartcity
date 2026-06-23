<?php
/**
 * تنظیمات سامانه «لنده هوشمند»
 * این فایل را با اطلاعات هاست cPanel خودتان ویرایش کنید.
 */

// ====== اطلاعات پایگاه داده ======
// این مقادیر را پس از ساخت دیتابیس از بخش MySQL Databases در cPanel جایگزین کنید.
// نام دیتابیس و نام کاربری معمولاً به‌صورت  cpanelusername_dbname  هستند.
define('DB_HOST', 'localhost');
define('DB_NAME', 'CPANELUSER_lendeh');
define('DB_USER', 'CPANELUSER_lendeh');
define('DB_PASS', 'PUT_YOUR_DATABASE_PASSWORD_HERE');

// ====== ورود مدیر سامانه ======
// نام کاربری پیش‌فرض: admin   |   رمز عبور پیش‌فرض: lendeh1404
// برای تغییر رمز عبور: فایل api/generate_password.php را یک‌بار در مرورگر باز کنید،
// رمز هش‌شده جدید را در ADMIN_PASSWORD_HASH جایگزین کنید و سپس آن فایل را حذف کنید.
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', '$2b$12$16TCjsKEeWSfA2oo6s6hle1uK0CcP5y8CYIXQ6FeWDVnqj4mqpmtC');

// ====== تنظیمات نشست مدیریتی ======
define('SESSION_NAME', 'lendeh_admin_session');

// ====== تنظیمات نشست کارمندان (فرماندار / شهردار) ======
define('STAFF_SESSION_NAME', 'lendeh_staff_session');

// ====== تنظیمات نشست کاربران (شهروندان) ======
define('USER_SESSION_NAME', 'lendeh_user_session');

// ====== ایمیل ارسالی هنگام ثبت‌نام (با تابع mail داخلی PHP) ======
define('MAIL_FROM', 'no-reply@yourdomain.ir');
define('MAIL_FROM_NAME', 'لنده هوشمند');

// ====== تنظیمات پیامک (کاوه‌نگار) ======
// برای دریافت API Key به پنل کاوه‌نگار (kaveh.ir) مراجعه کنید
// در صورت استفاده از سرویس دیگر، فایل api/otp.php را ویرایش کنید
define('SMS_PROVIDER', 'kavenegar');  // kavenegar | melipayamak | disabled
define('SMS_API_KEY', 'PUT_YOUR_KAVENEGAR_API_KEY_HERE');
define('SMS_SENDER', '1000596446');   // شماره فرستنده (از پنل کاوه‌نگار)
define('OTP_EXPIRE_MINUTES', 5);      // مدت اعتبار کد OTP (دقیقه)
