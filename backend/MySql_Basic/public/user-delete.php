<?php
require __DIR__ . '/../src/bootstrap.php';
require_login(); require_admin();
$id=(int)($_GET['id']??0);
if($id){
  global $db;
  $db->where('id',$id)->delete('users'); // FK sayesinde contact da silinir
}
redirect('/user-list.php');