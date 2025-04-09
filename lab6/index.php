<?php
require_once 'function.php';
header('Content-Type: text/html; charset=UTF-8');
//session_start();
  $user = 'u68600'; // Заменить на ваш логин uXXXXX
  $pass = '8589415'; // Заменить на пароль
  $db = new PDO('mysql:host=localhost;dbname=u68600', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX

function generate_pass(int $length=12):string{
  $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
  $shuff = str_shuffle($characters);
  return substr($shuff, 0, $length);
}
function check_login($login, $db)
{
  try{
    $stmt = $db->prepare("SELECT COUNT(*) FROM LOGIN WHERE login = :login");
    $stmt->bindParam(':login', $login, PDO::PARAM_STR);
    $stmt->execute();
    $fl = $stmt->fetchColumn();
  }
  catch (PDOException $e){
    print('Error : ' . $e->getMessage());
    return false;
  }
  return $fl;
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  $messages = array();
  if (!empty($_COOKIE['save'])) {
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    $messages[] = '<div class="result">Спасибо, результаты сохранены.</div>';
    print('Вы успешно авторизовались и видите защищенные паролем данные.');
      print("PHP_AUTH_USER: " . (!empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : "empty"));
      print("PHP_AUTH_PW: " . (!empty($_SERVER['PHP_AUTH_PW']) ? "set" : "empty"));
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('<div class="result">Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
      и паролем <strong>%s</strong> для изменения данных.</div>',
      strip_tags($_COOKIE['login']),
      strip_tags($_COOKIE['pass']));
      print('Вы успешно авторизовались и видите защищенные паролем данные.');
      print("PHP_AUTH_USER: " . (!empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : "empty"));
      print("PHP_AUTH_PW: " . (!empty($_SERVER['PHP_AUTH_PW']) ? "set" : "empty"));
      $login_check_result = admin_login_check($db);
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

  // Выдаем сообщения об ошибках.
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
        $update_id = $_GET['uid'];//XSS
        $log=loginbyuid($update_id, $db);
        $values=insertData($log, $db);
        $values['uid']=$update_id;
        //$_POST['uid']=$update_id;
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

  $errors = FALSE;
  if (empty($_POST['name'])) {
    setcookie('fio_error', '1', time() + 24*60*60);
    $errors = TRUE;
  }
  else{
    // Проверка длины
      if (strlen($_POST['name']) > 128) {
        //print( "ФИО не должно превышать 128 символов.<br>");
        setcookie('fio_error', '2', time() + 24*60*60);
        $errors = TRUE;
      }
    // Проверка на только буквы и пробелы (кириллица и латиница)
      elseif (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $_POST['name'])) {
          //print("ФИО должно содержать только буквы и пробелы.<br>");
          setcookie('fio_error', '3', time() + 24 * 60 * 60);
          $errors = TRUE;
      } 
  }
  setcookie('fio_value', $_POST['name'], time() + 365 * 24 * 60 * 60);
 
  $_POST['number']=trim($_POST['number']);
  if(empty($_POST['number'])){
    setcookie('number_error', '1', time() + 24 * 60 * 60);
    $errors= TRUE;
  }elseif(!preg_match('/^[0-9+]+$/', $_POST['number'])){
    setcookie('number_error', '2', time() + 24 * 60 * 60); 
    $errors= TRUE;
  }
  setcookie('number_value', $_POST['number'], time() + 365 * 24 * 60 * 60);


  if(empty($_POST['email'])){
    setcookie('email_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }elseif(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
    setcookie('email_error', '2', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('email_value', $_POST['email'], time() + 365 * 24 * 60 * 60);

  if (empty($_POST['gender'])){
    //print ('Укажите пол.<br/>');
    setcookie('gen_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('gen_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);

  
  $allowed_languages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala"];
  if (empty($fav_languages)) {
    //print('Выберите хотя бы один язык программирования.<br/>');
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
    //print('Заполните дату.<br/>');
    setcookie('date_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('date_value', $_POST['bdate'], time() + 365 * 24 * 60 * 60);

  if (!isset($_POST["gender"])) {
    setcookie('gen_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('gen_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);
  
  if (empty($_POST['biography'])) {
    //print('Заполните биографию.<br/>');
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }elseif(!preg_match('/^[а-яА-Яa-zA-Z1-9.,!?: ]+$/u', $_POST['biography'])){
    //print('Поле "биография" содержит недопустимые символы.<br/>');
    setcookie('bio_error', '2', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('bio_value', $_POST['biography'], time() + 365 * 24 * 60 * 60);

  // С КОНТРАКТОМ ОЗНАКОМЛЕН
  if (!isset($_POST["checkbox"])) {
    //print('Вы должны подтвердить ознакомление с контрактом.<br/>');
    setcookie('check_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('check_value', $_POST['checkbox'], time() + 365 * 24 * 60 * 60);


  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
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

  if (!empty($_SERVER['PHP_AUTH_USER'])){
    error_log("Authentication successful!");
    if(isset($_POST['uid'])) {
      $user_id = $_POST['uid'];
      $lang = $_POST['languages'] ?? [];
      update($user_id,$_POST['name'], $_POST['number'], $_POST['email'], $_POST['bdate'], $_POST['gender'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0,$lang);
      header('Location: adm.php');
      exit();
    } else{
      print('Пользователь для изменения не выбран');
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
      /*$user_id;
      try {
          $stmt_select = $db->prepare("SELECT id FROM person_LOGIN WHERE login=?");
          $stmt_select->execute([$_SESSION['login']]);
          $user_id = $stmt_select->fetchColumn();
      } catch (PDOException $e){
          print('Error : ' . $e->getMessage());
          exit();
      }

      //update
      try {
          $stmt_update = $db->prepare("UPDATE application SET name=?, number=?, email=?, bdate=?, gender=?, biography=?, checkbox=? WHERE id=?");
          $stmt_update->execute([$_POST['name'], $_POST['number'], $_POST['email'], $_POST['bdate'], $_POST['gender'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0, $user_id ]);
      
          $stmt_delete = $db->prepare("DELETE FROM prog_lang WHERE id=?");
          $stmt_delete -> execute([$user_id]);
          $stmt_select = $db->prepare("SELECT id_lang_name FROM prog WHERE lang_name = ?");
          $insert_stmt = $db->prepare("INSERT INTO prog_lang (id, id_lang_name) VALUES (?,?)");
          foreach ($fav_languages as $language) {
            $stmt_select->execute([$language]);
            $language_id = $stmt_select->fetchColumn();
            
            if ($language_id) {
                $insert_stmt->execute([$user_id, $language_id]);
            }
          }
      } catch (PDOException $e){
          print('update Error : ' . $e->getMessage());
          exit();
      }*/

    } 
    else{
      /*try {
        $stmt = $db->prepare("INSERT INTO application(name, number, email, gender, bdate, biography, checkbox) values(?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['number'], $_POST['email'], $_POST['gender'], $_POST['bdate'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0]);
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
      $user_id = $db->lastInsertId();*/ 
    
        $login = generate_pass(7);
        while(check_login($login, $db)>0)
      
        $login = generate_pass(7);
      
        $pass = generate_pass();
        $hash_p = password_hash($pass, PASSWORD_DEFAULT);
        setcookie('login', $login);
        setcookie('pass', $pass);
        /*$stmt = $db->prepare("INSERT INTO LOGIN (login, pass) VALUES (:login, :pass)");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':pass', $hash_p);
        $stmt->execute();
        $stmt = $db->prepare("INSERT INTO person_LOGIN (id, login) VALUES (:id, :login)");
        $stmt->bindParam(':id', $user_id);
        $stmt->bindParam(':login', $login);
        $stmt->execute();*/
      try{
        insert($login, $hash_p, $db);
      }
        catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
      /*try{
        $stmt = $db->prepare("SELECT id_lang_name FROM prog WHERE lang_name = ?");
        $insert_stmt = $db->prepare("INSERT INTO prog_lang (id, id_lang_name) VALUES (?, ?)");
        
        foreach ($fav_languages as $language) {
            $stmt->execute([$language]);
            $language_id = $stmt->fetchColumn();
            if ($language_id) {
                $insert_stmt->execute([$user_id, $language_id]);
            }
        }
      }
      catch (PDOException $e) {
        print('Ошибка БД: ' . $e->getMessage());
        exit();
      }*/
    }
  }
  setcookie('save', '1');
  header('Location: ./');
}
