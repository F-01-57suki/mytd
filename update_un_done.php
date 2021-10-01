<?php
header('X-FRAME-OPTIONS: SAMEORIGIN');
if($_SERVER['REQUEST_METHOD'] !== 'POST'):
  header('Location: index.php');
  die();
endif;

session_start();
session_regenerate_id(true);
if(!isset($_SESSION['username'])):
  header('Location: login.php');
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

//未使用かチェック
$username = null;
$sql = mysqli_prepare($link,'SELECT `username` FROM `user_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_SESSION['newusername']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) === 0):
  if(preg_match("/^[a-zA-Z0-9@+*._-]{1,20}$/",$_SESSION['newusername'])):
    $newusername = htmlspecialchars($_SESSION['newusername'],ENT_QUOTES,'UTF-8');
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
      <p>アカウント名を正しく入力してください。</p>
      <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
  <?php
    die();
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
    <p>既に使用されているアカウント名です。</p>
    <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
  <?php
  die();
endif;
mysqli_stmt_close($sql);

//パスワードチェック
$sql = mysqli_prepare($link,'SELECT `pass` FROM `user_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) !== 0):
  mysqli_stmt_bind_result($sql,$pass);
  mysqli_stmt_fetch($sql);
  if($_SESSION['pass']==$_SESSION['pcheck'] and password_verify($_SESSION['pcheck'],$pass)):
    mysqli_stmt_close($sql);
    //postit_tbl
    $sql = mysqli_prepare($link,"UPDATE `postit_tbl` SET `username`=? WHERE `username`=?");
    mysqli_stmt_bind_param($sql,'ss',$_SESSION['newusername'],$_SESSION['username']);
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    mysqli_stmt_close($sql);
    //label_tbl
    $sql = mysqli_prepare($link,"UPDATE `label_tbl` SET `username`=? WHERE `username`=?");
    mysqli_stmt_bind_param($sql,'ss',$_SESSION['newusername'],$_SESSION['username']);
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    mysqli_stmt_close($sql);
    //board_tbl
    $sql = mysqli_prepare($link,"UPDATE `board_tbl` SET `username`=? WHERE `username`=?");
    mysqli_stmt_bind_param($sql,'ss',$_SESSION['newusername'],$_SESSION['username']);
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    mysqli_stmt_close($sql);
    //user_tbl
    $sql = mysqli_prepare($link,"UPDATE `user_tbl` SET `username`=? WHERE `username`=?");
    mysqli_stmt_bind_param($sql,'ss',$_SESSION['newusername'],$_SESSION['username']);
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    mysqli_stmt_close($sql);
    //ログアウト処理
    $_SESSION=array();
    if(isset($_COOKIE[session_name()])):
      setcookie(session_name(),"",time()-1000);
    endif;
    session_destroy();
    mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet">
    <title>MYtodo‐変更完了</title>
  </head>
  <body id="signup">
    <div id="wrapper">
      <main>
        <h1>Update</h1>
        <p class="su_pc"><?php echo $newusername; ?><br>変更しました。<br><a href="login.php">ログイン画面へ</a></p>
      </main>
<?php
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
    die();
  endif;
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
      <p>該当ユーザーがいません。</p>
      <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
<?php
  die();
endif;
?>
    </div>
    <footer>
      <p>copyright &copy; <?php echo date('Y'); ?> Miyashita.</p>
    </footer>
  </body>
</html>