<?php
$user = 'u68600'; // Заменить на ваш логин uXXXXX
$pass = '8589415'; // Заменить на пароль
$db = new PDO('mysql:host=localhost;dbname=u68600', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
