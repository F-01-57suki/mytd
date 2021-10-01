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
    <title>MYtodo‐エラー</title>
    <link href="style.css" rel="stylesheet">
  </head>
  <body id="error">
    <div id="wrapper">
    <p><?php die("DB接続エラー：" . mysqli_connect()); ?></p>
<?php
endif;

mysqli_select_db($link,'crimsonscar_mytodo');
mysqli_set_charset($link,'UTF-8');

//色変更
if(strlen($_POST['b_color'])):
  $sql = mysqli_prepare($link,'UPDATE `board_tbl` SET `b_color`=? WHERE `b_id`=?');
  mysqli_stmt_bind_param($sql,'ss',$_POST['b_color'],$_POST['bid']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_close($sql);
endif;

//ボード名変更
if(strlen($_POST['board_in'])):
  if(preg_match("/^[a-zA-Z0-9ぁ-んァ-ヶー一-龠！]{1,30}$/",$_POST['board_in']) and strlen($_POST['board_in'])<=30):
    $sql = mysqli_prepare($link,'UPDATE `board_tbl` SET `b_name`=? WHERE `b_id`=?');
    mysqli_stmt_bind_param($sql,'ss',$_POST['board_in'],$_POST['bid']);
    mysqli_stmt_execute($sql);
    mysqli_stmt_close($sql);
  else:
    ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>MYtodo‐エラー</title>
  </head>
  <body id="error">
  <div id="wrapper">
    <p>ボード名を正しく入力してください。</p>
    <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
<?php
    die();
    mysqli_close($link);
  endif;
endif;

//ボード削除
if(strlen($_POST['b_del'])):
  $sql = mysqli_prepare($link,"DELETE FROM `board_tbl` WHERE `username`=? and `b_id`=?");
  mysqli_stmt_bind_param($sql,'ss',$_SESSION['username'],$_POST['bid']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  mysqli_stmt_close($sql);
  //ラベルを取得
  $lidarr=array();
  $sql = mysqli_prepare($link,"SELECT `l_id` FROM `postit_tbl` WHERE `username`=? and `b_id`=?");
  mysqli_stmt_bind_param($sql,'ss',$_SESSION['username'],$_POST['bid']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  if(mysqli_stmt_num_rows($sql) !== 0):
    mysqli_stmt_bind_result($sql,$l_id);
    while(mysqli_stmt_fetch($sql)):
      $lidarr[]=$l_id;
    endwhile;
  endif;
  mysqli_stmt_close($sql);
  //付箋の削除
  $sql = mysqli_prepare($link,"DELETE FROM `postit_tbl` WHERE `username`=? and `b_id`=?");
  mysqli_stmt_bind_param($sql,'ss',$_SESSION['username'],$_POST['bid']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  mysqli_stmt_close($sql);
  //ラベルチェック
  foreach($lidarr as $value):
    $sql = mysqli_prepare($link,"SELECT `l_id` FROM `postit_tbl` WHERE `username`=? and `l_id`=?");
    mysqli_stmt_bind_param($sql,'ss',$_SESSION['username'],$value);
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    if(mysqli_stmt_num_rows($sql) === 0):
      mysqli_stmt_close($sql);
      $sql = mysqli_prepare($link,"DELETE FROM `label_tbl` WHERE `l_id`=?");
      mysqli_stmt_bind_param($sql,'s',$value);
      mysqli_stmt_execute($sql);
      mysqli_stmt_store_result($sql);
      mysqli_stmt_close($sql);
    else:
      mysqli_stmt_close($sql);
    endif;
  endforeach;
endif;
mysqli_close($link);
header('Location: login.php');
die();