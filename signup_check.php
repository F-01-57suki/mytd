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

$errors = array();
$username = null;
$pass = null;

$sql = mysqli_prepare($link,'SELECT `username` FROM `user_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_POST['username']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) === 0):
  if(preg_match("/^[a-zA-Z0-9@+*._-]{1,20}$/",$_POST['username'])):
    $username = htmlspecialchars($_POST['username'],ENT_QUOTES,'UTF-8');
  else:
    $errors['username'] = "アカウント名を正しく入力してください。";
  endif;
else:
  $errors['username'] = "既に使用されているアカウント名です。";
endif;
mysqli_stmt_close($sql);
mysqli_close($link);

if(preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]{8,16}$/",$_POST['pass'])):
  $pass = htmlspecialchars($_POST['pass'],ENT_QUOTES,'UTF-8');
else:
  $errors['pass'] = "パスワードを正しく入力してください。";
endif;

if(count($errors) !== 0):
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
<?php
  foreach($errors as $value):
    echo "<p>",$value,"</p>";
  endforeach;
?>
    <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
<?php
else:
  $_SESSION['ucheck']=$username;
  $_SESSION['pcheck']=$pass;
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
        <h1>Sign Up</h1>
        <div>
          <table>
            <tr>
              <th>アカウント名：</th><td><?php echo $username; ?></td>
            </tr>
            <tr>
              <th>パスワード：</th><td><?php echo str_repeat("*",mb_strlen($pass,"UTF8")); ?></td>
            </tr>
          </table>
        </div>
        <p class="su_pc">アカウントを作成しますか？</p>
        <div>
          <form action="signup_done.php" method="post" class="su_fc">
            <input type="submit" value="作成">
            <input type="button" value="戻る" onclick="history.go(-1)">
          </form>
        </div>
<?php endif; ?>
        </main>
      </div>
    <footer>
      <p>copyright &copy; <?php echo date('Y'); ?> Miyashita.</p>
    </footer>
    <script>
    </script>
  </body>
</html>