<?php
header('X-FRAME-OPTIONS: SAMEORIGIN');
if($_SERVER['REQUEST_METHOD'] !== 'GET'):
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
    </div>
  </body>
</html>
<?php
endif;

mysqli_select_db($link,'crimsonscar_mytodo');
mysqli_set_charset($link,'UTF-8');

$sql = mysqli_prepare($link,"SELECT `b_id`,`l_id` FROM `postit_tbl` WHERE `username`=? and `p_id`=?");
mysqli_stmt_bind_param($sql,'ss',$_SESSION['username'],$_GET['pid']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) !== 0):
  mysqli_stmt_bind_result($sql,$b_id,$l_id);
  mysqli_stmt_fetch($sql);
  mysqli_stmt_close($sql);
  //付箋削除
  $sql = mysqli_prepare($link,"DELETE FROM `postit_tbl` WHERE `username`=? and `p_id`=?");
  mysqli_stmt_bind_param($sql,'ss',$_SESSION['username'],$_GET['pid']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  mysqli_stmt_close($sql);
  //ラベルチェック
  $sql = mysqli_prepare($link,"SELECT `l_id` FROM `postit_tbl` WHERE `l_id`=?");
  mysqli_stmt_bind_param($sql,'s',$l_id);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  if(mysqli_stmt_num_rows($sql) === 0):
    mysqli_stmt_close($sql);
    $sql = mysqli_prepare($link,"DELETE FROM `label_tbl` WHERE `l_id`=?");
    mysqli_stmt_bind_param($sql,'s',$l_id);
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    mysqli_stmt_close($sql);
  else:
    mysqli_stmt_close($sql);
  endif;
  //ボードチェック
  $sql = mysqli_prepare($link,"SELECT `b_id` FROM `postit_tbl` WHERE `b_id`=?");
  mysqli_stmt_bind_param($sql,'s',$b_id);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  if(mysqli_stmt_num_rows($sql) === 0):
    mysqli_stmt_close($sql);
    $sql = mysqli_prepare($link,"DELETE FROM `board_tbl` WHERE `b_id`=?");
    mysqli_stmt_bind_param($sql,'s',$b_id);
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    mysqli_stmt_close($sql);
  else:
    mysqli_stmt_close($sql);
  endif;
  mysqli_close($link);
  header('Location: index.php');
  die();
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
        <p>既に削除済みです。</p>
        <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
      </div>
    </body>
  </html>
<?php
endif;
mysqli_close($link);
?>