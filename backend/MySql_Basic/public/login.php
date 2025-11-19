<?php
require __DIR__ . '/../src/bootstrap.php';

$err = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  checkCsrf();
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  $db = $GLOBALS['db'];
  $user = $db->where('username', $username)->getOne('users');

  if ($user && !empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
    $_SESSION['user'] = [
      'id'   => (int)$user['id'],
      'name' => $user['name'],
      'role' => $user['role'],
    ];
    redirect('/index.php');
  } else {
    $err = 'Invalid credentials';
  }
}
?>
<!doctype html>
<meta charset="utf-8">
<h1>Login</h1>

<?php if ($err): ?>
  <p style="color:red"><?= e($err) ?></p>
<?php endif; ?>

<form method="post">
  <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
  <label>Username <input name="username" required></label><br>
  <label>Password <input name="password" type="password" required></label><br>
  <button>Sign in</button>
</form>