<?php
if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' ||
    md5($_SERVER['PHP_AUTH_PW']) != md5('123')) {
  header('HTTP/1.1 401 Unanthorized');
  header('WWW-Authenticate: Basic realm="My site"');
  print('<h1>401 Требуется авторизация</h1>');
  exit();
}
print('Вы успешно авторизовались и видите защищенные паролем данные.');
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
        $query_languages = "SELECT id, id_lang_name FROM prog_lang";
        $stmt_languages = $db->prepare($query_languages);
        $stmt_languages->execute();
        $person_languages = $stmt_languages->fetchAll(PDO::FETCH_ASSOC);
        $languages_by_person = [];
        foreach ($person_languages as $row) {
        $person_id = $row['id'];
        $language_id = $row['id_lang_name'];
        if (!isset($languages_by_person[$person_id])) {
            $languages_by_person[$person_id] = [];
        }
        $languages_by_person[$person_id][] = $language_id;
    }
        ?>

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
                <form method="post" action="">
                <input type="hidden" name="delete_id" value="<?= htmlspecialchars($row['id']) ?>">
                <button type="submit">Удалить</button>
                </form>
                <a href="index.php/?uid=<?= htmlspecialchars($row['id']) ?>">Изменить</a>
            </td>
            </tr>
        <?php endforeach; ?>
        </table>

<?php

    }
        