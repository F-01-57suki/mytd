<?php
header('X-FRAME-OPTIONS: SAMEORIGIN');
if($_SERVER['REQUEST_METHOD']!=='GET'):
  die("直接アクセス禁止です");
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

//付箋の取得
$sql = mysqli_prepare($link,'SELECT `l_id`,`b_id`,`cont` FROM `postit_tbl` WHERE `p_id`=?');
mysqli_stmt_bind_param($sql,'s',$_GET['pid']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) !== 0):
  mysqli_stmt_bind_result($sql,$l_idnow,$b_idnow,$contnow);
  mysqli_stmt_fetch($sql);
  mysqli_stmt_close($sql);
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
<?php
endif;
//ラベルの取得
$sql = mysqli_prepare($link,'SELECT `l_name` FROM `label_tbl` WHERE `l_id`=?');
mysqli_stmt_bind_param($sql,'s',$l_idnow);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) !== 0):
  mysqli_stmt_bind_result($sql,$l_namenow);
  mysqli_stmt_fetch($sql);
  mysqli_stmt_close($sql);
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
      <p>ラベルの取得に失敗しました。</p>
      <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
    </div>
<?php
endif;
//ボードの取得
$sql = mysqli_prepare($link,'SELECT `b_name` FROM `board_tbl` WHERE `b_id`=?');
mysqli_stmt_bind_param($sql,'s',$b_idnow);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) !== 0):
  mysqli_stmt_bind_result($sql,$b_namenow);
  mysqli_stmt_fetch($sql);
  mysqli_stmt_close($sql);
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
      <p>ボードの取得に失敗しました。</p>
      <p><input type="button" value="戻る" onclick='history.go(-1)'></p>
    </div>
<?php
endif;
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet">
    <title>MYtodo‐編集</title>
  </head>
  <body id="create">
    <div id="wrapper">
      <main>
        <h1>edit</h1>
        <div>
          <form action="p_edit_done.php" method="post">
            <div id="board_select">
              <p>
                ボード<br>
                設定中：<?php echo $b_namenow; ?><br>
                <select name="board_se" class="su_inpt">
                  <option value="">変更したいボード</option>
<?php
$sql = mysqli_prepare($link,'SELECT `b_id`,`b_name` FROM `board_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) === 0):
  $bflag = 0;
else:
  mysqli_stmt_bind_result($sql,$b_id,$b_name);
  while(mysqli_stmt_fetch($sql)):
  ?>
  <option value="<?php echo $b_id; ?>"><?php echo $b_name; ?></option>
  <?php
  endwhile;
  $bflag = 1;
endif;
mysqli_stmt_close($sql);
?>
                </select>
              </p>
            </div>
            <div id="board_input">
              <p>
                ボード<br>
                設定中：<?php echo $b_namenow; ?><br>
                <input type="text" name="board_in" class="su_inpt"><br>
                ※全角10文字以内。
              </p>
            </div>
              <p id="bin_swtch">新規追加はこちら</p>

            <div id="label_select">
              <p>
                ラベル<br>
                設定中：<?php echo $l_namenow; ?><br>
                <select name="label_se" class="su_inpt">
                <option value="">変更したいラベル</option>
<?php
$sql = mysqli_prepare($link,'SELECT `l_id`,`l_name` FROM `label_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) === 0):
  $lflag = 0;
else:
  mysqli_stmt_bind_result($sql,$l_id,$l_name);
  while(mysqli_stmt_fetch($sql)):
  ?>
  <option value="<?php echo $l_id; ?>"><?php echo $l_name; ?></option>
  <?php
  endwhile;
  $lflag = 1;
endif;
mysqli_stmt_close($sql);
mysqli_close($link);
?>
                </select>
              </p>
            </div>
            <div id="label_input">
              <p>
                ラベル<br>
                設定中：<?php echo $l_namenow; ?><br>
                <input type="text" name="label_in" class="su_inpt"><br>
                ※全角13文字以内。<br>
              </p>
            </div>
            <p id="lin_swtch">新規追加はこちら</p>

            <p>変更後の内容<br>
            <textarea name="cont" cols="30" rows="8"><?php echo $contnow; ?></textarea><br>
            ※全角100文字以内。</p>

            <p class="su_fc">
              <input type="hidden" name="p_idnow" value="<?php echo $_GET['pid']; ?>">
              <input type="hidden" name="l_idnow" value="<?php echo $l_idnow; ?>">
              <input type="hidden" name="b_idnow" value="<?php echo $b_idnow; ?>">
              <input type="button" value="戻る" onclick="history.go(-1)">
              <input type="submit" value="送信">
            </p>
          </form>
        </div>
      </main>
    </div>
    <footer>
      <p>copyright &copy; <?php echo date('Y'); ?> Miyashita.</p>
    </footer>
    <script>
      const board_select=document.getElementById("board_select");
      const board_input=document.getElementById("board_input");
      const label_select=document.getElementById("label_select");
      const label_input=document.getElementById("label_input");

      const bin_swtch=document.getElementById("bin_swtch");
      const lin_swtch=document.getElementById("lin_swtch");

      function bsi(){
        if(bflag==0){
          bflag=1;
          bin_swtch.innerHTML="既存から選ぶ";
          board_input.style.display="block";
          board_select.style.display="none";
        }
        else{
          bflag=0;
          bin_swtch.innerHTML="新規追加はこちら";
          board_input.style.display="none";
          board_select.style.display="block";
        }
      }

      function lsi(){
        if(lflag==0){
          lflag=1;
          lin_swtch.innerHTML="既存から選ぶ";
          label_input.style.display="block";
          label_select.style.display="none";
        }
        else{
          lflag=0;
          lin_swtch.innerHTML="新規追加はこちら";
          label_input.style.display="none";
          label_select.style.display="block";
        }
      }

      let bflag = "<?php echo $bflag; ?>";//PHPから受け取る
      bin_swtch.onclick=bsi;
      let lflag = "<?php echo $lflag; ?>";//PHPから受け取る
      lin_swtch.onclick=lsi;
      window.addEventListener("DOMContentLoaded",bsi);
      window.addEventListener("DOMContentLoaded",lsi);
    </script>
  </body>
</html>