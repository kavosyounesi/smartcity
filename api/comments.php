<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  // لیست همه نظرات برای پنل‌های مدیریتی
  if (isset($_GET['all'])) {
    require_staff_role(['admin', 'governor', 'mayor']);
    $stmt = $pdo->query("SELECT nc.*, n.title as news_title FROM news_comments nc LEFT JOIN news n ON nc.news_id = n.id ORDER BY nc.id DESC");
    json_response($stmt->fetchAll());
  }

  $newsId = intval($_GET['news_id'] ?? 0);
  if ($newsId <= 0) json_response(['error' => 'شناسه خبر نامعتبر است.'], 400);

  // مدیر و کارمندان همه نظرات را می‌بینند؛ شهروندان فقط تایید شده‌ها
  $staff = current_staff();
  $admin = is_admin();
  if ($admin || $staff) {
    $stmt = $pdo->prepare("SELECT * FROM news_comments WHERE news_id = ? ORDER BY id DESC");
  } else {
    $stmt = $pdo->prepare("SELECT * FROM news_comments WHERE news_id = ? AND status = 'approved' ORDER BY id ASC");
  }
  $stmt->execute([$newsId]);
  json_response($stmt->fetchAll());
}

if ($method === 'POST') {
  $action = $_GET['action'] ?? 'submit';

  // ------- تایید/رد نظر (مدیر یا کارمند) -------
  if ($action === 'approve' || $action === 'reject') {
    require_staff_role(['admin', 'governor', 'mayor']);
    $id = intval($_GET['id'] ?? 0);
    if ($action === 'reject') {
      $pdo->prepare("DELETE FROM news_comments WHERE id = ?")->execute([$id]);
    } else {
      $pdo->prepare("UPDATE news_comments SET status = 'approved' WHERE id = ?")->execute([$id]);
    }
    json_response(['ok' => true]);
  }

  // ------- پاسخ به نظر (فرماندار / مدیر / شهردار) -------
  if ($action === 'reply') {
    $staff = require_staff_role(['admin', 'governor', 'mayor']);
    $id = intval($_GET['id'] ?? 0);
    $body = get_json_body();
    $reply = clean_str($body['reply'] ?? '', 2000);
    if ($reply === '') json_response(['error' => 'متن پاسخ خالی است.'], 422);
    $pdo->prepare("UPDATE news_comments SET reply = ?, reply_by = ?, reply_date = ?, status = 'approved' WHERE id = ?")
        ->execute([$reply, $staff['name'], clean_str($body['date'] ?? '', 50), $id]);
    json_response(['ok' => true]);
  }

  // ------- ثبت نظر جدید توسط شهروند -------
  $user = current_user();
  $body = get_json_body();
  $newsId = intval($_GET['news_id'] ?? 0);
  if ($newsId <= 0) json_response(['error' => 'شناسه خبر نامعتبر است.'], 400);

  $text = clean_str($body['body'] ?? '', 2000);
  if (strlen($text) < 5) json_response(['error' => 'متن نظر خیلی کوتاه است (حداقل ۵ کاراکتر).'], 422);

  $userName = $user ? $user['name'] : clean_str($body['user_name'] ?? 'ناشناس', 200);
  $userId   = $user ? $user['id'] : null;

  $stmt = $pdo->prepare("INSERT INTO news_comments (news_id, user_id, user_name, body, status, date) VALUES (?, ?, ?, ?, 'pending', ?)");
  $stmt->execute([$newsId, $userId, $userName, $text, clean_str($body['date'] ?? '', 50)]);
  json_response(['ok' => true, 'message' => 'نظر شما ثبت شد و پس از تایید نمایش داده می‌شود.'], 201);
}

if ($method === 'DELETE') {
  require_staff_role(['admin', 'governor', 'mayor']);
  $id = intval($_GET['id'] ?? 0);
  $pdo->prepare("DELETE FROM news_comments WHERE id = ?")->execute([$id]);
  json_response(['ok' => true]);
}

json_response(['error' => 'متد نامعتبر است.'], 405);
