<?php
/**
 * إعدادات قاعدة البيانات
 * Code Elta6ur Tools - النسخة السورية
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u359882181_elta6ur_tools');
define('DB_USER', 'u359882181_codeelta6ur');
define('DB_PASS', 'E7d@?8Uf=');
define('DB_CHARSET', 'utf8mb4');

/**
 * إنشاء اتصال PDO
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('خطأ في الاتصال بقاعدة البيانات: ' . $e->getMessage());
        }
    }
    
    return $pdo;
}
