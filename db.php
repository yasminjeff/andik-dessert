<?php
class DB {
    public static function connect() {
        if ($_SERVER['HTTP_HOST'] === 'localhost' || str_starts_with($_SERVER['HTTP_HOST'], 'localhost:')) {
            $db = new PDO("mysql:host=localhost;dbname=andiks_desserts;charset=utf8mb4", "root", "");
        } else {
            $host = 'localhost';
            $dbname = 'u141697790_db_ls8mfL7Y';
            $username = 'u141697790_usr_ls8mfL7Y';
            $password = 'Andik_2026';
            $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        }
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("SET time_zone = '+08:00'");
        return $db;
    }
}
?>