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
    <title>MYtodo‐退会</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  </head>
  <body id="unsubscribe">
    <div id="wrapper">
      <main>
        <h1>Delete Account</h1>
        <div>
          <form action="unsubscribe_check.php" method="post">
            <p>アカウント名<br>
            <?php echo $_SESSION['username']; ?></p>
            <p>パスワード<br>
            <input type="password" name="pass" class="su_inpt"></p>
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