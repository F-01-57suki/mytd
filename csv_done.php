<?php
header('X-FRAME-OPTIONS: SAMEORIGIN');
if($_SERVER['REQUEST_METHOD'] !== 'POST'):
  die("直接アクセス禁止です。");
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
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
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
mysqli_set_charset($link,'utf8');

//ボード名の取得
$b_arr=array();
$sql = mysqli_prepare($link,'SELECT `b_id`,`b_name` FROM `board_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) != 0):
  mysqli_stmt_bind_result($sql,$b_id,$b_name);
  while(mysqli_stmt_fetch($sql)):
    $b_arr[$b_id] = $b_name;
  endwhile;
  mysqli_stmt_close($sql);
else:
  mysqli_stmt_close($sql);
  mysqli_close($link);
  header('Location: create.php');
  die();
endif;

//ラベルの取得
$l_arr=array();
$sql = mysqli_prepare($link,'SELECT `l_id`,`l_name` FROM `label_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) != 0):
  mysqli_stmt_bind_result($sql,$l_id,$l_name);
  while(mysqli_stmt_fetch($sql)):
    $l_arr[$l_id] = $l_name;
  endwhile;
endif;
mysqli_stmt_close($sql);

//全ボード全ラベル
if($_POST['b_value'] == "all" and $_POST['l_value'] == "all"):
  $sql = mysqli_prepare($link,'SELECT `b_id`,`l_id`,`cont` FROM `postit_tbl` WHERE `username`=?');
  mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  if(mysqli_stmt_num_rows($sql) != 0):
    mysqli_stmt_bind_result($sql,$b_id,$l_id,$cont);
  else:
    mysqli_close($link);
    header('Location: create.php');
    die();
  endif;
else:
  //全ボード（各ラベル）
  if($_POST['b_value'] == "all"):
    $sql = mysqli_prepare($link,'SELECT `b_id`,`l_id`,`cont` FROM `postit_tbl` WHERE `username`=? and `l_id`=?');
    mysqli_stmt_bind_param($sql,'ss',$_SESSION['username'],$_POST['l_value']);
    mysqli_stmt_execute($sql);
    mysqli_stmt_store_result($sql);
    if(mysqli_stmt_num_rows($sql) != 0):
      mysqli_stmt_bind_result($sql,$b_id,$l_id,$cont);
    else:
      mysqli_stmt_close($sql);
      mysqli_close($link);
      ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>MYtodo‐エラー</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  </head>
  <body id="error">
    <div id="wrapper">
    <p>データがありません。</p>
    <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
    </div>
  </body>
</html>
      <?php
      die();
    endif;
  else:
    //全ラベル（各ボード）
    if($_POST['l_value'] == "all"):
      $sql = mysqli_prepare($link,'SELECT `b_id`,`l_id`,`cont` FROM `postit_tbl` WHERE `username`=? and `b_id`=?');
      mysqli_stmt_bind_param($sql,'ss',$_SESSION['username'],$_POST['b_value']);
      mysqli_stmt_execute($sql);
      mysqli_stmt_store_result($sql);
      if(mysqli_stmt_num_rows($sql) != 0):
        mysqli_stmt_bind_result($sql,$b_id,$l_id,$cont);
      else:
        mysqli_stmt_close($sql);
        mysqli_close($link);
        ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>MYtodo‐エラー</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  </head>
  <body id="error">
    <div id="wrapper">
    <p>データがありません。</p>
    <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
    </div>
  </body>
</html>
        <?php
        die();
      endif;
    else:
      //全指定
      $sql = mysqli_prepare($link,'SELECT `b_id`,`l_id`,`cont` FROM `postit_tbl` WHERE `username`=? and `b_id`=? and `l_id`=?');
      mysqli_stmt_bind_param($sql,'sss',$_SESSION['username'],$_POST['b_value'],$_POST['l_value']);
      mysqli_stmt_execute($sql);
      mysqli_stmt_store_result($sql);
      if(mysqli_stmt_num_rows($sql) != 0):
        mysqli_stmt_bind_result($sql,$b_id,$l_id,$cont);
      else:
        mysqli_stmt_close($sql);
        mysqli_close($link);
        ?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>MYtodo‐エラー</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  </head>
  <body id="error">
    <div id="wrapper">
    <p>データがありません。</p>
    <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
    </div>
  </body>
</html>
        <?php
        die();
      endif;
    endif;
  endif;
endif;

//データ処理
$str = "ユーザー,ボード,ラベル,内容\r\n";
while($post = mysqli_stmt_fetch($sql)):
  $str .= $_SESSION['username'].",".$b_arr[$b_id].",".$l_arr[$l_id].",".$cont."\r\n";
endwhile;

//csv出力
$fname = $_SESSION['username'].date("_ymd_His").".csv";
header('Content-type:text/csv');
header('Content-Disposition:attachment;filename='.$fname);
if($_POST['mime'] == "utf8"):
  echo $str;
endif;
if($_POST['mime'] == "sjis"):
  echo mb_convert_encoding($str,'SJIS','UTF-8');
endif;
mysqli_stmt_close($sql);
mysqli_close($link);
?>