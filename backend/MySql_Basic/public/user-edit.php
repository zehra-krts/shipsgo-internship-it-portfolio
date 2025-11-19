<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if(!$id){ http_response_code(404); exit('Not found'); }
global $db;
$user = $db->where('id',$id)->getOne('users');
if(!$user){ http_response_code(404); exit('Not found'); }

if(!is_admin() && current_user_id()!==$id){ http_response_code(403); exit('Forbidden'); }

$err=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
  checkCsrf();
  $name=trim($_POST['name']??''); $age=(int)($_POST['age']??0);
  $data=['name'=>$name,'age'=>$age];
  if(is_admin()){ // admin rol değiştirebilir, şifre sıfırlayabilir
    $role=in_array(($_POST['role']??'user'),['admin','user'],true)?$_POST['role']:'user';
    $data['role']=$role;
    if(!empty($_POST['password'])){ $data['password_hash']=password_hash($_POST['password'],PASSWORD_DEFAULT); }
  } else { // user kendi şifresini değiştirebilir
    if(!empty($_POST['password'])){ $data['password_hash']=password_hash($_POST['password'],PASSWORD_DEFAULT); }
  }
  try{
    $db->where('id',$id)->update('users',$data);
    redirect(is_admin()?'/user-list.php':'/profile.php');
  }catch(Throwable $e){ $err=$e->getMessage(); }
}
?>
<!doctype html><meta charset="utf-8">
<h1>Edit User #<?=e((string)$user['id'])?></h1>
<?php if($err):?><p style="color:red"><?=$err?></p><?php endif;?>
<form method="post">
  <input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
  <label>Name <input name="name" value="<?=e($user['name'])?>" required></label><br>
  <label>Age <input name="age" type="number" min="0" max="120" value="<?=e((string)$user['age'])?>"></label><br>
  <?php if(is_admin()): ?>
    <label>Role
      <select name="role">
        <option value="user" <?=$user['role']==='user'?'selected':''?>>user</option>
        <option value="admin" <?=$user['role']==='admin'?'selected':''?>>admin</option>
      </select>
    </label><br>
  <?php endif; ?>
  <label>New Password (optional) <input name="password" type="password"></label><br>
  <button>Save</button>
</form>
<p><a href="<?=is_admin()?'/user-list.php':'/index.php'?>">Back</a></p>