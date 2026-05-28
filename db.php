<?php
class DB {
    public static function connect() {
        if ($_SERVER['HTTP_HOST'] === 'localhost' || str_starts_with($_SERVER['HTTP_HOST'], 'localhost:')) {
            $db = new PDO("mysql:host=localhost;dbname=andiks_desserts;charset=utf8mb4", "root", "");
        } else {
            $host = 'sql306.infinityfree.com';
            $dbname = 'if0_41989340_andiks_desserts';
            $username = 'if0_41989340';
            $password = 'jgwbK3TOo7xn';
            $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        }
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
}
?>