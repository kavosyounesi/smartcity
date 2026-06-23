<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

$DEPARTMENTS = [
  'فرمانداری', 'شهرداری', 'اداره بهداشت و درمان', 'اداره آموزش و پرورش',
  'اداره جهاد کشاورزی', 'اداره راه و شهرسازی', 'اداره میراث فرهنگی و گردشگری',
  'اداره محیط زیست', 'شرکت آب و فاضلاب', 'برق منطقه‌ای', 'سایر',
];

if ($method === 'GET') {
  if (isset($_GET['departments'])) {
    json_response($DEPARTMENTS);
  }

  // فهرست پیام‌ها فقط برای مدیر / کارمندان مجاز
  $to = $_GET['to'] ?? null;

  if (is_admin()) {
    // مدیر همه پیام‌ها را می‌بیند
    $stmt = $to
      ? $pdo->prepare("SELECT * FROM messages WHERE to_role = ? ORDER BY id DESC") : $pdo->query("SELECT * FROM messages ORDER BY id DESC");
    if ($to) $stmt->execute([$to]);
  } else {
    $staff = require_staff_role(['governor', 'mayor']);
    // فرماندار: همه پیام‌ها | شهردار: فقط پیام‌های مخصوص شهرداری
    if ($staff['role'] === 'governor') {
      $stmt = $pdo->query("SELECT * FROM messages ORDER BY id DESC");
    } else {
      $stmt = $pdo->prepare("SELECT * FROM messages WHERE to_role = 'mayor' OR to_role = 'department' ORDER BY id DESC");
      $stmt->execute();
    }
  }
  json_response($stmt->fetchAll());
}

if ($method === 'POST') {
  $action = $_GET['action'] ?? 'send';

  // ------- پاسخ به پیام (فرماندار / شهردار / مدیر) -------
  if ($action === 'reply') {
    $staff = require_staff_role(['admin', 'governor', 'mayor']);
    $id = intval($_GET['id'] ?? 0);
    $body = get_json_body();
    $reply = clean_str($body['reply'] ?? '', 3000);
    if ($reply === '') json_response(['error' => 'متن پاسخ خالی است.'], 422);
    $pdo->prepare("UPDATE messages SET reply = ?, reply_by = ?, reply_date = ?, status = 'replied' WHERE id = ?")
        ->execute([$reply, $staff['name'], clean_str($body['date'] ?? '', 50), $id]);
    json_response(['ok' => true]);
  }

  // ------- بایگانی پیام -------
  if ($action === 'archive') {
    require_staff_role(['admin', 'governor', 'mayor']);
    $id = intval($_GET['id'] ?? 0);
    $pdo->prepare("UPDATE messages SET status = 'archived' WHERE id = ?")->execute([$id]);
    json_response(['ok' => true]);
  }

  // ------- ارسال پیام جدید توسط شهروند -------
  $body = get_json_body();
  $user = current_user();

  $fromName    = $user ? $user['name'] : clean_str($body['from_name'] ?? '', 200);
  $fromContact = $user ? ($user['phone'] ?? $user['email']) : clean_str($body['from_contact'] ?? '', 100);
  $fromUserId  = $user ? $user['id'] : null;
  $toRole      = in_array($body['to_role'] ?? '', ['governor','mayor','department']) ? $body['to_role'] : 'governor';
  $deptName    = clean_str($body['department_name'] ?? '', 200);
  $subject     = clean_str($body['subject'] ?? '', 500);
  $msgBody     = clean_str($body['body'] ?? '', 3000);

  if ($fromName === '' || $subject === '' || $msgBody === '') {
    json_response(['error' => 'نام، موضوع و متن پیام را کامل وارد کنید.'], 422);
  }

  $stmt = $pdo->prepare("INSERT INTO messages (from_user_id, from_name, from_contact, to_role, department_name, subject, body, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([$fromUserId, $fromName, $fromContact, $toRole, $deptName, $subject, $msgBody, clean_str($body['date'] ?? '', 50)]);
  json_response(['ok' => true, 'message' => 'پیام شما با موفقیت ارسال شد.'], 201);
}

if ($method === 'DELETE') {
  require_staff_role(['admin', 'governor']);
  $id = intval($_GET['id'] ?? 0);
  $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([$id]);
  json_response(['ok' => true]);
}

json_response(['error' => 'متد نامعتبر است.'], 405);
