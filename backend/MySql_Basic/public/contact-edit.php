<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();
$targetUserId = is_admin() ? (int)($_GET['user_id'] ?? current_user_id()) : current_user_id();

global $db;
$contact = $db->where('user_id',$targetUserId)->getOne('user_contact_informations');

$err=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
  checkCsrf();
  $email=trim($_POST['email']??'');
  $phone=trim($_POST['phone']??'');
  $address=trim($_POST['address']??'');
  if(!$email){ $err='Email required'; }
  else {
    try {
      if($contact){
        $db->where('user_id',$targetUserId)->update('user_contact_informations',[
          'email'=>$email,'phone'=>$phone,'address'=>$address
        ]);
      } else {
        // Yeni kayıt oluşturma: yalnızca admin başka kullanıcı için oluşturabilir
        if($targetUserId!==current_user_id() && !is_admin()){ http_response_code(403); exit('Forbidden'); }
        $db->insert('user_contact_informations',[
          'user_id'=>$targetUserId,'email'=>$email,'phone'=>$phone,'address'=>$address
        ]);
      }
      redirect(is_admin()?'/users-list.php':'/profile.php');
    } catch(Throwable $e){ $err=$e->getMessage(); }
  }
}
?>
<!doctype html><meta charset="utf-8">
<h1>Contact Info (User #<?=e((string)$targetUserId)?>)</h1>
<?php if($err):?><p style="color:red"><?=$err?></p><?php endif;?>
<form method="post">
  <input type="hidden" name="csrf" value="<?=e(csrfToken())?>">
  <label>Email <input name="email" value="<?=e($contact['email']??'')?>" required></label><br>
  <label>Phone <input name="phone" value="<?=e($contact['phone']??'')?>"></label><br>
  <label>Address <input name="address" value="<?=e($contact['address']??'')?>"></label><br>
  <button>Save</button>
</form>
<p><a href="<?=is_admin()?'/users-list.php':'/profile.php'?>">Back</a></p>