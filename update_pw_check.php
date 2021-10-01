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

//正規表現チェック
$newpass = null;
if(preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,16}$/",$_POST['newpass'])):
  $newpass = htmlspecialchars($_POST['newpass'],ENT_QUOTES,'UTF-8');
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
      <p>パスワードを正しく入力してください。</p>
      <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
  <?php
  die();
endif;

//パスワードチェック
$sql = mysqli_prepare($link,'SELECT `pass` FROM `user_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) !== 0):
  mysqli_stmt_bind_result($sql,$pass);
  mysqli_stmt_fetch($sql);
  if($_SESSION['pass']==$_POST['pass'] and password_verify($_POST['pass'],$pass)):
    $_SESSION['newpass']=$newpass;
    $_SESSION['pcheck']=$_POST['pass'];
  ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet">
    <title>MYtodo‐確認</title>
  </head>
  <body id="signup">
    <div id="wrapper">
      <main>
        <h1>Update</h1>
        <div>
          <table>
            <tr>
              <th>新パスワード：</th><td><?php echo str_repeat("*",mb_strlen($newpass,"UTF8")); ?></td>
            </tr>
          </table>
        </div>
        <p class="su_pc">パスワードを変更しますか？<br>（この操作をすると、再度ログインが必要です）</p>
        <div>
          <form action="update_pw_done.php" method="post" class="su_fc">
            <input type="submit" value="変更">
            <input type="button" value="戻る" onclick="history.go(-1)">
          </form>
        </div>
      </main>
<?php
    mysqli_stmt_close($sql);
    mysqli_close($link);
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
  mysqli_close($link);
  die();
endif;
?>
    </div>
    <footer>
      <p>copyright &copy; <?php echo date('Y'); ?> Miyashita.</p>
    </footer>
    <script>
    </script>
  </body>
</html>