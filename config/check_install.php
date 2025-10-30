<?php
/**
 * 安装状态检测
 * 在所有入口文件中引入此文件，确保系统已安装
 */

function checkInstallation() {
    // 统一使用 data/install.lock 作为安装标记
    $lockFile = dirname(__DIR__) . '/data/install.lock';
    
    // 检查是否已安装
    if (!file_exists($lockFile)) {
        // 获取当前访问的文件路径
        $currentScript = $_SERVER['PHP_SELF'];
        
        // 如果不是安装相关页面，重定向到安装页面
        $allowedPaths = ['/install/', '/api/check.php'];
        $isAllowed = false;
        
        foreach ($allowedPaths as $path) {
            if (strpos($currentScript, $path) !== false) {
                $isAllowed = true;
                break;
            }
        }
        
        if (!$isAllowed) {
            // 计算到install目录的相对路径
            $scriptDir = dirname($_SERVER['SCRIPT_FILENAME']);
            $installDir = dirname(__DIR__) . '/install';
            
            // 简化：直接使用相对路径
            $pathParts = explode('/', trim($currentScript, '/'));
            $depth = count($pathParts) - 1;
            
            if ($depth === 0) {
                // 根目录
                $installPath = 'install/install.php';
            } elseif ($depth === 1) {
                // 一级目录 (admin/, agent/, api/)
                $installPath = '../install/install.php';
            } else {
                // 多级目录
                $installPath = str_repeat('../', $depth) . 'install/install.php';
            }
            
            header('Location: ' . $installPath);
            exit;
        }
    }
    
    return true;
}

// 自动执行检测
checkInstallation();
?>

