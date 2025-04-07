<?php
function insertData($login, $db)
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
            $stmt->bindValue(':login', $login, PDO::PARAM_STR);
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
}