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

//背景色の取得
$c_arr=array();
$sql = mysqli_prepare($link,'SELECT `b_id`,`b_color` FROM `board_tbl` WHERE `username`=?');
mysqli_stmt_bind_param($sql,'s',$_SESSION['username']);
mysqli_stmt_execute($sql);
mysqli_stmt_store_result($sql);
if(mysqli_stmt_num_rows($sql) != 0):
  mysqli_stmt_bind_result($sql,$b_id_c,$b_color);
  while(mysqli_stmt_fetch($sql)):
    $c_arr[$b_id_c] = $b_color;
  endwhile;
endif;
mysqli_stmt_close($sql);

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
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>MYtodo</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  </head>
  <body id="index">
    <header>
      <h1>MY t<span class="h1bulue">o</span>d<span class="h1red">o</span>
        <span class="h1user">‐<?php echo $_SESSION['username']; ?>でログイン中</span>
      </h1>
      <nav>
        <ul>
          <li><a href="create.php"><i class="fas fa-plus"></i></a></li>
          <li><a href="csv.php"><i class="fas fa-file-download"></i></a></li>
          <li><a href="user.php"><i class="fas fa-user"></i></a></li>
          <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i></a></li>
        </ul>
      </nav>
    </header>
    <div id="wrapper">
      <main>
<?php
//ボードの書き出し
foreach($b_arr as $key => $value):
  ?>
        <section class="board <?php echo $c_arr[$key]; ?>">
          <div class="b_title">
            <h1><?php echo $value; ?></h1>
            <a href="b_edit.php?bid=<?php echo $key; ?>&bnm=<?php echo $value; ?>"><i class="fas fa-ellipsis-h"></i></a>
          </div>
  <?php
  //付箋の取得
  $sql = mysqli_prepare($link,'SELECT `p_id`,`cont`,`l_id` FROM `postit_tbl` WHERE `b_id`=?');
  mysqli_stmt_bind_param($sql,'s',$key);
  mysqli_stmt_execute($sql);
  mysqli_stmt_store_result($sql);
  if(mysqli_stmt_num_rows($sql) != 0):
    mysqli_stmt_bind_result($sql,$p_id,$cont,$l_id);
    while(mysqli_stmt_fetch($sql)):
      ?>
          <div class="b_cont">
            <div class="b_c_menu">
              <div class="b_label"><?php echo $l_arr[$l_id]; ?></div>
              <div class="b_c_btn">
                <a href="p_edit.php?pid=<?php echo $p_id; ?>"><i class="far fa-edit"></i></a>
                <a href="#" onclick="p_delete(<?php echo $p_id; ?>)"><i class="fas fa-times pd"></i></a>
              </div>
            </div>
            <p><?php echo $cont; ?></p>
          </div>
      <?php
    endwhile;
  else:
  ?>
          <div class="b_cont"><p>内容がありません。画面右上の「＋」から作成して下さい。</p></div>
  <?php
  endif;
  mysqli_stmt_close($sql);
  ?>
        </section>
<?php
endforeach;
mysqli_close($link);
?>
      </main>
    </div>
    <footer>
      <p>copyright &copy; <?php echo date('Y'); ?> Miyashita.</p>
    </footer>
    <script>
      function logout() {
        let outyn;
        outyn = confirm("ログアウトしますか？");
        if(outyn){
          document.location.href = "logout.php";
        }
      }
      function p_delete(num) {
        let delyn;
        delyn = confirm("削除してよろしいですか？");
        if(delyn){
          document.location.href = "p_delete.php?pid="+num;
        }
      }
    </script>
  </body>
</html>