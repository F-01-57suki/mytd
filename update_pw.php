<?php
header('X-FRAME-OPTIONS: SAMEORIGIN');
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
    <title>MYtodo‐変更</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  </head>
  <body id="signup">
    <div id="wrapper">
      <main>
        <h1>Update</h1>
        <div>
          <form action="update_pw_check.php" method="post">
            <p>今のパスワード<br>
            <input type="password" name="pass" class="su_inpt"></p>
            <p>変更したいパスワード<br>
            <input type="password" name="newpass" class="su_inpt"><br>
            ※半角英数字のみ、8～16文字。<br>※大文字・小文字・数字が必須。</p>
            <p><input type="submit" value="確認画面へ" class="su_inpt"></p>
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