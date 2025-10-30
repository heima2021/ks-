-- ks任务系统数据库结构 v2.1
-- 更新时间: 2024-10-25
-- 包含：代理系统、授权系统、卡密增强、个性化设置、API版本号配置
-- 兼容Windows宝塔环境

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- 账号表
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) DEFAULT NULL COMMENT '所属用户ID',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注名称',
  `cookie` text NOT NULL,
  `salt` varchar(100) NOT NULL,
  `proxy` varchar(255) DEFAULT NULL COMMENT '代理地址',
  `push_token` varchar(255) DEFAULT NULL COMMENT '推送Token',
  `push_enabled` tinyint(1) DEFAULT '0' COMMENT '推送开关',
  `status` tinyint(1) DEFAULT '1' COMMENT '1=启用 0=禁用',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_status_update` (`status`, `update_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 配置表
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `value` text,
  `description` varchar(255) DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `config` (`key`, `value`, `description`) VALUES
('admin_username', 'admin', '管理员用户名'),
('admin_password', 'admin123', '管理员密码'),
('sitename', 'ks任务系统', '网站名称'),
('ksrounds', '35', '执行轮数'),
('coinLimit', '500000', '金币上限'),
('lowRewardThreshold', '10', '低奖励阈值'),
('lowRewardLimit', '3', '连续低奖励上限'),
('maxConcurrency', '888', '最大并发数'),
('maxAttempts', '30', '任务尝试次数'),
('tasks', 'food,box,look', '执行任务'),
('apiVersion', '1023-03', 'API版本号（用于快手API请求验证）'),
('interval_seconds', '3', '账号间隔时间（秒）'),
('task_delay_min', '2', '任务延时最小值（秒）'),
('task_delay_max', '5', '任务延时最大值（秒）'),
('pushplusToken', '', '管理员推送Token'),
('pushplusEnabled', '0', '管理员推送开关'),
('withdraw_enabled', '0', '用户提现功能'),
('withdraw_message', '提现功能暂时关闭', '提现关闭提示'),
('user_push_enabled', '0', '用户推送功能'),
('logRetentionDays', '7', '日志保留天数'),
('agent_activated', '0', '代理功能激活状态'),
('card_types', '[{"name":"周卡","days":7,"price":9.9,"enabled":true},{"name":"月卡","days":30,"price":29.9,"enabled":true},{"name":"年卡","days":365,"price":259.9,"enabled":true}]', '卡密类型');

