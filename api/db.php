<?php
require_once __DIR__ . '/config.php';

function db() {
  static $pdo = null;
  if ($pdo === null) {
    try {
      $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
      $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      ]);
    } catch (PDOException $e) {
      http_response_code(500);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['error' => 'اتصال به پایگاه داده برقرار نشد. تنظیمات api/config.php را بررسی کنید.']);
      exit;
    }
  }
  return $pdo;
}
