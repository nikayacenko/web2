<?php
require_once 'function.php';
require_once 'db.php';


header('Content-Type: text/html; charset=UTF-8');

$session_started = false;
if ($_COOKIE[session_name()] && session_start()) {
  $session_started = true;
  if (!empty($_SESSION['login'])) {
    if(isset($_POST['logout'])){
      session_unset();
      session_destroy();
      header('Location: login.php');
      exit();
    }
    header('Location: ./');
    exit();
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  include 'tablehtml.php';
?>
    <form class="form" action="" method="post">
      <label class="f">
        Логин:<br>
        <input name="login" />
      </label><br>
      <label class="f">
        Пароль:<br>
        <input name="pass" />
      </label><br>
      <input type="submit" value="Войти" />
      <br><a href="adm.php">Вход для администратора</a><br>
    </form>
</body>

<?php
}
else {
   $login = $_POST['login'];
  $password = $_POST['pass'];
  
  if (!$session_started) {
    session_start();
  }
  if (isValid($login, $db) && password_check($login, $password, $db)){
    $_SESSION['login'] = $_POST['login'];

    $_SESSION['uid'];
    try {
        $stmt_select = $db->prepare("SELECT id FROM person_LOGIN WHERE login=?");
        $stmt_select->execute([$_SESSION['login']]);
        $_SESSION['uid']  = $stmt_select->fetchColumn();
    } catch (PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }

    // Делаем перенаправление.
    header('Location: ./');
  }
  else {
    print('Неверный логин или пароль');
  }
}
