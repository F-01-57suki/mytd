<?php
session_start();
if(!isset($_SESSION['username'])):
  die("セッションがありません。");
endif;

$_SESSION=array();
  if(isset($_COOKIE[session_name()])):
setcookie(session_name(),"",time()-1000);
endif;
session_destroy();
header('Location: login.php');
die();
?>