<?php
header('X-FRAME-OPTIONS: SAMEORIGIN');
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
<?php
endif;
mysqli_select_db($link,'crimsonscar_mytodo');
mysqli_set_charset($link,'UTF-8');

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
mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>MYtodo‐CSVダウンロード</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  </head>
  <body id="user">
    <div id="wrapper">
      <main>
        <h1>Download</h1>
        <div>
          <form action="csv_done.php" method="post">
            <select name="b_value">
              <option value="all">全ボード</option>
              <?php  foreach($b_arr as $key => $value): ?>
              <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
              <?php endforeach; ?>
            </select>
            <select name="l_value">
              <option value="all">全ラベル</option>
              <?php  foreach($l_arr as $key => $value): ?>
              <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
              <?php endforeach; ?>
            </select>
            <select name="mime">
              <option value="utf8">UTF-8</option>
              <option value="sjis">Shift-JIS</option>
            </select>
            <input type="submit" value="出力">
          </form>
        </div>
        <div>
          <form class="su_fc">
            <input type="button" value="戻る" onclick="history.go(-1)">
          </form>
        </div>
      </main>
    </div>
    <footer>
      <p>copyright &copy; <?php echo date('Y'); ?> Miyashita.</p>
    </footer>
    <script>
    </script>
  </body>
</html>