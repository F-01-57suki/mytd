<?php
header('X-FRAME-OPTIONS: SAMEORIGIN');
if($_SERVER['REQUEST_METHOD'] !== 'POST'):
  header('Location: index.php');
  die();
endif;

session_start();
session_regenerate_id(true);
if(isset($_SESSION['username'])):
  header('Location: index.php');
  die();
endif;

$link = mysqli_connect('mysql1.php.xdomain.ne.jp','crimsonscar_root','Ha020714');
if(!$link):
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet">
    <title>MYtodo‐エラー</title>
  </head>
  <body id="error">
    <div id="wrapper">
      <p><?php die("DB接続エラー：" . mysqli_connect()); ?></p>
<?php
endif;

mysqli_select_db($link,'crimsonscar_mytodo');
mysqli_set_charset($link,'UTF-8');

$sql = mysqli_prepare($link,'SELECT `username`,`pass` FROM `user_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_POST['username']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);

if(mysqli_stmt_num_rows($sql) !== 0):
  mysqli_stmt_bind_result($sql,$username,$pass);
  mysqli_stmt_fetch($sql);
  if(password_verify($_POST['pass'],$pass)):
    $_SESSION['username'] = $username;
    $_SESSION['pass'] = htmlspecialchars($_POST['pass'],ENT_QUOTES);
    mysqli_stmt_close($sql);
    mysqli_close($link);
    header('Location: index.php');
    die();
  else:
    mysqli_stmt_close($sql);
    mysqli_close($link);
  ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet">
    <title>MYtodo‐エラー</title>
  </head>
  <body id="error">
    <div id="wrapper">
      <p>パスワードが間違っています。</p>
      <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
  <?php
  endif;
else:
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet">
    <title>MYtodo‐エラー</title>
  </head>
  <body id="error">
    <div id="wrapper">
      <p>該当ユーザーがいません。</p>
      <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
<?php
endif;
?>
    </div>
    <footer>
      <p>copyright &copy; <?php echo date('Y'); ?> Miyashita.</p>
    </footer>
  </body>
</html>