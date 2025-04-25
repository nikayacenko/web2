<?php
require_once 'function.php';

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

// Отправляем браузеру правильную кодировку,
// файл login.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');
function isValid($login, $db) {
  $count;
  try{
    $stmt = $db->prepare("SELECT COUNT(*) FROM person_LOGIN WHERE login = ?");
    $stmt->execute([$login]);
    $count = $stmt->fetchColumn();
  } 
  catch (PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
  }
  return $count > 0;
}

// В суперглобальном массиве $_SESSION хранятся переменные сессии.
// Будем сохранять туда логин после успешной авторизации.
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

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
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
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
   $login = $_POST['login'];
  $password = $_POST['pass'];

  $user = 'u68600';
  $pass = '8589415';
  $db = new PDO('mysql:host=localhost;dbname=u68600', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

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
