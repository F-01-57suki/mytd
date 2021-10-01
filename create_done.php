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

$errors = array();

if(strlen($_POST['board_se']) and strlen($_POST['board_in'])):
  $errors['board'] = "ボードは複数同時に選択できません。";
endif;
if(strlen($_POST['label_se']) and strlen($_POST['label_in'])):
  $errors['label'] = "ラベルは複数同時に選択できません。";
endif;

if(!strlen($_POST['board_se']) and !strlen($_POST['board_in'])):
  $errors['board'] = "ボードを選択して下さい。";
endif;
if(!strlen($_POST['label_se']) and !strlen($_POST['label_in'])):
  $errors['label'] = "ラベルを選択して下さい";
endif;

//ボードの新規追加
if(strlen($_POST['board_in'])):
  $sql = mysqli_prepare($link,'SELECT `username` FROM `board_tbl` WHERE `username`=?');
  mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  if(mysqli_stmt_num_rows($sql) >= 4):
    $errors['board_in'] = "ボードが作成上限（４つ）を超えています。";
  else:
    if(preg_match("/^[a-zA-Z0-9ぁ-んァ-ヶー一-龠！]{1,30}$/",$_POST['board_in']) and strlen($_POST['board_in'])<=30):
      $board_in = htmlspecialchars($_POST['board_in'],ENT_QUOTES,'UTF-8');
    else:
      $errors['board_in'] = "新規ボード名を正しく入力して下さい。";
    endif;
  endif;
  mysqli_stmt_close($sql);
endif;

//ラベルの新規追加
if(strlen($_POST['label_in'])):
  $sql = mysqli_prepare($link,'SELECT `username` FROM `label_tbl` WHERE `username`=?');
  mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  if(mysqli_stmt_num_rows($sql) >= 9):
    $errors['label_in'] = "ラベルが作成上限（9つ）を超えています。";
  else:
    if(preg_match("/^[a-zA-Z0-9ぁ-んァ-ヶー一-龠！]{1,39}$/",$_POST['label_in']) and strlen($_POST['label_in'])<=39):
      $label_in = htmlspecialchars($_POST['label_in'],ENT_QUOTES,'UTF-8');
    else:
      $errors['label_in'] = "新規ラベル名を正しく入力してください。";
    endif;
  endif;
  mysqli_stmt_close($sql);
endif;

//ボードIDをひっぱってくる
//b_idがboard_seのvalueに入ってるので、そのまま使う
if(strlen($_POST['board_se'])):
  $sql = mysqli_prepare($link,'SELECT `b_id` FROM `board_tbl` WHERE `b_id`=?');
  mysqli_stmt_bind_param($sql,'s',$_POST['board_se']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  if(mysqli_stmt_num_rows($sql) != 0):
    mysqli_stmt_bind_result($sql,$b_id);
    mysqli_stmt_fetch($sql);
  else:
    $errors['board_se'] = "選択したボードが存在しません。";
  endif;
  mysqli_stmt_close($sql);
endif;
//ラベルIDをひっぱってくる
if(strlen($_POST['label_se'])):
  $sql = mysqli_prepare($link,'SELECT `l_id` FROM `label_tbl` WHERE `l_id`=?');
  mysqli_stmt_bind_param($sql,'s',$_POST['label_se']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  if(mysqli_stmt_num_rows($sql) != 0):
    mysqli_stmt_bind_result($sql,$l_id);
    mysqli_stmt_fetch($sql);
  else:
    $errors['label_se'] = "選択したラベルが存在しません。";
  endif;
  mysqli_stmt_close($sql);
endif;

//内容チェック
if(strlen($_POST['cont'])>=1 and strlen($_POST['cont'])<=300):
  $cont = htmlspecialchars($_POST['cont'],ENT_QUOTES,'UTF-8');
else:
  $errors['cont'] = "内容を正しく入力して下さい。";
endif;

//エラー確認～実行
if(count($errors) !== 0):
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
  <?php
    foreach($errors as $value):
      echo "<p>",$value,"</p>";
    endforeach;
  ?>
      <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
  <?php
  die();
  mysqli_close($link);
else:
  //エラーなしで処理
  if(isset($board_in)):
    $bc="bc_gray";
    $sql = mysqli_prepare($link,'INSERT INTO `board_tbl` (`username`,`b_name`,`b_color`) VALUES (?,?,?)');
    mysqli_stmt_bind_param($sql,'sss',$_SESSION['username'],$board_in,$bc);
    mysqli_stmt_execute($sql);
    mysqli_stmt_close($sql);
    //$b_idに追加したid入れる
    $sql = mysqli_prepare($link,'SELECT `b_id` FROM `board_tbl` ORDER BY b_id DESC LIMIT 1');
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    mysqli_stmt_bind_result($sql,$b_id);
    mysqli_stmt_fetch($sql);
    mysqli_stmt_close($sql);
  endif;

  if(isset($label_in)):
    $sql = mysqli_prepare($link,'INSERT INTO `label_tbl` (`username`,`l_name`) VALUES (?,?)');
    mysqli_stmt_bind_param($sql,'ss',$_SESSION['username'],$label_in);
    mysqli_stmt_execute($sql);
    mysqli_stmt_close($sql);
    //$l_idに追加したid入れる
    $sql = mysqli_prepare($link,'SELECT `l_id` FROM `label_tbl` ORDER BY l_id DESC LIMIT 1');
    //mysqli_stmt_bind_param($sql,'s',$label_in);
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    mysqli_stmt_bind_result($sql,$l_id);
    mysqli_stmt_fetch($sql);
    mysqli_stmt_close($sql);
  endif;

  //付箋をDBに追加
  if(isset($cont) and isset($b_id) and isset($l_id)):
    $sql = mysqli_prepare($link,'INSERT INTO `postit_tbl` (`username`,`cont`,`l_id`,`b_id`) VALUES (?,?,?,?)');
    mysqli_stmt_bind_param($sql,'ssss',$_SESSION['username'],$cont,$l_id,$b_id);
    mysqli_stmt_execute($sql);
    mysqli_stmt_close($sql);
    mysqli_close($link);
    header('Location: index.php');
    die();
  else:
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
    <p>エラーが発生しました。</p>
    <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
  <?php
    mysqli_close($link);
    die();
  endif;
endif;
mysqli_close($link);
?>