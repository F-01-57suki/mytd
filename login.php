<?php
header('X-FRAME-OPTIONS: SAMEORIGIN');
session_start();
session_regenerate_id(true);
if(isset($_SESSION['username'])):
  header('Location: index.php');
  die();
endif;
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>MYtodo‐ログイン画面</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  </head>
  <body id="login">
    <div id="wrapper">
      <main>
        <h1>MY t<span class="h1bulue">o</span>d<span class="h1red">o</span></h1>
        <div>
          <form action="login_done.php" method="post">
            <p>アカウント名<br><input type="text" name="username"></p>
            <p>パスワード<br><input type="password" name="pass"></p>
            <p><input type="submit" value="ログイン"></p>
          </form>
          <p>新規登録は<a href="signup.php">こちら</a></p>
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