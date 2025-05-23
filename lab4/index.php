<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // Массив для временного хранения сообщений пользователю.
  $messages = array();
  // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
  if (!empty($_COOKIE['save'])) {
    // Удаляем куку, указывая время устаревания в прошлом.
    setcookie('save', '', 100000);
    // Если есть параметр save, то выводим сообщение пользователю.
    $messages[] = '<div class="result">Спасибо, результаты сохранены.</div>';
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
    /*elseif($_COOKIE['lang_error']=='2'){
      $messages[] = '<div class="messages">Указан недопустимый язык.</div>';
    }*/
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

  // Включаем содержимое файла form.php.

  include('form.php');
  //include('index.html');
  // Завершаем работу скрипта.
  exit();
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в БД.
else{
  // Проверяем ошибки.

  $fav_languages = $_POST['languages'] ?? [];

  $errors = FALSE;
  if (empty($_POST['name'])) {
    setcookie('fio_error', '1', time() + 24*60*60);
    $errors = TRUE;
  }
  else{
    // Проверка длины
      if (strlen($_POST['name']) > 128) {
        setcookie('fio_error', '2', time() + 24*60*60);
        $errors = TRUE;
      }
    // Проверка на только буквы и пробелы (кириллица и латиница)
      elseif (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $_POST['name'])) {
          setcookie('fio_error', '3', time() + 24 * 60 * 60);
          $errors = TRUE;
      } 
  }
  setcookie('fio_value', $_POST['name'], time() + 365 * 24 * 60 * 60);
 
  $_POST['number']=trim($_POST['number']);
  if(empty($_POST['number'])){
    setcookie('number_error', '1', time() + 24 * 60 * 60);
    $errors= TRUE;
  }elseif(!preg_match('/^\+7\d{10}$/', $_POST['number'])){
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

  if (!isset($_POST["gender"])) {
    setcookie('gen_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('gen_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);
  
  if (empty($_POST['biography'])) {
    //print('Заполните биографию.<br/>');
    setcookie('bio_error', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }elseif(!preg_match('/^[а-яА-Яa-zA-Z0-9.,!?)({}<>|: ]+$/u', $_POST['biography'])){
    //print('Поле "биография" содержит недопустимые символы.<br/>');
    setcookie('bio_error', '2', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('bio_value', $_POST['biography'], time() + 365 * 24 * 60 * 60);

  // С КОНТРАКТОМ ОЗНАКОМЛЕН
  if (!isset($_POST["checkbox"])) {
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

  // Сохранение в базу данных.

  $user = 'u68600'; // Заменить на ваш логин uXXXXX
  $pass = '8589415'; // Заменить на пароль
  $db = new PDO('mysql:host=localhost;dbname=u68600', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); // Заменить test на имя БД, совпадает с логином uXXXXX
  $table_app = 'applucation';
  $table_lang = 'prog_lang';
  $table_ul = 'user_lang';

  // Подготовленный запрос. Не именованные метки.
  try {
    $stmt = $db->prepare("INSERT INTO application(name, number, email, gender, bdate, biography, checkbox) values(?,?,?,?,?,?,?)");
    $stmt->execute([$_POST['name'], $_POST['number'], $_POST['email'], $_POST['gender'], $_POST['bdate'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0]);
  }
  catch(PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
  }

  $user_id = $db->lastInsertId(); // ID последнего вставленного пользователя
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

  setcookie('save', '1');
  header('Location: index.php');
}



