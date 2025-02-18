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
    $messages[] = 'Спасибо, результаты сохранены.';
  }
  $errors = array();
  $errors['name'] = !empty($_COOKIE['fio_error']);
  $errors['number'] = !empty($_COOKIE['number_error']);
  $errors['email'] = !empty($_COOKIE['email_error']);
  $errors['checkbox'] = !empty($_COOKIE['check_error']);
  $errors['bdate'] = !empty($_COOKIE['date_error']);
  $errors['languages'] = !empty($_COOKIE['lang_error']);
  $errors['gen1'] = !empty($_COOKIE['gen_error']);
  $errors['biography'] = !empty($_COOKIE['bio_error']);



  // TODO: аналогично все поля.


  
  // Выдаем сообщения об ошибках.
  if ($errors['name']) {
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('fio_error', '', 100000);
    setcookie('fio_value', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="messages">Заполните имя верно.</div>';
  }
  if ($errors['number']) {
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('number_error', '', 100000);
    setcookie('number_value', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="messages">Заполните номер телефона верно.</div>';
  }
  if ($errors['email']) {
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('email_error', '', 100000);
    setcookie('email_value', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="messages">Заполните email верно.</div>';
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
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('lang_error', '', 100000);
    setcookie('lang_value', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="messages">Отметьте любимый язык программирования.</div>';
  }
  if ($errors['gen1']) {
    setcookie('gen_error1', '', 100000);
    setcookie('gen_value', '', 100000);
    $messages[] = '<div class="error">Укажите пол.</div>';
  }
    if ($errors['biography']) {
    // Удаляем куки, указывая время устаревания в прошлом.
    setcookie('bio_error', '', 100000);
    setcookie('bio_value', '', 100000);
    // Выводим сообщение.
    $messages[] = '<div class="messages">Внесите данные биографии.</div>';
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





  // Включаем содержимое файла form.php.

  include('form.php');
  //include('index.html');
  // Завершаем работу скрипта.
  exit();
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
    setcookie('fio_error', "1", time() + 365*24*60*60);
    $errors = TRUE;
  }
  else{
    // Проверка длины
      if (strlen($_POST['name']) > 150) {
        //print( "ФИО не должно превышать 150 символов.<br>");
        setcookie('fio_error', "1", time() + 365*24*60*60);
        $errors = TRUE;
      }

    // Проверка на только буквы и пробелы (кириллица и латиница)
      elseif (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $_POST['name'])) {
          //print("ФИО должно содержать только буквы и пробелы.<br>");
          setcookie('fio_error', "1", time() + 24 * 60 * 60);
          $errors = TRUE;
      } 
  }

  setcookie('fio_value', $_POST['name'], time() + 30 * 24 * 60 * 60);
  /*if (empty($_POST['number'])) {
    print('Заполните номер телефона.<br/>');
    $errors = TRUE;
  }*/
  $_POST['number']=trim($_POST['number']);
  if(empty($_POST['number']) || !preg_match('/^[0-9+]+$/', $_POST['number'])) {
    //print('Заполните корректно номер телефона (номер телефона должен содержать только цифры!).<br/>');
    setcookie('number_error', "1", time() + 24 * 60 * 60);
    $errors= TRUE;
  }
  setcookie('number_value', $_POST['number'], time() + 30 * 24 * 60 * 60);

  if (empty($_POST['email'])|| !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    //print('Заполните email корректно.<br/>');
    setcookie('email_error', "1", time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);

   if (empty($_POST['gender'])){
    //print ('Укажите пол.<br/>');
    setcookie('gen_error1', '1', time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('gen_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);

  
  $allowed_languages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala"];
  if (empty($fav_languages)) {
    //print('Выберите хотя бы один язык программирования.<br/>');
    setcookie('lang_error', "1", time() + 24 * 60 * 60);
    $errors = TRUE;
  } 
  $langs_value =(implode(",", $fav_languages));
  setcookie('lang_value', $langs_value, time() + 30 * 24 * 60 * 60);
  


  if (empty($_POST['bdate'])) {
    //print('Заполните дату.<br/>');
    setcookie('date_error', "1", time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('date_value', $_POST['bdate'], time() + 30 * 24 * 60 * 60);
  //setcookie('gen_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);
  if (!isset($_POST["gender"])) {
    setcookie('gen_error', "1", time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('gen_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);
  
  if (empty($_POST['biography'])) {
    //print('Заполните дату.<br/>');
    setcookie('bio_error', "1", time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('bio_value', $_POST['biography'], time() + 30 * 24 * 60 * 60);

  // С КОНТРАКТОМ ОЗНАКОМЛЕН
  if (!isset($_POST["checkbox"])) {
    //print('Вы должны подтвердить ознакомление с контрактом.<br/>');
    setcookie('check_error', "1", time() + 24 * 60 * 60);
    $errors = TRUE;
  }
  setcookie('check_value', $_POST['checkbox'], time() + 30 * 24 * 60 * 60);

  // *************
  // Тут необходимо проверить правильность заполнения всех остальных полей.
  // *************

  if ($errors) {
    // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
    header('Location: index.php');
    exit();
  }
  else {
    // Удаляем Cookies с признаками ошибок.
    setcookie('fio_error', "", time() + 24 * 60 * 60);
    setcookie('number_error',"", time() + 24 * 60 * 60);
    setcookie('email_error', "", time() + 24 * 60 * 60);
    setcookie('date_error', "", time() + 24 * 60 * 60);
    setcookie('check_error', "", time() + 24 * 60 * 60);
    setcookie('gen_error1', "", time() + 24 * 60 * 60);
    setcookie('bio_error', "", time() + 24 * 60 * 60);
    setcookie('lang_error', "", time() + 24 * 60 * 60);



    // TODO: тут необходимо удалить остальные Cookies.
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
    $stmt = $db->prepare("SELECT id FROM prog WHERE name = ?");
    $insert_stmt = $db->prepare("INSERT INTO prog_lang (id, id_lang_name) VALUES (?, ?)");
    
    foreach ($fav_languages as $language) {
        // Получаем ID языка программирования
        $stmt->execute([$language]);
        $language_id = $stmt->fetchColumn();
        
        if ($language_id) {
            // Связываем пользователя с языком
            $insert_stmt->execute([$id, $id_lang_name]);
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



