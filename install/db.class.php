<?php
/**
 * 数据库操作类（用于安装程序）
 * 提供基础的数据库连接和操作功能
 */
class DB {
    private static $instance = null;
    private static $connection = null;
    private static $last_error = '';
    private static $last_errno = 0;
    
    /**
     * 连接数据库
     */
    public static function connect($host, $user, $pass, $dbname, $port = 3306) {
        try {
            // 先尝试不指定数据库连接，检测服务器是否可达
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // 检查数据库是否存在
            $result = $pdo->query("SHOW DATABASES LIKE '{$dbname}'");
            if ($result->rowCount() === 0) {
                // 数据库不存在，尝试创建
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            }
            
            // 连接到指定的数据库
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
            self::$connection = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            return self::$connection;
            
        } catch (PDOException $e) {
            self::$last_error = $e->getMessage();
            self::$last_errno = $e->getCode();
            return false;
        }
    }
    
    /**
     * 获取连接错误号
     */
    public static function connect_errno() {
        return self::$last_errno;
    }
    
    /**
     * 获取连接错误信息
     */
    public static function connect_error() {
        return self::$last_error;
    }
    
    /**
     * 执行SQL查询
     */
    public static function query($sql) {
        if (!self::$connection) {
            return false;
        }
        
        try {
            $result = self::$connection->query($sql);
            return $result;
        } catch (PDOException $e) {
            self::$last_error = $e->getMessage();
            self::$last_errno = $e->getCode();
            return false;
        }
    }
    
    /**
     * 获取最后一次错误信息
     */
    public static function error() {
        return self::$last_error;
    }
    
    /**
     * 获取最后一次错误号
     */
    public static function errno() {
        return self::$last_errno;
    }
    
    /**
     * 获取查询结果
     */
    public static function fetch($result) {
        if ($result instanceof PDOStatement) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    
    /**
     * 获取所有查询结果
     */
    public static function fetch_all($result) {
        if ($result instanceof PDOStatement) {
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }
    
    /**
     * 获取影响行数
     */
    public static function affected_rows($result) {
        if ($result instanceof PDOStatement) {
            return $result->rowCount();
        }
        return 0;
    }
    
    /**
     * 关闭连接
     */
    public static function close() {
        self::$connection = null;
    }
}
?>

