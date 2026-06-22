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

// ====== تنظیمات نشست کاربران (شهروندان) ======
define('USER_SESSION_NAME', 'lendeh_user_session');

// ====== ایمیل ارسالی هنگام ثبت‌نام (با تابع mail داخلی PHP) ======
define('MAIL_FROM', 'no-reply@yourdomain.ir');
define('MAIL_FROM_NAME', 'لنده هوشمند');
