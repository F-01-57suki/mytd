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
    <title>MYtodo‐ユーザー情報</title>
    <link href="style.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  </head>
  <body id="user">
    <div id="wrapper">
      <main>
        <h1>User</h1>
        <div>
          <table>
            <tr>
              <th>アカウント名：</th>
              <td><?php echo $_SESSION['username']; ?></td>
              <td><a href="update_un.php"><i class="fas fa-edit"></i></a></td>
            </tr>
            <tr>
              <th>パスワード：</th>
              <td><?php echo str_repeat("*",mb_strlen($_SESSION['pass'],"UTF8")); ?></td>
              <td><a href="update_pw.php"><i class="fas fa-edit"></i></a></td>
            </tr>
          </table>
        </div>
        <div>
          <form class="su_fc">
            <td><input type="button" value="戻る" onclick="history.go(-1)"></td>
            <td><input type="button" value="退会" onclick="location.href='unsubscribe.php'"></td>
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