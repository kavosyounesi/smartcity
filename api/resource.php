<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';

// جدول‌های مجاز و ستون‌های قابل‌ویرایش هر کدام (برای جلوگیری از درج ستون‌های غیرمجاز)
// نوع هر ستون: str | int | bool
$TABLES = [
  'news'           => ['title' => 'str', 'category' => 'str', 'excerpt' => 'str', 'date' => 'str'],
  'government'     => ['title' => 'str', 'body' => 'str', 'date' => 'str'],
  'municipality'   => ['title' => 'str', 'body' => 'str', 'date' => 'str'],
  'tourism'        => ['name' => 'str', 'category' => 'str', 'description' => 'str', 'date' => 'str'],
  'events'         => ['title' => 'str', 'date' => 'str', 'location' => 'str', 'description' => 'str'],
  'crisis_alerts'  => ['title' => 'str', 'level' => 'str', 'body' => 'str', 'active' => 'bool', 'date' => 'str'],
  'projects'       => ['title' => 'str', 'description' => 'str', 'progress' => 'int', 'status' => 'str', 'location' => 'str', 'requirements' => 'str', 'obstacles' => 'str', 'date' => 'str'],
];

$table = $_GET['table'] ?? '';
if (!isset($TABLES[$table])) {
  json_response(['error' => 'جدول نامعتبر است.'], 400);
}
$columns = array_keys($TABLES[$table]);
$pdo = db();
$method = $_SERVER['REQUEST_METHOD'];

function cast_row($row, $types) {
  if (!$row) return $row;
  foreach ($types as $col => $type) {
    if (!array_key_exists($col, $row)) continue;
    if ($type === 'bool') $row[$col] = !!intval($row[$col]);
    elseif ($type === 'int') $row[$col] = intval($row[$col]);
  }
  return $row;
}

if ($method === 'GET') {
  $stmt = $pdo->query("SELECT * FROM `$table` ORDER BY id DESC");
  $rows = array_map(function ($r) use ($TABLES, $table) { return cast_row($r, $TABLES[$table]); }, $stmt->fetchAll());
  json_response($rows);
}

if ($method === 'POST') {
  require_admin();
  $body = get_json_body();
  $insertCols = [];
  $placeholders = [];
  $values = [];
  foreach ($columns as $col) {
    $type = $TABLES[$table][$col];
    $insertCols[] = "`$col`";
    $placeholders[] = '?';
    if ($type === 'bool') {
      $values[] = !empty($body[$col]) ? 1 : 0;
    } elseif ($type === 'int') {
      $n = intval($body[$col] ?? 0);
      $values[] = max(0, min(100, $n));
    } else {
      $values[] = clean_str($body[$col] ?? '');
    }
  }
  $sql = "INSERT INTO `$table` (" . implode(',', $insertCols) . ") VALUES (" . implode(',', $placeholders) . ")";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($values);
  $id = $pdo->lastInsertId();
  $row = $pdo->prepare("SELECT * FROM `$table` WHERE id = ?");
  $row->execute([$id]);
  json_response(cast_row($row->fetch(), $TABLES[$table]), 201);
}

if ($method === 'DELETE') {
  require_admin();
  $id = intval($_GET['id'] ?? 0);
  if ($id <= 0) json_response(['error' => 'شناسه نامعتبر است.'], 400);
  $stmt = $pdo->prepare("DELETE FROM `$table` WHERE id = ?");
  $stmt->execute([$id]);
  json_response(['ok' => true]);
}

json_response(['error' => 'متد نامعتبر است.'], 405);
