<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

function poll_with_results($pdo, $poll, $userId) {
  $options = json_decode($poll['options'], true);
  if (!is_array($options)) $options = [];
  $counts = array_fill(0, count($options), 0);

  $stmt = $pdo->prepare("SELECT option_index, COUNT(*) AS c FROM poll_votes WHERE poll_id = ? GROUP BY option_index");
  $stmt->execute([$poll['id']]);
  foreach ($stmt->fetchAll() as $row) {
    $idx = intval($row['option_index']);
    if (isset($counts[$idx])) $counts[$idx] = intval($row['c']);
  }
  $total = array_sum($counts);

  $myVote = null;
  if ($userId) {
    $v = $pdo->prepare("SELECT option_index FROM poll_votes WHERE poll_id = ? AND user_id = ?");
    $v->execute([$poll['id'], $userId]);
    $found = $v->fetch();
    if ($found) $myVote = intval($found['option_index']);
  }

  return [
    'id' => intval($poll['id']),
    'question' => $poll['question'],
    'options' => $options,
    'counts' => $counts,
    'total' => $total,
    'active' => !!intval($poll['active']),
    'date' => $poll['date'],
    'myVote' => $myVote,
  ];
}

if ($method === 'GET') {
  $user = current_user();
  $userId = $user ? $user['id'] : null;

  if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
    $stmt->execute([intval($_GET['id'])]);
    $poll = $stmt->fetch();
    if (!$poll) json_response(['error' => 'نظرسنجی یافت نشد.'], 404);
    json_response(poll_with_results($pdo, $poll, $userId));
  }

  $stmt = $pdo->query("SELECT * FROM polls ORDER BY id DESC");
  $result = [];
  foreach ($stmt->fetchAll() as $poll) {
    $result[] = poll_with_results($pdo, $poll, $userId);
  }
  json_response($result);
}

if ($method === 'POST') {
  $action = $_GET['action'] ?? '';

  if ($action === 'vote') {
    $user = require_user();
    $pollId = intval($_GET['id'] ?? 0);
    $body = get_json_body();
    $optionIndex = intval($body['option_index'] ?? -1);

    $stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
    $stmt->execute([$pollId]);
    $poll = $stmt->fetch();
    if (!$poll || !intval($poll['active'])) {
      json_response(['error' => 'این نظرسنجی فعال نیست.'], 400);
    }
    $options = json_decode($poll['options'], true);
    if ($optionIndex < 0 || $optionIndex >= count($options)) {
      json_response(['error' => 'گزینه نامعتبر است.'], 422);
    }

    try {
      $ins = $pdo->prepare("INSERT INTO poll_votes (poll_id, user_id, option_index) VALUES (?, ?, ?)");
      $ins->execute([$pollId, $user['id'], $optionIndex]);
    } catch (PDOException $e) {
      json_response(['error' => 'شما قبلاً در این نظرسنجی رای داده‌اید.'], 409);
    }

    json_response(poll_with_results($pdo, $poll, $user['id']));
  }

  if ($action === 'toggle') {
    require_admin();
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("UPDATE polls SET active = IF(active = 1, 0, 1) WHERE id = ?");
    $stmt->execute([$id]);
    json_response(['ok' => true]);
  }

  // ساخت نظرسنجی جدید - فقط مدیر
  require_admin();
  $body = get_json_body();
  $question = clean_str($body['question'] ?? '', 500);
  $options = isset($body['options']) && is_array($body['options']) ? array_values(array_filter(array_map('trim', $body['options']))) : [];

  if ($question === '' || count($options) < 2) {
    json_response(['error' => 'سوال و حداقل دو گزینه را وارد کنید.'], 422);
  }

  $stmt = $pdo->prepare("INSERT INTO polls (question, options, active, date) VALUES (?, ?, 1, ?)");
  $stmt->execute([$question, json_encode($options, JSON_UNESCAPED_UNICODE), clean_str($body['date'] ?? '', 50)]);
  $id = $pdo->lastInsertId();

  $stmt = $pdo->prepare("SELECT * FROM polls WHERE id = ?");
  $stmt->execute([$id]);
  json_response(poll_with_results($pdo, $stmt->fetch(), null), 201);
}

if ($method === 'DELETE') {
  require_admin();
  $id = intval($_GET['id'] ?? 0);
  if ($id <= 0) json_response(['error' => 'شناسه نامعتبر است.'], 400);
  $pdo->prepare("DELETE FROM poll_votes WHERE poll_id = ?")->execute([$id]);
  $pdo->prepare("DELETE FROM polls WHERE id = ?")->execute([$id]);
  json_response(['ok' => true]);
}

json_response(['error' => 'متد نامعتبر است.'], 405);
