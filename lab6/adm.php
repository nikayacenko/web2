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
        <div class="t container-fluid mt-sm-0">
            <div class="table-responsive">
                <div class="table table-bordered">
                    <table border='1' style="width: 100%;">
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
                </div>
            </div>
        </div>


<?php
        try {
            echo "<table class='stat full-width-table'>"; // Добавляем класс для таблицы
            echo "<thead><tr class='nametb px-sm-2 pt-sm-2 pb-sm-2'><td>LANGUAGE</td><td>COUNT</td></tr></thead>";
            echo "<tbody>";
        
            $stmt = $db->prepare("SELECT l.lang_name, COUNT(pl.id) AS cnt
                                   FROM prog_lang pl
                                   JOIN prog l ON pl.id_lang_name = l.id_lang_name
                                   GROUP BY l.lang_name");
            $stmt->execute();
        
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $lang_name = htmlspecialchars($row['lang_name'] ?? '', ENT_QUOTES, 'UTF-8');
                $count = (int) ($row['cnt'] ?? 0);
        
                echo "<tr><td>{$lang_name}</td><td>{$count}</td></tr>";
            }
        
            echo "</tbody>";
            echo "</table>";
        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            echo "<p class='error'>An error occurred while retrieving data. Please try again later.</p>";
        }
    ?>
<?php
    }
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
            if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']) &&  $_SERVER['PHP_AUTH_USER'] == admin_login_check($db) &&
            admin_password_check($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'], $db)){
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
}
        
