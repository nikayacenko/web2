<?php
/*function insertData($login, $db)
{
    $values = [];

    $fields = [
        'name' => "SELECT name FROM application join person_LOGIN using(id) where login = :login",
        'email' => "SELECT email FROM application join person_LOGIN using(id) where login = :login",
        'number' => "SELECT number FROM application join person_LOGIN using(id) WHERE login = :login",
        'bdate' => "SELECT bdate FROM application join person_LOGIN using(id) WHERE login = :login",
        'gen' => "SELECT gender FROM application join person_LOGIN using(id) WHERE login = :login",
        'biography' => "SELECT biography FROM application join person_LOGIN using(id) WHERE login = :login",
    ];

    try {
        foreach ($fields as $key => $sql) {
            $stmt = $db->prepare($sql);
            if ($stmt === false) {
                error_log("Ошибка подготовки запроса: " . print_r($db->errorInfo(), true));
                throw new Exception("Ошибка подготовки запроса"); 
            }
            $stmt->bindValue(':login', $login, PDO::PARAM_STR);
            $stmt->bindValue(':login', $login, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                error_log("Ошибка выполнения запроса: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Ошибка выполнения запроса"); 
            }
            $stmt->execute();
            $values[$key] = $stmt->fetchColumn();
        }

        // Fetch languages
        $stmt = $db->prepare("SELECT pl.lang_name FROM prog pl JOIN prog_lang ul ON pl.id_lang_name=ul.id_lang_name where ul.id = :uid;");
        $stmt->bindValue(':login', $login, PDO::PARAM_STR); // Use $uid here
        $stmt->execute();
        $lang = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $values['languages'] = implode(",", $lang);


    } catch (Exception $e) {
        error_log('Database Error: ' . $e->getMessage()); 
        throw new Exception("Failed to retrieve user data. Please try again later.");
    }

    return $values;
}*/
function insertData($login, $db) {
    $values = []; // Локальная переменная для хранения данных

    // SQL-запросы и соответствующие ключи в массиве $values
    $queries = [
        'name' => "SELECT fio FROM person JOIN person_LOGIN USING(id) WHERE login = :login",
        'field-email' => "SELECT email FROM person JOIN person_LOGIN USING(id) WHERE login = :login",
        'field-tel' => "SELECT tel FROM person JOIN person_LOGIN USING(id) WHERE login = :login",
        'field-date' => "SELECT bdate FROM person JOIN person_LOGIN USING(id) WHERE login = :login",
        'radio-group-1' => "SELECT gender FROM person JOIN person_LOGIN USING(id) WHERE login = :login",
        'bio' => "SELECT biography FROM person JOIN person_LOGIN USING(id) WHERE login = :login"
    ];
    foreach ($queries as $key => $sql) {
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':login', $login, PDO::PARAM_STR);
            $values[$key] = $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Ошибка при выполнении запроса для ключа " . $key . ": " . $e->getMessage());
            $values[$key] = null;
        }
    }
    $sql = "SELECT lang.namelang
            FROM personlang pl
            JOIN person_LOGIN l ON pl.pers_id = l.id
            JOIN languages lang ON pl.lang_id = lang.id
            WHERE l.login = :login";
    try {
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        $lang = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $values['languages'] = implode(",", $lang);
    } catch (PDOException $e) {
        error_log("Ошибка при получении языков: " . $e->getMessage());
        $values['languages'] = null; 
    }

    return $values;
}