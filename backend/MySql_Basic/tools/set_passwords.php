<?php
// Dotenv yükle
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// MysqliDb oluştur (bootstrap'a güvenmeden)
use MysqliDb as DB;

$host = '127.0.0.1';                         // PHP host'ta çalışıyorsan
$port = (int)($_ENV['DB_PORT'] ?? 3307);     // .env portun
$user = $_ENV['DB_USERNAME'];
$pass = $_ENV['DB_PASSWORD'];
$dbn  = $_ENV['DB_DATABASE'];

$db = new DB([
  'host'     => $host,
  'username' => $user,
  'password' => $pass,
  'db'       => $dbn,
  'port'     => $port,
  'charset'  => 'utf8mb4',
]);

if (!$db) { die("DB init failed\n"); }

// basit bağlantı testi
try {
  $db->getValue('users', 'COUNT(*)');
} catch (Throwable $e) {
  die("DB error: " . $e->getMessage() . "\n");
}

// şifreleri güncelle
$db->where('username','admin')->update('users', [
  'password_hash' => password_hash('admin123', PASSWORD_DEFAULT)
]);
$db->where('username','zehra')->update('users', [
  'password_hash' => password_hash('zehra123', PASSWORD_DEFAULT)
]);

echo "Passwords updated!\n";