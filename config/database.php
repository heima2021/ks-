<?php
/**
 * 数据库配置文件
 */

// 数据库配置
define('DB_HOST', 'localhost');      // 数据库地址
define('DB_PORT', '3306');           // 数据库端口
define('DB_NAME', 'heima');  // 数据库名称
define('DB_USER', 'heima');           // 数据库用户名
define('DB_PASS', 'heima');               // 数据库密码
define('DB_CHARSET', 'utf8mb4');     // 字符集

// 系统配置
define('CARD_KEY_LENGTH', 16);       // 卡密长度
define('CARD_KEY_PREFIX', 'KS');     // 卡密前缀
define('ADMIN_PASSWORD', '123456');  // 管理员密码（请修改）

/**
 * 获取数据库连接
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => true,  // 启用模拟模式，所有值作为字符串处理，避免类型推断问题
                PDO::ATTR_PERSISTENT => false,  // 禁用持久连接，避免连接问题
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,  // 设置字符集
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            die(json_encode([
                'success' => false,
                'message' => '数据库连接失败: ' . $e->getMessage()
            ]));
        }
    }
    
    return $pdo;
}

/**
 * 执行SQL查询
 */
function query($sql, $params = []) {
    $pdo = getDB();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * 获取单条记录
 */
function fetchOne($sql, $params = []) {
    return query($sql, $params)->fetch();
}

/**
 * 获取多条记录
 */
function fetchAll($sql, $params = []) {
    return query($sql, $params)->fetchAll();
}

/**
 * 插入数据并返回ID（自动添加时间字段）
 */
function insert($table, $data) {
    // 自动添加create_time和update_time（如果字段为空）
    if (!isset($data['create_time'])) {
        $data['create_time'] = date('Y-m-d H:i:s');
    }
    if (!isset($data['update_time'])) {
        $data['update_time'] = date('Y-m-d H:i:s');
    }
    // 对于created_at字段
    if (!isset($data['created_at']) && array_key_exists('created_at', $data)) {
        $data['created_at'] = date('Y-m-d H:i:s');
    }
    
    $keys = array_keys($data);
    $fields = '`' . implode('`,`', $keys) . '`';
    $placeholders = implode(',', array_fill(0, count($keys), '?'));
    
    $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
    
    // 使用位置参数而不是命名参数
    $values = array_values($data);
    query($sql, $values);
    
    return getDB()->lastInsertId();
}

/**
 * 更新数据（自动更新update_time）
 */
function update($table, $data, $where, $whereParams = []) {
    // 自动更新update_time
    if (!isset($data['update_time'])) {
        $data['update_time'] = date('Y-m-d H:i:s');
    }
    
    $set = [];
    $params = [];
    
    // 构建SET子句，使用位置参数
    foreach ($data as $key => $value) {
        $set[] = "`{$key}`=?";
        $params[] = $value;
    }
    $setStr = implode(',', $set);
    
    // 合并WHERE子句的参数
    if (is_array($whereParams)) {
        $params = array_merge($params, $whereParams);
    }
    
    $sql = "UPDATE {$table} SET {$setStr} WHERE {$where}";
    
    return query($sql, $params)->rowCount();
}

/**
 * 删除数据
 */
function delete($table, $where, $params = []) {
    $sql = "DELETE FROM {$table} WHERE {$where}";
    return query($sql, $params)->rowCount();
}

/**
 * 生成卡密
 */
function generateCardKey() {
    $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $key = CARD_KEY_PREFIX;
    
    for ($i = 0; $i < CARD_KEY_LENGTH; $i++) {
        $key .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    // 每4位添加一个连字符，去除末尾连字符
    return trim(chunk_split($key, 4, '-'), '-');
}

/**
 * 验证管理员密码
 */
function verifyAdminPassword($password) {
    // 尝试从数据库读取管理员密码
    try {
        $config = fetchOne("SELECT value FROM config WHERE `key`='admin_password'");
        if ($config) {
            return $password === $config['value'];
        }
    } catch (Exception $e) {
        // 数据库查询失败，使用默认密码
    }
    
    // 如果数据库中没有配置，使用默认密码
    return $password === ADMIN_PASSWORD;
}

/**
 * 添加日志到数据库（增强版）
 * 直接使用PDO，避免依赖其他函数
 */
if (!function_exists('addLog')) {
    function addLog($message, $type = 'info', $accountId = null, $userId = null) {
        try {
            $pdo = getDB();
            $sql = "INSERT INTO execution_logs (user_id, account_id, log_type, message, create_time) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$userId, $accountId, $type, $message]);
            return true;
        } catch (Exception $e) {
            // 静默失败
            return false;
        }
    }
}
