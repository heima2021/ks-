<?php
// 检查是否已安装
require_once dirname(__DIR__) . '/config/check_install.php';

session_start();

// 自动跳转到新版（如需使用旧版，在URL中添加 ?old=1）
if(!isset($_GET['old'])){
    header('Location: main.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ks极速版自动任务控制台 - 经典版</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Microsoft YaHei', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .disclaimer {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
        }

        .disclaimer h3 {
            color: #856404;
            margin-bottom: 10px;
        }

        .disclaimer ul {
            list-style: none;
            color: #856404;
        }

        .disclaimer li {
            padding: 5px 0;
            padding-left: 20px;
            position: relative;
        }

        .disclaimer li:before {
            content: "⚠️";
            position: absolute;
            left: 0;
        }

        .advertisement {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 20px;
            margin: 20px;
            border-radius: 5px;
        }

        .advertisement h3 {
            color: #0c5460;
            margin-bottom: 10px;
        }

        .advertisement ul {
            list-style: none;
            color: #0c5460;
        }

        .advertisement li {
            padding: 5px 0;
            padding-left: 20px;
            position: relative;
        }

        .advertisement li:before {
            content: "✨";
            position: absolute;
            left: 0;
        }

        .content {
            padding: 30px;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid #e0e0e0;
            margin-bottom: 30px;
        }

        .tab {
            padding: 15px 30px;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 16px;
            color: #666;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
        }

        .tab.active {
            color: #667eea;
            border-bottom-color: #667eea;
        }

        .tab:hover {
            background: #f5f5f5;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            min-height: 150px;
            font-family: 'Consolas', monospace;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            margin-right: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .account-list {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }

        .account-item {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 4px solid #667eea;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .account-info {
            flex: 1;
        }

        .account-actions button {
            margin-left: 10px;
        }

        .log-container {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 5px;
            font-family: 'Consolas', monospace;
            font-size: 13px;
            max-height: 500px;
            overflow-y: auto;
        }

        .log-line {
            padding: 2px 0;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-running {
            background: #28a745;
            color: white;
        }

        .status-stopped {
            background: #6c757d;
            color: white;
        }

        .status-error {
            background: #dc3545;
            color: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 14px;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .stat-card .value {
            font-size: 32px;
            font-weight: bold;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.active {
            display: block;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            display: none;
        }

        .alert.active {
            display: block;
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 ks极速版自动任务控制台 🎉</h1>
            <p>安全稳定 · 高效收益 · 尊贵体验</p>
            <p>交流Q群：<a href="https://qm.qq.com/q/9nkfaEoTGE" target="_blank" style="color: #fff; text-decoration: underline;">立即直达</a> | <a href="cardkey.php" style="color: #fff; text-decoration: underline;">🔐 卡密管理</a></p>
        </div>

        <!-- 免责声明 -->
        <div class="disclaimer">
            <h3>【免责声明】</h3>
            <ul>
                <li>本脚本仅用于学习研究，禁止用于商业用途</li>
                <li>不能保证其合法性、准确性、完整性和有效性</li>
                <li>使用者需根据情况自行判断，风险自负</li>
                <li>禁止在闲鱼/公众号等平台发布或用于收费/获利</li>
                <li>您必须在下载后的24小时内从计算机或手机中完全删除</li>
                <li><strong>如您付费购买，那你就被当成韭菜了！</strong></li>
            </ul>
        </div>

        <!-- 广告 -->
        <div class="advertisement" id="advertisement">
            <h3>【项目推荐】</h3>
            <p>🔥 最新HID蓝牙打金采用ESP-32芯片</p>
            <ul>
                <li>脚本采用Auto.js编写，多年开发经验</li>
                <li>去除抓包难题，仅需一台安卓设备即可</li>
                <li>不需要开启无障碍，不需要root</li>
                <li>降低官方检测，模拟真人用户点击</li>
                <li>适配多种项目，内部免费提供+教程</li>
            </ul>
            <p><strong>📞 需要请加Q群：<a href="https://qm.qq.com/q/9nkfaEoTGE" target="_blank" style="color: #667eea; text-decoration: underline;">立即直达</a></strong></p>
        </div>

        <div class="content">
            <!-- 统计卡片 -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>总账号数</h3>
                    <div class="value" id="totalAccounts">0</div>
                </div>
                <div class="stat-card">
                    <h3>运行状态</h3>
                    <div class="value" style="font-size: 20px;">
                        <span class="status-badge status-stopped" id="statusBadge">已停止</span>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>总金币收益</h3>
                    <div class="value" id="totalCoins">0</div>
                </div>
                <div class="stat-card">
                    <h3>成功任务数</h3>
                    <div class="value" id="successTasks">0</div>
                </div>
            </div>

            <!-- 标签页 -->
            <div class="tabs">
                <button class="tab active" onclick="switchTab('accounts')">账号管理</button>
                <button class="tab" onclick="switchTab('config')">配置管理</button>
                <button class="tab" onclick="switchTab('execute')">执行任务</button>
                <button class="tab" onclick="switchTab('logs')">运行日志</button>
            </div>

            <!-- 提示框 -->
            <div class="alert alert-success" id="successAlert"></div>
            <div class="alert alert-error" id="errorAlert"></div>

            <!-- 账号管理 -->
            <div class="tab-content active" id="tab-accounts">
                <h2>账号管理</h2>
                <div class="form-group">
                    <label>批量添加账号（每行一个，格式：cookie#salt#代理 或 备注#cookie#salt#代理）</label>
                    <textarea id="accountsInput" placeholder="示例：
备注名#cookie内容#salt值#socks5://user:pass@ip:port
cookie内容#salt值
cookie内容#salt值#socks5://user:pass@ip:port"></textarea>
                </div>
                <button class="btn btn-primary" onclick="addAccounts()">批量添加账号</button>
                <button class="btn btn-danger" onclick="clearAccounts()">清空所有账号</button>

                <div class="account-list" id="accountList">
                    <p style="color: #999; text-align: center;">暂无账号，请添加账号</p>
                </div>
            </div>

            <!-- 配置管理 -->
            <div class="tab-content" id="tab-config">
                <h2>配置管理</h2>
                <div class="form-group">
                    <label>执行轮数 (KSROUNDS)</label>
                    <input type="number" id="ksrounds" value="35" min="1" max="100">
                </div>
                <div class="form-group">
                    <label>金币上限 (KSCOIN_LIMIT)</label>
                    <input type="number" id="coinLimit" value="500000" min="0">
                </div>
                <div class="form-group">
                    <label>低奖励阈值 (KSLOW_REWARD_THRESHOLD)</label>
                    <input type="number" id="lowRewardThreshold" value="10" min="0">
                </div>
                <div class="form-group">
                    <label>连续低奖励上限 (KSLOW_REWARD_LIMIT)</label>
                    <input type="number" id="lowRewardLimit" value="3" min="1">
                </div>
                <div class="form-group">
                    <label>最大并发数 (MAX_CONCURRENCY)</label>
                    <input type="number" id="maxConcurrency" value="888" min="1">
                </div>
                <div class="form-group">
                    <label>执行任务 (多选)</label>
                    <div>
                        <label><input type="checkbox" name="tasks" value="food" checked> 饭补广告</label>
                        <label><input type="checkbox" name="tasks" value="box" checked> 宝箱广告</label>
                        <label><input type="checkbox" name="tasks" value="look" checked> 看广告得金币</label>
                    </div>
                </div>
                <hr style="margin: 30px 0; border: none; border-top: 2px solid #e0e0e0;">
                <h3 style="color: #667eea; margin-bottom: 20px;">📱 消息推送配置（Pushplus）</h3>
                <div class="form-group">
                    <label>启用推送</label>
                    <div>
                        <label><input type="checkbox" id="pushplusEnabled"> 开启消息推送（需要先获取Pushplus Token）</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Pushplus Token <a href="https://www.pushplus.plus/push1.html" target="_blank" style="color: #667eea; font-size: 12px;">(点击获取Token)</a></label>
                    <input type="text" id="pushplusToken" placeholder="请输入您的Pushplus Token">
                    <small style="color: #999; margin-top: 5px; display: block;">
                        推送通知包括：登录成功、任务完成、执行出错等关键信息
                    </small>
                </div>
                <button class="btn btn-success" onclick="saveConfig()">保存配置</button>
            </div>

            <!-- 执行任务 -->
            <div class="tab-content" id="tab-execute">
                <h2>执行任务</h2>
                <p>点击开始按钮后，系统将自动执行所有账号的任务。</p>
                <div style="margin: 30px 0;">
                    <button class="btn btn-success" id="startBtn" onclick="startExecution()">开始执行</button>
                    <button class="btn btn-danger" id="stopBtn" onclick="stopExecution()" style="display: none;">停止执行</button>
                </div>
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <p>正在执行任务...</p>
                </div>
                <div class="log-container" id="logContainer"></div>
            </div>

            <!-- 运行日志 -->
            <div class="tab-content" id="tab-logs">
                <h2>运行日志</h2>
                <button class="btn btn-primary" onclick="refreshLogs()">刷新日志</button>
                <button class="btn btn-danger" onclick="clearLogs()">清空日志</button>
                <div class="log-container" id="historyLogs" style="margin-top: 20px;"></div>
            </div>
        </div>

        <div class="footer">
            <p>ks极速版自动任务控制台 | 本脚本完全免费 | 交流Q群：<a href="https://qm.qq.com/q/9nkfaEoTGE" target="_blank" style="color: #fff; text-decoration: underline;">立即直达</a></p>
            <p style="margin-top: 10px; color: #dc3545; font-weight: bold;">如您付费购买此脚本，请立即申请退款！</p>
        </div>
    </div>

    <script>
        // 标签页切换
        function switchTab(tabName) {
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            event.target.classList.add('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        }

        // 显示提示信息
        function showAlert(message, type = 'success') {
            const alertId = type === 'success' ? 'successAlert' : 'errorAlert';
            const alert = document.getElementById(alertId);
            alert.textContent = message;
            alert.classList.add('active');
            setTimeout(() => {
                alert.classList.remove('active');
            }, 3000);
        }

        // 批量添加账号
        function addAccounts() {
            const input = document.getElementById('accountsInput').value.trim();
            if (!input) {
                showAlert('请输入账号信息', 'error');
                return;
            }

            fetch('../api/api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'addAccounts',
                    accounts: input
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('添加成功！');
                    document.getElementById('accountsInput').value = '';
                    loadAccounts();
                } else {
                    showAlert(data.message || '添加失败', 'error');
                }
            })
            .catch(err => {
                showAlert('操作失败：' + err.message, 'error');
            });
        }

        // 加载账号列表
        function loadAccounts() {
            fetch('../api/api.php?action=getAccounts')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const list = document.getElementById('accountList');
                    if (data.accounts.length === 0) {
                        list.innerHTML = '<p style="color: #999; text-align: center;">暂无账号，请添加账号</p>';
                    } else {
                        list.innerHTML = data.accounts.map((acc, index) => `
                            <div class="account-item">
                                <div class="account-info">
                                    <strong>账号 ${index + 1}</strong>
                                    ${acc.remark ? `<span style="color: #667eea;"> (${acc.remark})</span>` : ''}
                                    <br>
                                    <small style="color: #999;">Cookie: ${acc.cookie.substring(0, 50)}...</small>
                                </div>
                                <div class="account-actions">
                                    <button class="btn btn-danger" style="padding: 5px 15px; font-size: 14px;" 
                                            onclick="deleteAccount(${index})">删除</button>
                                </div>
                            </div>
                        `).join('');
                    }
                    document.getElementById('totalAccounts').textContent = data.accounts.length;
                }
            });
        }

        // 删除账号
        function deleteAccount(index) {
            if (!confirm('确定要删除这个账号吗？')) return;
            
            fetch('../api/api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'deleteAccount',
                    index: index
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('删除成功！');
                    loadAccounts();
                } else {
                    showAlert(data.message || '删除失败', 'error');
                }
            });
        }

        // 清空所有账号
        function clearAccounts() {
            if (!confirm('确定要清空所有账号吗？此操作不可恢复！')) return;
            
            fetch('../api/api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'clearAccounts'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('清空成功！');
                    loadAccounts();
                } else {
                    showAlert(data.message || '清空失败', 'error');
                }
            });
        }

        // 保存配置
        function saveConfig() {
            const tasks = Array.from(document.querySelectorAll('input[name="tasks"]:checked'))
                .map(cb => cb.value);

            const config = {
                ksrounds: document.getElementById('ksrounds').value,
                coinLimit: document.getElementById('coinLimit').value,
                lowRewardThreshold: document.getElementById('lowRewardThreshold').value,
                lowRewardLimit: document.getElementById('lowRewardLimit').value,
                maxConcurrency: document.getElementById('maxConcurrency').value,
                tasks: tasks.join(','),
                pushplusToken: document.getElementById('pushplusToken').value,
                pushplusEnabled: document.getElementById('pushplusEnabled').checked ? '1' : '0'
            };

            fetch('../api/api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'saveConfig',
                    config: config
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('配置保存成功！');
                } else {
                    showAlert(data.message || '保存失败', 'error');
                }
            });
        }

        // 加载配置
        function loadConfig() {
            fetch('../api/api.php?action=getConfig')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.config) {
                    document.getElementById('ksrounds').value = data.config.ksrounds || 35;
                    document.getElementById('coinLimit').value = data.config.coinLimit || 500000;
                    document.getElementById('lowRewardThreshold').value = data.config.lowRewardThreshold || 10;
                    document.getElementById('lowRewardLimit').value = data.config.lowRewardLimit || 3;
                    document.getElementById('maxConcurrency').value = data.config.maxConcurrency || 888;
                    
                    const tasks = (data.config.tasks || 'food,box,look').split(',');
                    document.querySelectorAll('input[name="tasks"]').forEach(cb => {
                        cb.checked = tasks.includes(cb.value);
                    });
                    
                    // 加载pushplus配置
                    document.getElementById('pushplusToken').value = data.config.pushplusToken || '';
                    document.getElementById('pushplusEnabled').checked = data.config.pushplusEnabled == '1';
                }
            });
        }

        // 开始执行
        let eventSource = null;
        
        function startExecution() {
            document.getElementById('startBtn').style.display = 'none';
            document.getElementById('stopBtn').style.display = 'inline-block';
            document.getElementById('loading').classList.add('active');
            document.getElementById('statusBadge').textContent = '运行中';
            document.getElementById('statusBadge').className = 'status-badge status-running';
            document.getElementById('logContainer').innerHTML = '';

            // 使用SSE（Server-Sent Events）获取实时日志
            eventSource = new EventSource('../api/run_real.php');
            
            eventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if (data.log) {
                        const container = document.getElementById('logContainer');
                        const logLine = document.createElement('div');
                        logLine.className = 'log-line';
                        logLine.textContent = '[' + new Date().toLocaleTimeString() + '] ' + data.log;
                        container.appendChild(logLine);
                        container.scrollTop = container.scrollHeight;
                        
                        // 检查是否完成
                        if (data.log === 'TASK_COMPLETE') {
                            setTimeout(() => {
                                stopExecution();
                                showAlert('任务执行完成！');
                            }, 1000);
                        }
                    }
                } catch (e) {
                    // 解析失败
                }
            };
            
            eventSource.onerror = function(error) {
                // 连接错误
                // 不要立即关闭，因为可能只是暂时中断
                setTimeout(() => {
                    if (eventSource && eventSource.readyState === EventSource.CLOSED) {
                        stopExecution();
                        showAlert('连接已断开', 'error');
                    }
                }, 1000);
            };
            
            eventSource.onopen = function() {
                // SSE连接已建立
            };
        }

        // 停止执行
        function stopExecution() {
            // 关闭SSE连接
            if (eventSource) {
                eventSource.close();
                eventSource = null;
            }
            
            // 清除日志轮询（兼容旧版）
            if (logPollInterval) {
                clearInterval(logPollInterval);
                logPollInterval = null;
            }
            
            document.getElementById('startBtn').style.display = 'inline-block';
            document.getElementById('stopBtn').style.display = 'none';
            document.getElementById('loading').classList.remove('active');
            document.getElementById('statusBadge').textContent = '已停止';
            document.getElementById('statusBadge').className = 'status-badge status-stopped';
        }

        // 轮询日志
        let logPollInterval;
        let lastLogCount = 0;
        
        function pollLogs() {
            // 清除旧的轮询
            if (logPollInterval) {
                clearInterval(logPollInterval);
            }
            
            // 立即获取一次
            updateLogs();
            
            // 每500毫秒更新一次（更频繁）
            logPollInterval = setInterval(updateLogs, 500);
        }
        
        function updateLogs() {
            fetch('../api/api.php?action=getLogs&_t=' + Date.now())
            .then(res => res.json())
            .then(data => {
                if (data.success && data.logs) {
                    const container = document.getElementById('logContainer');
                    
                    // 只在日志有变化时更新DOM
                    if (data.logs.length !== lastLogCount) {
                        container.innerHTML = data.logs.map(log => 
                            `<div class="log-line">${escapeHtml(log)}</div>`
                        ).join('');
                        container.scrollTop = container.scrollHeight;
                        lastLogCount = data.logs.length;
                        
                        // 检查是否完成
                        if (data.logs.length > 0 && data.logs[data.logs.length - 1].indexOf('全部完成') !== -1) {
                            setTimeout(() => {
                                stopExecution();
                                showAlert('任务执行完成！');
                            }, 1000);
                        }
                    }
                }
            })
            .catch(err => {
                // 获取失败
            });
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // 刷新日志
        function refreshLogs() {
            fetch('../api/api.php?action=getHistoryLogs')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.logs) {
                    document.getElementById('historyLogs').innerHTML = 
                        data.logs.map(log => `<div class="log-line">${log}</div>`).join('');
                }
            });
        }

        // 清空日志
        function clearLogs() {
            if (!confirm('确定要清空所有日志吗？')) return;
            
            fetch('../api/api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'clearLogs' })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAlert('日志已清空！');
                    document.getElementById('historyLogs').innerHTML = '';
                }
            });
        }

        // 随机显示广告
        function showRandomAd() {
            const ad = document.getElementById('advertisement');
            ad.style.display = 'block';
            
            // 1-3分钟后再次显示
            const randomTime = Math.floor(Math.random() * (180000 - 60000 + 1)) + 60000;
            setTimeout(() => {
                ad.style.display = 'none';
                setTimeout(showRandomAd, randomTime);
            }, 10000); // 显示10秒后隐藏
        }

        // 页面加载时执行
        window.onload = function() {
            loadAccounts();
            loadConfig();
            refreshLogs();
            
            // 启动随机广告
            setTimeout(showRandomAd, Math.floor(Math.random() * (180000 - 60000 + 1)) + 60000);
        };
    </script>
</body>
</html>

