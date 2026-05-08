<?php
class DB {
    public static function connect() {
        $db = new PDO('sqlite:' . __DIR__ . '/Database1.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    }
}
?>