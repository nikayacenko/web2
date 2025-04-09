<?php
require_once 'function.php';
require_once 'db.php';

if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != admin_login_check($db) ||
    !admin_password_check($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $db)) {
  header('HTTP/1.1 401 Unanthorized');
  header('WWW-Authenticate: Basic realm="My site"');
  print('<h1>401 Требуется авторизация</h1>');
  exit();
}

print('Вы успешно авторизовались и видите защищенные паролем данные.');
print("PHP_AUTH_USER: " . (!empty($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : "empty"));
print("PHP_AUTH_PW: " . (!empty($_SERVER['PHP_AUTH_PW']) ? "set" : "empty"));
$login_check_result = admin_login_check($db);
print("admin_login_check(): " . $login_check_result);
$password_check_result = admin_password_check($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $db);
print("admin_password_check(): " . ($password_check_result ? "true" : "false"));
$user = 'u68600'; // Заменить на ваш логин uXXXXX
$pass = '8589415'; // Заменить на пароль
$db = new PDO('mysql:host=localhost;dbname=u68600', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    if($_SERVER['REQUEST_METHOD'] == 'GET')
    {
        
        $query = "SELECT id, name, number, email, bdate, gender, biography FROM application"; // Запрос с параметром

        $stmt = $db->prepare($query); // Подготавливаем запрос
        $stmt->execute();// Выполняем запрос с параметром
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $query_languages = "SELECT
                            pl.id,
                            l.lang_name
                        FROM
                            prog_lang pl
                        JOIN
                            prog l ON pl.id_lang_name = l.id_lang_name";
    $stmt_languages = $db->prepare($query_languages);
    $stmt_languages->execute();
    $person_languages = $stmt_languages->fetchAll(PDO::FETCH_ASSOC);
    // 2. Группируем данные в PHP
    $languages_by_person = [];
    foreach ($person_languages as $row) {
        $person_id = $row['id'];
        $language_name = $row['lang_name']; // Используем language_name
        if (!isset($languages_by_person[$person_id])) {
            $languages_by_person[$person_id] = [];
        }
        $languages_by_person[$person_id][] = $language_name; // Добавляем название языка
    }
    include 'tablehtml.php';
    ?>
        <div class="t">
        <table border='1'>
        <tr>
            <th>ID</th>
            <th>FIO</th>
            <th>Tel</th>
            <th>Email</th>
            <th>Bdate</th>
            <th>Gender</th>
            <th>Biography</th>
            <th>Languages</th>
            <th>Действия</th>
        </tr>

        <?php foreach ($results as $row): ?>
            <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['number']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['bdate']) ?></td>
            <td><?= htmlspecialchars($row['gender']) ?></td>
            <td><?= htmlspecialchars($row['biography']) ?></td>
            <td>
            <?php
                // 3. Используем implode для объединения языков
                $person_id = $row['id'];
                if (isset($languages_by_person[$person_id])) {
                    $languages_string = implode(', ', $languages_by_person[$person_id]);
                    echo htmlspecialchars($languages_string);
                } else {
                    echo "Нет данных";
                }
                ?>
                </td>
                <td>
                <form method="post" action="">
                <input type="hidden" name="delete_id" value="<?= htmlspecialchars($row['id']) ?>">
                <button type="submit">Удалить</button>
                </form>
                <a href="index.php?uid=<?= htmlspecialchars($row['id']) ?>">Изменить</a>
            </td>
            </tr>
        <?php endforeach; ?>
        </table>

<?php

    }
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id']) && !empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
        $delete_id = $_POST['delete_id'];
        $delete_query = "DELETE FROM application WHERE id = :id";
        $delete_querylang="DELETE FROM prog_lang WHERE id=:id";
        $delete_querylogin="DELETE FROM person_LOGIN WHERE id=:id";
        $addition_query="SELECT login FROM person_LOGIN WHERE id=:id";
        $delete_LOGIN="DELETE FROM LOGIN WHERE login=:login";
        try {
            $delete_stmt = $db->prepare($addition_query);
            $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
            $delete_stmt->execute();
            $doplog=$delete_stmt->fetchColumn();
            $delete_stmt = $db->prepare($delete_querylogin);
            $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT); 
            $delete_stmt->execute();
            $delete_stmt = $db->prepare($delete_LOGIN);
            $delete_stmt->bindParam(':login', $doplog, PDO::PARAM_STR); 
            $delete_stmt->execute();
            $delete_stmt = $db->prepare($delete_querylang);
            $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT); 
            $delete_stmt->execute();
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
            $delete_stmt->execute();

            header("Location: adm.php");
            exit;
        
            } catch (PDOException $e) {
            echo "<p style='color: red;'>Ошибка удаления: " . $e->getMessage() . "</p>";
            }
}
        
