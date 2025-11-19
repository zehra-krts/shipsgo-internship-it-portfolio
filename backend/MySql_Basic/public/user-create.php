<?php
require __DIR__ . '/../src/bootstrap.php';
require_login(); require_admin();
$err = null;
if ($_SERVER['REQUEST_METHOD']==='POST'){
  checkCsrf();
  $name=trim($_POST['name']??''); $age=(int)($_POST['age']??0);
  $username=trim($_POST['username']??''); $password=$_POST['password']??'';
  $role=in_array(($_POST['role']??'user'),['admin','user'],true)?$_POST['role']:'user';
  if($name && $username && $password){
    global $db;
    try{
      $id=$db->insert('users',[
        'name'=>$name,'age'=>$age,'username'=>$username,
        'password_hash'=>password_hash($password,PASSWORD_DEFAULT),
        'role'=>$role
      ]);
      if($id){ redirect('/user-list.php'); } else { $err='Insert failed'; }
    }catch(Throwable $e){ $err=$e->getMessage(); }
  } else { $err='Name, username, password required'; }
}
?>
<!doctype html><meta charset="utf-8">
<h1>New User</h1>
<?php if($err):?><p style="color:red"><?=$err?></p><?php endif;?>
<form method="post">
  <input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
  <label>Name <input name="name" required></label><br>
  <label>Age <input name="age" type="number" min="0" max="120"></label><br>
  <label>Username <input name="username" required></label><br>
  <label>Password <input name="password" type="password" required></label><br>
  <label>Role
    <select name="role"><option value="user">user</option><option value="admin">admin</option></select>
  </label><br>
  <button>Create</button>
</form>