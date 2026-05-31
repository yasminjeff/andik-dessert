<?php
class DB {
    public static function connect() {
        if ($_SERVER['HTTP_HOST'] === 'localhost' || str_starts_with($_SERVER['HTTP_HOST'], 'localhost:')) {
            $db = new PDO('sqlite:' . __DIR__ . '/Database1.db');
        } else {
            $host = 'zephyr.proxy.rlwy.net';
            $dbname = 'railway';
            $username = 'root';
            $password = 'OdiCtRykjBAYZuuOQDmppeALkvipdYaO';
            $port = '45879';
            $db = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
        }
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
}
?>