<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  if (isset($_GET['all'])) {
    require_admin();
    $stmt = $pdo->query("SELECT * FROM `market` ORDER BY id DESC");
  } else {
    $stmt = $pdo->query("SELECT * FROM `market` WHERE status = 'approved' ORDER BY id DESC");
  }
  json_response($stmt->fetchAll());
}

if ($method === 'POST') {
  $action = $_GET['action'] ?? '';

  if ($action === 'approve' || $action === 'reject') {
    require_admin();
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) json_response(['error' => 'شناسه نامعتبر است.'], 400);
    if ($action === 'reject') {
      $stmt = $pdo->prepare("DELETE FROM `market` WHERE id = ?");
      $stmt->execute([$id]);
    } else {
      $stmt = $pdo->prepare("UPDATE `market` SET status = 'approved' WHERE id = ?");
      $stmt->execute([$id]);
    }
    json_response(['ok' => true]);
  }

  // ثبت آگهی جدید توسط شهروند - بدون نیاز به ورود مدیر
  $body = get_json_body();
  $stmt = $pdo->prepare("INSERT INTO `market` (title, price, category, phone, description, status, date) VALUES (?, ?, ?, ?, ?, 'pending', ?)");
  $stmt->execute([
    clean_str($body['title'] ?? ''),
    clean_str($body['price'] ?? '', 100),
    clean_str($body['category'] ?? '', 100),
    clean_str($body['phone'] ?? '', 30),
    clean_str($body['description'] ?? ''),
    clean_str($body['date'] ?? '', 50),
  ]);
  $id = $pdo->lastInsertId();
  $row = $pdo->prepare("SELECT * FROM `market` WHERE id = ?");
  $row->execute([$id]);
  json_response($row->fetch(), 201);
}

if ($method === 'DELETE') {
  require_admin();
  $id = intval($_GET['id'] ?? 0);
  if ($id <= 0) json_response(['error' => 'شناسه نامعتبر است.'], 400);
  $stmt = $pdo->prepare("DELETE FROM `market` WHERE id = ?");
  $stmt->execute([$id]);
  json_response(['ok' => true]);
}

json_response(['error' => 'متد نامعتبر است.'], 405);
