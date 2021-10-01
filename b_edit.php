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

?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
    <title>MYtodo‐ボード設定</title>
  </head>
  <body id="create">
    <div id="wrapper">
      <main>
        <h1>setting</h1>
        <div>
          <p>現在のボード名：<br><span><?php echo $_GET['bnm']; ?></span></p>
        </div>
        <div>
          <form action="b_edit_done.php" method="post">
            <p>
              名前の変更<br>
              <input type="text" name="board_in" class="su_inpt"><br>※全角10文字以内。
            </p>
            <p>
              色の変更<br>
              <div id="bcs">
                <input type="radio" name="b_color" value="bc_gray"><i class="fas fa-palette bcs_gray"></i>
                <input type="radio" name="b_color" value="bc_red"><i class="fas fa-palette bcs_red"></i>
                <input type="radio" name="b_color" value="bc_blue"><i class="fas fa-palette bcs_blue"></i>
                <input type="radio" name="b_color" value="bc_green"><i class="fas fa-palette bcs_green"></i>
                <input type="radio" name="b_color" value="bc_yellow"><i class="fas fa-palette bcs_yellow"></i>
              </div>
            </p>

            <p>ボードの削除<br>中身を含め、ボードが削除されます。<br><span class="caution">この操作は元に戻すことができません。</span></p>
            <p><input type="checkbox" name="b_del" value="del">削除する</p>
            <p class="su_fc">
              <input type="hidden" name="bid" value="<?php echo $_GET['bid']; ?>">
              <input type="button" value="戻る" onclick="history.go(-1)">
              <input type="submit" value="保存">
            </p>

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