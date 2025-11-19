<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();
require_admin();

$db = $GLOBALS['db'];
$users = $db->orderBy('id', 'asc')->get('users');
?>
<!doctype html>
<meta charset="utf-8">
<title>Users List</title>
<h1>Users</h1>
<p><a href="/user-create.php">+ New User</a> | <a href="/index.php">Back</a></p>
<table border="1" cellpadding="6">
  <tr>
    <th>ID</th><th>Name</th><th>Age</th><th>Username</th><th>Role</th><th>Actions</th>
  </tr>
  <?php foreach ($users as $u): ?>
    <tr>
      <td><?= e((string)$u['id']) ?></td>
      <td><?= e($u['name']) ?></td>
      <td><?= e((string)$u['age']) ?></td>
      <td><?= e($u['username']) ?></td>
      <td><?= e($u['role']) ?></td>
      <td>
        <a href="/user-edit.php?id=<?= $u['id'] ?>">Edit</a> |
        <a href="/user-delete.php?id=<?= $u['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>