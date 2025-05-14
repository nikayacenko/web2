<?php
require_once 'db.php';
global $db;
$user = 'u68600';
$pass = '8589415';
$db = new PDO('mysql:host=localhost;dbname=u68600', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

function insertData($login, $db) {
    $values = []; // Локальная переменная для хранения данных

    // SQL-запросы и соответствующие ключи в массиве $values
    $queries = [
        'name' => "SELECT name FROM application JOIN person_LOGIN USING(id) WHERE login = :login",
        'email' => "SELECT email FROM application JOIN person_LOGIN USING(id) WHERE login = :login",
        'number' => "SELECT number FROM application JOIN person_LOGIN USING(id) WHERE login = :login",
        'bdate' => "SELECT bdate FROM application JOIN person_LOGIN USING(id) WHERE login = :login",
        'gen' => "SELECT gender FROM application JOIN person_LOGIN USING(id) WHERE login = :login",
        'biography' => "SELECT biography FROM application JOIN person_LOGIN USING(id) WHERE login = :login"
    ];
    foreach ($queries as $key => $sql) {
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':login', $login, PDO::PARAM_STR);
            $stmt->execute();
            $values[$key] = $stmt->fetchColumn();
        } catch (Exception $e) {
            print("Ошибка при выполнении запроса для ключа " . $key . ": " . $e->getMessage());
            $values[$key] = null;
        }
    }
    $sql = "select pl.lang_name from prog pl JOIN prog_lang ul
     ON pl.id_lang_name=ul.id_lang_name
     join person_LOGIN l on ul.id=l.id where l.login = :login;";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $lang = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $values['languages'] = implode(",", $lang);
    } catch (PDOException $e) {
        print("Ошибка при получении языков: " . $e->getMessage());
        $values['languages'] = null; 
    }

    return $values;
}

function password_check($login, $password, $db) {
    $passw;
    try{
      $stmt = $db->prepare("SELECT pass FROM LOGIN WHERE login = :login");
      $stmt->bindParam(':login', $login, PDO::PARAM_STR);
      $stmt->execute();
      $passw = $stmt->fetchColumn();
      if($passw===false){
        return false;
      }
      return password_verify($password, $passw);
    } 
    catch (PDOException $e){
      print('Error : ' . $e->getMessage());
      return false;
    }
}

  function admin_password_check($login, $password, $db) {
    $passw;
    try{
      $stmt = $db->prepare("SELECT pass FROM LOGIN WHERE login = ? and role='admin'");
      $stmt->bindParam(1, $login, PDO::PARAM_STR);
      $stmt->execute();
      $passw = $stmt->fetchColumn();
      if($passw===false){
        return false;
      }
      return ($password==$passw);
    } 
    catch (PDOException $e){
      print('Error : ' . $e->getMessage());
      return false;
    }
}

  function admin_login_check($db) {
      $stmt = $db->prepare("SELECT login FROM LOGIN WHERE role='admin'");
      $stmt->execute();
      $log = $stmt->fetchColumn();
      return $log;
}

  function loginbyuid($id, $db){
    $log;
    try{
        $stmt_select=$db->prepare("select login from person_LOGIN where id=?");
        $stmt_select->execute([$id]);
        $log = $stmt_select->fetchColumn();
    }
    catch (PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $log;
}

function getuid($login, $db){
    $uid;
    try{
        $stmt_select=$db->prepare("select id from person_LOGIN where login=?");
        $stmt_select->execute([$login]);
        $uid = $stmt_select->fetchColumn();
    }
    catch (PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $uid;
}

  function update($user_id,$name,$number,$email,$bdate,$gen,$bio,$check,$lang){
    global $db;
    try {
        $stmt_update = $db->prepare("UPDATE application SET name=?, number=?, email=?, bdate=?, gender=?, biography=?, checkbox=? WHERE id=?");
        $stmt_update->execute([$name, $number, $email, $bdate, $gen,$bio, $check, $user_id ]);
    
        $stmt_delete = $db->prepare("DELETE FROM prog_lang WHERE id=?");
        $stmt_delete -> execute([$user_id]);
        $stmt_select = $db->prepare("SELECT id_lang_name FROM prog WHERE lang_name = ?");
        $insert_stmt = $db->prepare("INSERT INTO prog_lang (id, id_lang_name) VALUES (?,?)");
        foreach ($lang as $language) {
          // Получаем ID языка программирования
          $stmt_select->execute([$language]);
          $language_id = $stmt_select->fetchColumn();
          
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

function insert($login, $hash_p, $db){
    global $db;
    try {
        $stmt = $db->prepare("INSERT INTO application(name, number, email, gender, bdate, biography, checkbox) values(?,?,?,?,?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['number'], $_POST['email'], $_POST['gender'], $_POST['bdate'], $_POST['biography'], isset($_POST["checkbox"]) ? 1 : 0]);
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
      $user_id = $db->lastInsertId();
      try{
        $stmt = $db->prepare("SELECT id_lang_name FROM prog WHERE lang_name = ?");
        $insert_stmt = $db->prepare("INSERT INTO prog_lang (id, id_lang_name) VALUES (?, ?)");
        $fav_languages = $_POST['languages'] ?? [];
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
      }
      try{
        $stmt = $db->prepare("INSERT INTO LOGIN (login, pass) VALUES (:login, :pass)");
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':pass', $hash_p);
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
}

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
function generateCsrfToken() {
  if (function_exists('random_bytes')) {
    $token = bin2hex(random_bytes(32));
  } else {
    $token = md5(uniqid(rand(), true)); 
  }
  $_SESSION['csrf_token'] = $token;
  $_SESSION['csrf_token_time'] = time();
  return $token;
}

function validateCsrfToken() {
  if (empty($_POST['csrf_token'])) {
    return false; 
  }

  if (empty($_SESSION['csrf_token'])) {
    return false; 
  }

  if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    return false; 
  }

  $token_age = time() - $_SESSION['csrf_token_time'];
  if ($token_age > 3600) {
    return false; 
  }

  unset($_SESSION['csrf_token']); 
  unset($_SESSION['csrf_token_time']);

  return true;
}
