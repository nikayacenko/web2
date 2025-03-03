<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');
  $user = 'u68600'; // Заменить на ваш логин uXXXXX
  $pass = '8589415'; // Заменить на пароль
  $db = new PDO('mysql:host=localhost;dbname=u68600', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();
  // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    setcookie('login', '', 100000);
    setcookie('pass', '', 100000);
    // Если есть параметр save, то выводим сообщение пользователю.
    $messages[] = '<div class="result">Спасибо, результаты сохранены.</div>';
    if (!empty($_COOKIE['pass'])) {
      $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
      и паролем <strong>%s</strong> для изменения данных.',
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
    // Удаляем куки, указывая время устаревания в прошлом.
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
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('date_error', '', 100000);
    setcookie('date_value', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="messages">Заполните дату верно.</div>';
  }
  if ($errors['checkbox']) {
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('check_error', '', 100000);
    setcookie('check_value', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="messages">Подтвердите, что согласны с контрактом.</div>';
  }
  if ($errors['languages']) {
    if($_COOKIE['lang_error']=='1'){
      $messages[] = '<div class="messages">Отметьте любимый язык программирования.</div>';
    }
    elseif($_COOKIE['lang_error']=='2'){
      $messages[] = '<div class="messages">Указан недопустимый язык.</div>';
    }
    // Удаляем куки, указывая время устаревания в прошлом.
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
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('bio_error', '', 100000);
    setcookie('bio_value', '', 100000);
  }

  // TODO: тут выдать сообщения об ошибках в других полях.

  // Складываем предыдущие значения полей в массив, если есть.
  $values = array();
  $values['name'] = empty($_COOKIE['fio_value']) ? '' : $_COOKIE['fio_value'];
  $values['number'] = empty($_COOKIE['number_value']) ? '' : $_COOKIE['number_value'];
  $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
  $values['bdate'] = empty($_COOKIE['date_value']) ? '' : $_COOKIE['date_value'];
  $values['checkbox'] = empty($_COOKIE['check_value']) ? '' : $_COOKIE['check_value'];
  $values['languages'] = empty($_COOKIE['lang_value']) ? '' : $_COOKIE['lang_value'];
  $values['biography'] = empty($_COOKIE['bio_value']) ? '' : $_COOKIE['bio_value'];
  $values['gen'] = empty($_COOKIE['gen_value']) ? '' : $_COOKIE['gen_value'];

  if (empty($errors) && !empty($_COOKIE[session_name()]) &&
    session_start() && !empty($_SESSION['login'])) {
    // TODO: загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
    // Для загрузки данных из БД делаем запрос SELECT и вызываем метод PDO fetchArray(), fetchObject() или fetchAll() 
    /*try{
      $stmt = $db->prepare("SELECT fio, number, email, biography AS bio, gender AS gen, bdate, checkbox FROM application WHERE login = ?");
      $stmt->execute([$_SESSION['login']]);
      $values = $stmt->fetchArray();
    } 
    catch (PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }*/
     try{
       $mas=[];

        $stmt = $db->prepare("SELECT fio, number, email, biography AS bio, gender AS gen, bdate, checkbox FROM application WHERE id = ?");
        $stmt->execute([$_SESSION['uid']]);
        $mas = $stmt->fetch(PDO::FETCH_ASSOC);
        $fields = ['fio', 'number', 'email', 'bio', 'gen', 'bdate', 'checkbox'];
        foreach($fields as $field) {
            $values[$field] = strip_tags($mas[$field]);
        }
     }
     catch (PDOException $e){
       print('ERROR : ' . $e->getMessage());
       exit();
     }

    
    try {
      $get_lang=[];
      $mas=[];
      $stmt_lang = $db->prepare("SELECT id_lang FROM user_lang WHERE id = ?");
      $stmt_lang->execute([$_SESSION['uid']]);
      $mas = $stmt_lang->fetch(PDO::FETCH_ASSOC);

      $stmt_get_lang = $db->prepare("SELECT lang_name FROM prog_lang WHERE id_lang=?");

      foreach ($mas as $id) {
        
          $stmt_get_lang->execute([$id]);
          $lang_name = $stmt_get_lang->fetchColumn();
          $get_lang = $lang_name;
      }
          
      $values['lang'] = $get_lang;
  } catch (PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
  }
        // 5. Закрытие курсора (необязательно, но рекомендуется)
    // TODO: загрузить данные пользователя из БД
    // и заполнить переменную $values,
    // предварительно санитизовав.
    // Для загрузки данных из БД делаем запрос SELECT и вызываем метод PDO fetchArray(), fetchObject() или fetchAll() 
    // См. https://www.php.net/manual/en/pdostatement.fetchall.php
        printf('Вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
    }

  include('form.php');
  //include('index.html');
  // Завершаем работу скрипта.
  //exit();
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в БД.
else{
  // Проверяем ошибки.

  $fio = $_POST['name'];
  $number = $_POST['number'];
  $email = $_POST['email'];
  $bdate = $_POST['bdate'];
  $gender = $_POST['gender'];
  $biography = $_POST['biography'];
  //$checkbox = $_POST['checkbox'];
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
  }elseif(!preg_match('/^[а-яА-Яa-zA-Z1-9.,: ]+$/u', $_POST['biography'])){
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
    // Удаляем Cookies с признаками ошибок.
    setcookie('fio_error', "", 100000);
    setcookie('number_error',"", 100000);
    setcookie('email_error', "", 100000);
    setcookie('date_error', "", 100000);
    setcookie('check_error', "", 100000);
    setcookie('gen_error', "", 100000);
    setcookie('bio_error', "", 100000);
    setcookie('lang_error', "", 100000);
  }
  if (!empty($_COOKIE[session_name()]) &&
  session_start() && !empty($_SESSION['login'])) {
    $user_id;
    try {
        $stmt_select = $db->prepare("SELECT id FROM LOGIN WHERE login=?");
        $stmt_select->execute([$_SESSION['login']]);
        $user_id = $stmt_select->fetchColumn();
    } catch (PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }

    //update
    try {
        $stmt_update = $db->prepare("UPDATE application SET fio=?, number=?, email=?, bdate=?, gender=?, biography=?, checkbox=? WHERE id=?");
        $stmt_update->execute([$_POST['fio'], $_POST['number'], $_POST['email'], $_POST['birthdate'], $_POST['radio-group-1'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0, $user_id ]);
    
        $stmt_delete = $db->prepare("DELETE FROM prog_lang WHERE id=?");
        $stmt_delete -> execute([$user_id]);
        $stmt_select = $db->prepare("SELECT id_lang_name FROM prog WHERE lang_name = ?");
        $insert_stmt = $db->prepare("INSERT INTO prog_lang (id, id_lang_name) VALUES (?,?)");
        foreach ($fav_languages as $language) {
          // Получаем ID языка программирования
          $stmt->execute([$language]);
          $language_id = $stmt->fetchColumn();
          
          if ($language_id) {
              // Связываем пользователя с языком
              $insert_stmt->execute([$user_id, $language_id]);
          }
        }
    } catch (PDOException $e){
        print('update Error : ' . $e->getMessage());
        exit();
    }

  } 
  // TODO: перезаписать данные в БД новыми данными,
  // кроме логина и пароля.
  
  // Сохранение в базу данных.
  else{

    /*$user = 'u68600'; // Заменить на ваш логин uXXXXX
    $pass = '8589415'; // Заменить на пароль
    $db = new PDO('mysql:host=localhost;dbname=u68600', $user, $pass,
      [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX
    $table_app = 'applucation';
    $table_lang = 'prog_lang';
    $table_ul = 'user_lang';*/
  
    // Подготовленный запрос. Не именованные метки.
    try {
      $stmt = $db->prepare("INSERT INTO application(name, number, email, gender, bdate, biography, checkbox) values(?,?,?,?,?,?,?)");
      $stmt->execute([$_POST['name'], $_POST['number'], $_POST['email'], $_POST['gender'], $_POST['bdate'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0]);
      /*$login = rand()%10000000;
      $pass = rand()%10000000000;
      // Сохраняем в Cookies.
      $hash_pass=md5($pass);
      setcookie('login', $login);
      setcookie('pass', $pass);
      $stmt = $db->prepare("INSERT INTO LOGIN(login, pass) values(?,?)");
      $stmt = execute($_POST['login'], $_POST['pass']);
      $stmt = $db->prepare("INSERT INTO person_LOGIN(id, login) values(?,?)");
      $stmt = execute($_POST['id'], $_POST['login']);*/
          
      /*$user_id = $db->lastInsertId();
      $stmt = $db->prepare("SELECT id_lang_name FROM prog WHERE lang_name = ?");
      $insert_stmt = $db->prepare("INSERT INTO prog_lang (id, id_lang_name) VALUES (?, ?)");
      foreach ($fav_languages as $language) {
          // Получаем ID языка программирования
          $stmt->execute([$language]);
          $language_id = $stmt->fetchColumn();
          
          if ($language_id) {
              // Связываем пользователя с языком
              $insert_stmt->execute([$user_id, $language_id]);
          }
      }// ID последнего вставленного пользователя*/
    }
    catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
    $user_id = $db->lastInsertId(); 
    try{
      $login = rand()%10000000;
      $pass = rand()%10000000000;
      // Сохраняем в Cookies.
      $hash_pass=md5($pass);
      setcookie('login', $login);
      setcookie('pass', $pass);
      $stmt = $db->prepare("INSERT INTO LOGIN (login, pass) VALUES (:login, :pass)");
      $stmt->bindParam(':login', $login);
      $stmt->bindParam(':pass', $hash_pass);
      $stmt->execute();
      $stmt = $db->prepare("INSERT INTO person_LOGIN (id, login) VALUES (:id, :login)");
      $stmt->bindParam(':id', $user_id);
      $stmt->bindParam(':login', $login);
      $stmt->execute();
    }
      catch(PDOException $e){
      print('Error : ' . $e->getMessage());
      exit();
    }
    //$user_id = $db->lastInsertId(); // ID последнего вставленного пользователя
    try{
      $stmt = $db->prepare("SELECT id_lang_name FROM prog WHERE lang_name = ?");
      $insert_stmt = $db->prepare("INSERT INTO prog_lang (id, id_lang_name) VALUES (?, ?)");
      
      foreach ($fav_languages as $language) {
          // Получаем ID языка программирования
          $stmt->execute([$language]);
          $language_id = $stmt->fetchColumn();
          
          if ($language_id) {
              // Связываем пользователя с языком
              $insert_stmt->execute([$user_id, $language_id]);
          }
      }
    }
    catch (PDOException $e) {
      print('Ошибка БД: ' . $e->getMessage());
      exit();
    }
  }
  setcookie('save', '1');
  header('Location: index.php');
}