-- 卡密表
DROP TABLE IF EXISTS `card_keys`;
CREATE TABLE `card_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) DEFAULT NULL COMMENT '代理ID',
  `user_id` varchar(50) DEFAULT NULL COMMENT '使用用户ID',
  `card_key` varchar(50) NOT NULL,
  `days` int(11) DEFAULT NULL COMMENT '有效天数',
  `status` tinyint(1) DEFAULT '0' COMMENT '0=未使用 1=已使用 2=已过期',
  `use_time` datetime DEFAULT NULL,
  `expire_time` datetime DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_card_key` (`card_key`),
  KEY `idx_agent_id` (`agent_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_agent_status` (`agent_id`, `status`),
  KEY `idx_status_expire` (`status`, `expire_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 代理用户表
DROP TABLE IF EXISTS `usr`;
CREATE TABLE `usr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acc` varchar(50) NOT NULL COMMENT '账号',
  `pwd` varchar(255) NOT NULL COMMENT '密码',
  `name` varchar(100) DEFAULT NULL COMMENT '姓名',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机号',
  `role` tinyint(1) DEFAULT '0' COMMENT '0=用户 1=管理员 2=代理',
  `stat` tinyint(1) DEFAULT '1' COMMENT '0=禁用 1=正常',
  `balance` decimal(10,2) DEFAULT '0.00' COMMENT '余额',
  `discount` decimal(3,2) DEFAULT '1.00' COMMENT '折扣',
  `permissions` text COMMENT '权限',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间（别名）',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_acc` (`acc`),
  KEY `idx_role` (`role`),
  KEY `idx_stat` (`stat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户代理表';

-- 用户设置表
DROP TABLE IF EXISTS `user_settings`;
CREATE TABLE `user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) NOT NULL,
  `tasks` varchar(100) DEFAULT 'food,box,look',
  `rounds` int(11) DEFAULT 1,
  `coin_limit` int(11) DEFAULT 0,
  `max_attempts` int(11) DEFAULT 30,
  `push_token` varchar(255) DEFAULT NULL,
  `push_enabled` tinyint(1) DEFAULT 0,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 执行日志表
DROP TABLE IF EXISTS `execution_logs`;
CREATE TABLE `execution_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(50) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `log_type` varchar(20) DEFAULT 'info',
  `message` text NOT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_account_id` (`account_id`),
  KEY `idx_create_time` (`create_time`),
  KEY `idx_user_time` (`user_id`, `create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 金币历史表
DROP TABLE IF EXISTS `coin_history`;
CREATE TABLE `coin_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `coin_amount` varchar(20) DEFAULT NULL,
  `change_amount` varchar(20) DEFAULT NULL,
  `record_type` varchar(20) DEFAULT 'task',
  `record_date` date DEFAULT NULL,
  `record_time` datetime DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_account_id` (`account_id`),
  KEY `idx_record_date` (`record_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 每日金币统计表
DROP TABLE IF EXISTS `daily_coin_stats`;
CREATE TABLE `daily_coin_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL COMMENT '账号ID',
  `stat_date` date NOT NULL COMMENT '统计日期',
  `initial_coin` bigint(20) DEFAULT '0' COMMENT '初始金币',
  `final_coin` bigint(20) DEFAULT '0' COMMENT '最终金币',
  `earned_coin` bigint(20) DEFAULT '0' COMMENT '获得金币',
  `task_count` int(11) DEFAULT '0' COMMENT '任务次数',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_account_date` (`account_id`, `stat_date`),
  KEY `idx_stat_date` (`stat_date`),
  KEY `idx_account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='每日金币统计表';

-- 系统设置表
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `card_verify_enabled` tinyint(1) DEFAULT '0' COMMENT '卡密验证开关',
  `current_card_key` varchar(50) DEFAULT NULL COMMENT '当前卡密',
  `card_expire_time` datetime DEFAULT NULL COMMENT '卡密过期时间',
  `last_verify_time` datetime DEFAULT NULL COMMENT '最后验证时间',
  `site_name` varchar(100) DEFAULT 'ks任务系统' COMMENT '网站名称',
  `maintenance_mode` tinyint(1) DEFAULT '0' COMMENT '维护模式',
  `max_accounts_per_user` int(11) DEFAULT '100' COMMENT '每用户最大账号数',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统设置表';

-- 插入系统设置默认数据
INSERT INTO `system_settings` (`id`, `card_verify_enabled`, `maintenance_mode`, `max_accounts_per_user`) 
VALUES (1, 0, 0, 100);

-- 使用记录表
DROP TABLE IF EXISTS `usage_logs`;
CREATE TABLE `usage_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `card_key` varchar(50) DEFAULT NULL COMMENT '卡密',
  `action` varchar(50) DEFAULT NULL COMMENT '操作类型',
  `description` text COMMENT '操作描述',
  `ip_address` varchar(50) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '用户代理',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_card_key` (`card_key`),
  KEY `idx_action` (`action`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='使用记录表';

-- 激活码使用记录表（一码一用）
DROP TABLE IF EXISTS `license_records`;
CREATE TABLE `license_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `license_key` varchar(30) NOT NULL COMMENT '激活码',
  `machine_code` varchar(30) NOT NULL COMMENT '特征码',
  `status` tinyint(1) DEFAULT '1' COMMENT '1=已激活 0=已撤销',
  `activate_time` datetime DEFAULT NULL COMMENT '激活时间',
  `ip_address` varchar(50) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` text COMMENT '用户代理',
  `server_info` text COMMENT '服务器信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_license_key` (`license_key`),
  KEY `idx_machine_code` (`machine_code`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='激活码使用记录表（防止重复激活）';

SET FOREIGN_KEY_CHECKS = 1;

