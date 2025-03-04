<?php

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

function password_check($login, $password, $db) {
  $passw;
  try{
    $stmt = $db->prepare("SELECT pass FROM LOGIN WHERE login = :login");
    $stmt->bindParam(':login', $login, PDO::PARAM_STR);
    $stmt->execute();
    $passw = $stmt->fetchColumn();
  } 
  catch (PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
  }
  return ($passw==$password);
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
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>зомбиробоповарозавр</title>
        <link href = "project4/projects/1/static/styles/style.css" rel = "stylesheet">
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
        <script
          src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        

    </head>
    <body>
        <header>
            <div id = "fir">
                <img id="logo" src="project4/projects/1/static/image/флаг.png" alt="Логотип">
                <h1 class="name">Мистер Гиббс: все о пиратах</h1>
            </div>
            </header>
        <div class = "page px-sm-0 mr-sm-0 mb-sm-2">
            <nav class="mt-sm-0 mb-sm-0 px-sm-0 mx-sm-0">
                <ul class="px-3">
                    <li> <a class="px-sm-2" href="project4/projects/1/famous.html"> Известные пираты </a></li>
                    <li> <a class="px-sm-2" href="project4/projects/1/myths.html"> Мифы о пиратах </a></li>
                    <li> <a class="px-sm-2" href="project4/projects/1/bio.html"> Пиратский кодекс </a></li>
                </ul>
            </nav>
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
            </form>

<?php
}
// Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
   $login = $_POST['login'];
  $password = md5($_POST['pass']);

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
