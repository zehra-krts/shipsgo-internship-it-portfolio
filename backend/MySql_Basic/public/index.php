<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();

// Güvenli çıktı helper'ı yoksa fallback:
if (!function_exists('e')) {
  function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
}

$user = $_SESSION['user'] ?? null;
?>
<!doctype html>
<meta charset="utf-8">
<title>Dashboard</title>
<h1>Dashboard</h1>

<?php if ($user): ?>
  <p>Welcome, <b><?= e($user['name']) ?></b> (role: <?= e($user['role']) ?>)</p>
  <p><a href="/logout.php">Logout</a></p>

  <?php if (($user['role'] ?? '') === 'admin'): ?>
    <p><a href="/user-list.php">Manage Users</a></p>
  <?php else: ?>
    <p><a href="/profile.php">My Profile</a> | <a href="/contact-edit.php">My Contact</a></p>
  <?php endif; ?>

<?php else: ?>
  <p>Session not found. <a href="/login.php">Login</a></p>
<?php endif; ?>

<?php if (isset($_GET['debug'])): ?>
  <hr>
  <pre><?php var_export($_SESSION); ?></pre>
<?php endif; ?>