<?php
/**
 * ks极速版自动任务系统 - 入口文件
 * 自动重定向到管理后台
 */

// 检查安装锁文件
$lockFile = __DIR__ . '/data/install.lock';
$configFile = __DIR__ . '/config/database.php';

if(!file_exists($lockFile) || !file_exists($configFile)) {
    // 未安装，显示安装引导页面
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ks任务系统 - 未安装</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Microsoft YaHei', Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .container {
                background: white;
                padding: 60px 40px;
                border-radius: 15px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                text-align: center;
                max-width: 600px;
            }
            h1 {
                color: #667eea;
                font-size: 48px;
                margin-bottom: 20px;
            }
            p {
                color: #666;
                font-size: 18px;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            .btn {
                display: inline-block;
                padding: 15px 40px;
                margin: 10px;
                font-size: 18px;
                text-decoration: none;
                border-radius: 50px;
                transition: all 0.3s;
            }
            .btn-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(102, 126, 234, 0.6);
            }
            .btn-secondary {
                background: white;
                color: #667eea;
                border: 2px solid #667eea;
            }
            .btn-secondary:hover {
                background: #f8f9ff;
            }
            .icon {
                font-size: 72px;
                margin-bottom: 30px;
            }
            .features {
                text-align: left;
                margin: 30px 0;
                padding: 20px;
                background: #f8f9ff;
                border-radius: 10px;
            }
            .features ul {
                list-style: none;
                padding: 0;
            }
            .features li {
                padding: 8px 0;
                color: #555;
            }
            .features li:before {
                content: "✓ ";
                color: #667eea;
                font-weight: bold;
                margin-right: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="icon">🚀</div>
            <h1>ks任务系统</h1>
            <p>感谢您选择本系统！系统尚未安装，请先完成安装流程。</p>
            
            <div class="features">
                <strong style="color: #667eea; font-size: 16px;">系统功能：</strong>
                <ul>
                    <li>多账号批量管理</li>
                    <li>自动任务调度</li>
                    <li>实时日志监控</li>
                    <li>消息推送通知</li>
                    <li>卡密授权系统</li>
                </ul>
            </div>
            
            <div>
                <a href="install/install.php" class="btn btn-primary">🎯 立即安装</a>
                <a href="install/check.php" class="btn btn-secondary">🔍 环境检测</a>
            </div>
            
            <p style="margin-top: 30px; font-size: 14px; color: #999;">
                如有问题，请查看 <a href="install/README.md" style="color: #667eea;">安装说明文档</a>
            </p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// 已安装，显示入口选择页面
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ks任务系统 - 入口选择</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 60px 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 800px;
        }
        
        h1 {
            color: #667eea;
            font-size: 48px;
            margin-bottom: 20px;
        }
        
        .subtitle {
            color: #666;
            font-size: 18px;
            margin-bottom: 50px;
        }
        
        .options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .option-card {
            background: #f8f9ff;
            border-radius: 15px;
            padding: 40px 30px;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
            border: 3px solid transparent;
        }
        
        .option-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            border-color: #667eea;
        }
        
        .option-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .option-title {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .option-desc {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .option-features {
            text-align: left;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        
        .option-features li {
            padding: 5px 0;
            color: #888;
            font-size: 13px;
        }
        
        .option-features li:before {
            content: "✓ ";
            color: #667eea;
            font-weight: bold;
            margin-right: 5px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin-top: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50px;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #999;
            font-size: 14px;
        }
        
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 ks任务系统</h1>
        <p class="subtitle">请选择您要访问的入口</p>
        
        <div class="options">
            <!-- 用户中心 -->
            <a href="user/login.php" class="option-card">
                <div class="option-icon">👥</div>
                <div class="option-title">用户中心</div>
                <div class="option-desc">使用卡密登录，管理账号和执行任务</div>
                <ul class="option-features">
                    <li>卡密登录</li>
                    <li>账号管理</li>
                    <li>任务执行</li>
                    <li>日志查看</li>
                    <li>数据独立</li>
                </ul>
                <div class="btn">进入用户中心</div>
            </a>
            
            <!-- 管理后台 -->
            <a href="admin/index.php" class="option-card">
                <div class="option-icon">⚙️</div>
                <div class="option-title">管理后台</div>
                <div class="option-desc">系统管理员入口，管理卡密和系统配置</div>
                <ul class="option-features">
                    <li>卡密管理</li>
                    <li>系统配置</li>
                    <li>用户管理</li>
                    <li>数据统计</li>
                    <li>全局设置</li>
                </ul>
                <div class="btn">进入管理后台</div>
            </a>
        </div>
        
        <div class="footer">
            <p>💡 <strong>提示：</strong></p>
            <p>普通用户请访问"用户中心"，管理员请访问"管理后台"</p>
            <p style="margin-top: 10px;">技术支持：黑马工作室@法老网络 | <a href="https://qm.qq.com/q/9nkfaEoTGE" target="_blank" style="color: #667eea; text-decoration: underline;">立即直达</a></p>
        </div>
    </div>
</body>
</html>
<?php
exit;
?>

