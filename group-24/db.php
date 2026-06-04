<?php
class DB {
    public static function connect() {
        if ($_SERVER['HTTP_HOST'] === 'localhost' || str_starts_with($_SERVER['HTTP_HOST'], 'localhost:')) {
    $db = new PDO("mysql:host=localhost;dbname=andiks_desserts;charset=utf8mb4", "root", "");
} elseif (str_contains($_SERVER['HTTP_HOST'], 'infinityfree') || str_contains($_SERVER['HTTP_HOST'], 'epizy') || str_contains($_SERVER['HTTP_HOST'], 'infinityfreeapp')) {
    $db = new PDO('mysql:host=sql306.infinityfree.com;dbname=if0_41989340_andiks_desserts;charset=utf8mb4', 'if0_41989340', 'jgwbK3TOo7xn');
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