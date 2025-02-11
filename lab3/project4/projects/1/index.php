<?php
// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
  // В суперглобальном массиве $_GET PHP хранит все параметры, переданные в текущем запросе через URL.
  if (!empty($_GET['save'])) {
    // Если есть параметр save, то выводим сообщение пользователю.
    print('Спасибо, результаты сохранены.');
  }
  // Включаем содержимое файла form.php.
  include('form.php');
  // Завершаем работу скрипта.
  exit();
}
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в БД.

// Проверяем ошибки.

$fio = $_POST['name'];
$number = $_POST['number'];
$email = $_POST['email'];
$bday = $_POST['bdate'];
$gen = $_POST['gen'];
$bio = $_POST['bio'];
$checkbox = $_POST['checkbox'];
$fav_languages = $_POST['languages'] ?? [];



$errors = FALSE;
if (empty($_POST['name'])) {
  print('Заполните имя.<br/>');
  $errors = TRUE;
}
else{
  // Проверка длины
    if (strlen($_POST['name']) > 128) {
      print( "Ошибка: ФИО не должно превышать 150 символов.<br>");
      $errors = TRUE;
    }

  // Проверка на только буквы и пробелы (кириллица и латиница)
    elseif (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]+$/u", $_POST['fio'])) {
        print("Ошибка: ФИО должно содержать только буквы и пробелы.<br>");
        $errors = TRUE;
    } 
}

if (empty($_POST['number']) || !preg_match('/^\+7\d{10}$/', $_POST['phone'])) {
  print('Заполните номер.<br/>');
  $errors = TRUE;
}

if (empty($_POST['email'])|| !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
  print('Заполните email.<br/>');
  $errors = TRUE;
}

$allowed_languages = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala"];
if (empty($fav_languages)) {
  print('Ошибка: Выберите хотя бы один язык программирования.<br/>');
  $errors = TRUE;
} 


// С КОНТРАКТОМ ОЗНАКОМЛЕН
if (!isset($_POST["checkbox"])) {
  print('Ошибка: Вы должны подтвердить ознакомление с контрактом.<br/>');
  $errors = TRUE;
}

// *************
// Тут необходимо проверить правильность заполнения всех остальных полей.
// *************

if ($errors) {
  // При наличии ошибок завершаем работу скрипта.
  exit();
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
  $stmt = $db->prepare("INSERT INTO application(name, number, email, gen, bday, bio, checkbox) values(?,?,?,?,?,?,?)");
  $stmt->execute([$_POST['name'], $_POST['number'], $_POST['email'], $_POST['gen'], $_POST['bdate'], $_POST['bio'], isset($_POST["checkbox"]) ? 1 : 0]);
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
// Собираем данные для запроса
//$data = array( 'fio' => $name, 'number' => $number,'email' => $email,'bday' => $bday,'gen' => $gen, 'bio' => $bio, 'checkbox' => $checkbox ); 
// Подготавливаем SQL-запрос
//$stmp = $db->execute("INSERT INTO $table_app (fio, number, email, bday, gen, bio, checkbox) values (:fio, :number, :email, :bday, :gen, :bio, :checkbox)");
// Выполняем запрос с данными
//$stmp->execute($data);

//  stmt - это "дескриптор состояния".
 
//  Именованные метки.
//$stmt = $db->prepare("INSERT INTO test (label,color) VALUES (:label,:color)");
//$stmt -> execute(['label'=>'perfect', 'color'=>'green']);
 
//Еще вариант
/*$stmt = $db->prepare("INSERT INTO users (firstname, lastname, email) VALUES (:firstname, :lastname, :email)");
$stmt->bindParam(':firstname', $firstname);
$stmt->bindParam(':lastname', $lastname);
$stmt->bindParam(':email', $email);
$firstname = "John";
$lastname = "Smith";
$email = "john@test.com";
$stmt->execute();
*/

// Делаем перенаправление.
// Если запись не сохраняется, но ошибок не видно, то можно закомментировать эту строку чтобы увидеть ошибку.
// Если ошибок при этом не видно, то необходимо настроить параметр display_errors для PHP.
header('Location: ?save=1');
