<?php
require __DIR__ . '/../src/bootstrap.php';

// Oturumu temizle
$_SESSION = [];
if (ini_get('session.use_cookies')) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

// Redirect dene
if (!headers_sent()) {
  header('Location: /login.php', true, 302);
  exit;
}

// Headers zaten gönderildiyse (BOM/boşluk vb.), güvenli fallback:
?>
<!doctype html>
<meta charset="utf-8">
<p>Logged out. <a href="/login.php">Go to Login</a></p>
<script>location.href="/login.php";</script>