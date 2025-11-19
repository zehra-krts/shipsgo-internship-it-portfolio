<?php
use MysqliDb as DB;

// PHP'yi hostta çalıştırdığın için:
$DB_HOST = '127.0.0.1';
$DB_PORT = (int)($_ENV['DB_PORT'] ?? 3307);

$db = new DB([
  'host'     => $DB_HOST,
  'username' => $_ENV['DB_USERNAME'] ?? 'shipsuser',
  'password' => $_ENV['DB_PASSWORD'] ?? 'StrongLocal!Pass123',
  'db'       => $_ENV['DB_DATABASE'] ?? 'shipsgo_task6',
  'port'     => $DB_PORT,
  'charset'  => 'utf8mb4',
]);

// Global alana yaz: login.php'de NULL olma ihtimali kalmasın
$GLOBALS['db'] = $db;

// (İsteğe bağlı) Hızlı bağlantı testi ve anlamlı hata mesajı
try {
  // Basit bir sorgu deneyelim:
  $db->getValue('users', 'COUNT(*)');  // tablo yoksa da exception atar
} catch (Throwable $e) {
  // Şu satırı açarsan sebebi direkt ekrana düşer:
  // die('DB connect error: ' . $e->getMessage());
}