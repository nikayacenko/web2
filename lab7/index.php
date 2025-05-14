<?php
require_once 'function.php';
require_once 'db.php';
header('Content-Type: text/html; charset=UTF-8');
//session_start();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    $messages[] = '<div class="result">Спасибо, результаты сохранены.</div>';
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('<div class="result">Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
      и паролем <strong>%s</strong> для изменения данных.</div>',
      strip_tags($_COOKIE['login']),
      strip_tags($_COOKIE['pass']));
    }
  }
  $errors = array();
  $errors['name'] = !empty($_COOKIE['fio_error']);
  $errors['number'] = !empty($_COOKIE['number_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['checkbox'] = !empty($_COOKIE['check_error']);
  $errors['bdate'] = !empty($_COOKIE['date_error']);
  $errors['languages'] = !empty($_COOKIE['lang_error']);
  $errors['gen'] = !empty($_COOKIE['gen_error']);
  $errors['biography'] = !empty($_COOKIE['bio_error']);

  if ($errors['name']) {
    if($_COOKIE['fio_error']=='1'){
        $messages[] = '<div class="messages">Заполните ФИО.</div>';
    }
    elseif($_COOKIE['fio_error']=='2'){
        $messages[] = '<div class="messages">ФИО не должно превышать 128 символов.</div>';
    }
    else{
        $messages[] = '<div class="messages">ФИО должно содержать только символы букв и пробелы.</div>';
    }
    setcookie('fio_error', '', 100000);
    setcookie('fio_value', '', 100000);
  }
  if ($errors['number']) {
    if($_COOKIE['number_error']=='1'){
      $messages[] = '<div class="messages">Номер не указан.</div>';
    }
    elseif($_COOKIE['number_error']=='2'){
      $messages[] = '<div class="messages">Номер заполнен некорректно.</div>';
    }
    setcookie('number_error', '', 100000);
    setcookie('number_value', '', 100000);
  }
  if ($errors['email']) {
    if($_COOKIE['email_error']=='1') {
      $messages[] = '<div class="messages">Email не указан.</div>';
    }
    elseif($_COOKIE['email_error']=='2') {
      $messages[] = '<div class="messages">Введенный email указан некорректно.</div>';
    }
    setcookie('email_error', '', 100000);
    setcookie('email_value', '', 100000);
  }
  if ($errors['bdate']) {
    setcookie('date_error', '', 100000);
    setcookie('date_value', '', 100000);
    $messages[] = '<div class="messages">Заполните дату верно.</div>';
  }
  if ($errors['checkbox']) {
    setcookie('check_error', '', 100000);
    setcookie('check_value', '', 100000);
    $messages[] = '<div class="messages">Подтвердите, что согласны с контрактом.</div>';
  }
  if ($errors['languages']) {
    if($_COOKIE['lang_error']=='1'){
      $messages[] = '<div class="messages">Отметьте любимый язык программирования.</div>';
    }
    elseif($_COOKIE['lang_error']=='2'){
      $messages[] = '<div class="messages">Указан недопустимый язык.</div>';
    }
    setcookie('lang_error', '', 100000);
    setcookie('lang_value', '', 100000);
  }
  
  if ($errors['gen']) {
    setcookie('gen_error', '', 100000);
    setcookie('gen_value', '', 100000);
    $messages[] = '<div class="messages">Укажите пол.</div>';
  }
  if ($errors['biography']) {
    if($_COOKIE['bio_error']=='1'){
      $messages[] = '<div class="messages">Внесите данные биографии.</div>';
      }
      elseif($_COOKIE['bio_error']=='2'){
        $messages[] = '<div class="messages">Используйте только допустимые символы: буквы, цифры, знаки препинания.</div>';
      }
    setcookie('bio_error', '', 100000);
    setcookie('bio_value', '', 100000);
  }

  $values = array();
  $values['name'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
  $values['number'] = empty($_COOKIE['number_value']) ? '' : $_COOKIE['number_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['bdate'] = empty($_COOKIE['date_value']) ? '' : $_COOKIE['date_value'];
  $values['checkbox'] = empty($_COOKIE['check_value']) ? '' : $_COOKIE['check_value'];
  $values['languages'] = empty($_COOKIE['lang_value']) ? '' : $_COOKIE['lang_value'];
  $values['biography'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
  $values['gen'] = empty($_COOKIE['gen_value']) ? '' : $_COOKIE['gen_value'];


  if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_USER'] ==  admin_login_check($db) && admin_password_check($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $db))
    {
      if(!empty($_GET['uid']))
      {
        $update_id = $_GET['uid'];
        $log=loginbyuid($update_id, $db);
        $values=insertData($log, $db);
        $values['uid']=$update_id;
        $messages[] = '<div class="result">Измените данные </div>';
      }
  }
  
  if (isset($_COOKIE[session_name()]) && session_start() &&!empty($_SESSION['login'])) {
    $_SESSION['uid']=getuid($_SESSION['login'],$db);
      $values=insertData(strip_tags($_SESSION['login']),$db);
      $messages[] = '<div class="result">Вход с логином ' . htmlspecialchars($_SESSION['login']) . ", uid " . (int)$_SESSION['uid'] . "</div>";
    }

  include('form.php');

}
else{
  $fav_languages = $_POST['languages'] ?? [];

  $errors = false;
  if (empty($_POST['name'])) {
      setcookie('fio_error', '1', time() + 24 * 60 * 60);
      $errors = true;
  } else {
      // Санитизация: удаляем лишние пробелы и экранируем спецсимволы
      $name = trim($_POST['name']);
      $name = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      if (strlen($name) > 128) {
          setcookie('fio_error', '2', time() + 24 * 60 * 60);
          $errors = true;
      } elseif (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $name)) {
          setcookie('fio_error', '3', time() + 24 * 60 * 60);
          $errors = true;
      }
  }
  setcookie('fio_value', $name, time() + 365 * 24 * 60 * 60);
 
  $number = trim($_POST['number'] ?? '');
  if (empty($number)) {
    setcookie('number_error', '1', time() + 24 * 60 * 60);
    $errors = true;
  } elseif (!preg_match('/^\+7\d{10}$/', $number)) {
    setcookie('number_error', '2', time() + 24 * 60 * 60); 
    $errors = true;
  }
  setcookie('number_value', $number, time() + 365 * 24 * 60 * 60);


  $email = trim($_POST['email'] ?? '');
  if (empty($email)) {
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = true;
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', '2', time() + 24 * 60 * 60);
    $errors = true;
  }
  setcookie('email_value', $email, time() + 365 * 24 * 60 * 60);

  if (empty($_POST['gender'])){
    setcookie('gen_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('gen_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);
  
  $allowed_languages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala"];
  if (empty($fav_languages)) {
    setcookie('lang_error', "1", time() + 24 * 60 * 60);
    $errors = TRUE;
  } /*else {
    foreach ($fav_languages as $lang) {
      if (!in_array($lang, $allowed_languages)) {
          //print('Указан недопустимый язык ($lang).<br/>');
          setcookie('lang_error2', '2', time() + 24 * 60 * 60);
          $errors = TRUE;
      }
    }
  }*/
  $langs_value =(implode(",", $fav_languages));
  setcookie('lang_value', $langs_value, time() + 365 * 24 * 60 * 60);
  
  if (empty($_POST['bdate'])) {
    setcookie('date_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('date_value', $_POST['bdate'], time() + 365 * 24 * 60 * 60);
  
  $biography = trim($_POST['biography'] ?? '');
  if (empty($biography)) {
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = true;
  } elseif (!preg_match('/^[а-яА-Яa-zA-Z0-9.,!?)({}<>|: \-]+$/u', $biography)) {
    setcookie('bio_error', '2', time() + 24 * 60 * 60);
    $errors = true;
  }
  setcookie('bio_value', htmlspecialchars($biography, ENT_QUOTES, 'UTF-8'), time() + 365 * 24 * 60 * 60);

  if (!isset($_POST["checkbox"])) {
    setcookie('check_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('check_value', $_POST['checkbox'], time() + 365 * 24 * 60 * 60);


  if ($errors) {
    if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_USER'] ==  admin_login_check($db) && admin_password_check($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $db))
    {
      header('Location: index.php?uid=' . $_POST['uid'] . '');
      exit();
    }
    else{
      header('Location: index.php');
      exit();
    }
  }
  else {
    setcookie('fio_error', "", 100000);
    setcookie('number_error',"", 100000);
    setcookie('email_error', "", 100000);
    setcookie('date_error', "", 100000);
    setcookie('check_error', "", 100000);
    setcookie('gen_error', "", 100000);
    setcookie('bio_error', "", 100000);
    setcookie('lang_error', "", 100000);
  }
  if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_USER'] ==  admin_login_check($db) && admin_password_check($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $db))
  {
    error_log("Authentication successful!");
    if(isset($_POST['uid'])) {
      try{
        $update_id = strip_tags($_POST['uid']);//XSS
        $doplog=loginbyuid($update_id, $db);
        $lang = $_POST['languages'] ?? [];
        update($update_id,$_POST['name'], $_POST['number'], $_POST['email'], $_POST['bdate'], $_POST['gender'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0,$lang);
        header('Location: adm.php');
        exit();
        }
        catch(PDOException $e){
          header('Location:adm.php');
          exit();
        }
      }
      else{
        print('Вы не выбрали пользователя для изменения');
        exit();
      }
  }
  else{
    if (isset($_COOKIE[session_name()]) && session_start() && !empty($_SESSION['login'])) {
      try {
        $user_id=getuid($_SESSION['login'],$db);
        update($user_id,$_POST['name'], $_POST['number'], $_POST['email'], $_POST['bdate'], $_POST['gender'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0,$fav_languages);
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
    } 
    else{

        $login = generate_pass(7);
      while(check_login($login, $db)>0)
      {
        $login = generate_pass(7);
      }
        $pass = generate_pass();
        $hash_p = password_hash($pass, PASSWORD_DEFAULT);

        setcookie('login', htmlspecialchars($login, ENT_QUOTES, 'UTF-8'));

        setcookie('pass', $pass);
      try{
        insert($login,$hash_p, $db);
      }
      catch (PDOException $e) {
        print('Ошибка БД: ' . $e->getMessage());
        exit();
      }
    }
  }
  setcookie('save', '1');
  header('Location: index.php');
}
