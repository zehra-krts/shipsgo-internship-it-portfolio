<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();
$id = current_user_id();
global $db;
$user = $db->where('id',$id)->getOne('users');
$contact = $db->where('user_id',$id)->getOne('user_contact_informations');
?>
<!doctype html><meta charset="utf-8">
<h1>My Profile</h1>
<p><b>Name:</b> <?=e($user['name'])?> | <b>Age:</b> <?=e((string)$user['age'])?> | <b>Username:</b> <?=e($user['username'])?></p>
<?php if($contact): ?>
<p><b>Email:</b> <?=e($contact['email'])?> | <b>Phone:</b> <?=e($contact['phone'])?> | <b>Address:</b> <?=e($contact['address'])?></p>
<?php else: ?>
<p>No contact set</p>
<?php endif; ?>
<p><a href="/user-edit.php?id=<?=$id?>">Edit Profile</a> | <a href="/contact-edit.php">Edit Contact</a> | <a href="/index.php">Back</a></p>