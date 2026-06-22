<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  // فهرست گزارش‌ها فقط برای مدیر سامانه قابل مشاهده است
  require_admin();
  $stmt = $pdo->query("SELECT * FROM `crisis_reports` ORDER BY id DESC");
  json_response($stmt->fetchAll());
}

if ($method === 'POST') {
  $action = $_GET['action'] ?? '';

  if ($action === 'toggle') {
    require_admin();
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) json_response(['error' => 'شناسه نامعتبر است.'], 400);
    $stmt = $pdo->prepare("UPDATE `crisis_reports` SET status = IF(status = 'بررسی شد', 'در انتظار بررسی', 'بررسی شد') WHERE id = ?");
    $stmt->execute([$id]);
    json_response(['ok' => true]);
  }

  // ثبت گزارش جدید توسط شهروند - بدون نیاز به ورود مدیر
  $body = get_json_body();
  $stmt = $pdo->prepare("INSERT INTO `crisis_reports` (type, location, description, contact, status, date) VALUES (?, ?, ?, ?, 'در انتظار بررسی', ?)");
  $stmt->execute([
    clean_str($body['type'] ?? '', 200),
    clean_str($body['location'] ?? '', 300),
    clean_str($body['description'] ?? ''),
    clean_str($body['contact'] ?? '', 50),
    clean_str($body['date'] ?? '', 50),
  ]);
  json_response(['ok' => true], 201);
}

if ($method === 'DELETE') {
  require_admin();
  $id = intval($_GET['id'] ?? 0);
  if ($id <= 0) json_response(['error' => 'شناسه نامعتبر است.'], 400);
  $stmt = $pdo->prepare("DELETE FROM `crisis_reports` WHERE id = ?");
  $stmt->execute([$id]);
  json_response(['ok' => true]);
}

json_response(['error' => 'متد نامعتبر است.'], 405);
