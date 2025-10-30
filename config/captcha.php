<?php
/**
 * 验证码生成和验证
 */

/**
 * 验证验证码
 * @param string $code 用户输入的验证码
 * @return array ['ok' => bool, 'msg' => string]
 */
function verifyCaptcha($code) {
    // 如果session未启动，先启动
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // 检查验证码是否为空
    if (empty($code)) {
        return ['ok' => false, 'msg' => '请输入验证码'];
    }
    
    // 检查session中是否有验证码
    if (!isset($_SESSION['captcha'])) {
        return ['ok' => false, 'msg' => '验证码已过期，请刷新'];
    }
    
    // 验证码比对（不区分大小写）
    if (strtoupper($code) !== strtoupper($_SESSION['captcha'])) {
        // 验证失败后清除验证码
        unset($_SESSION['captcha']);
        return ['ok' => false, 'msg' => '验证码错误'];
    }
    
    // 验证成功后清除验证码（一次性使用）
    unset($_SESSION['captcha']);
    return ['ok' => true, 'msg' => '验证成功'];
}

// 只有直接访问此文件时才生成验证码图片
// 通过检查当前执行的脚本文件名来判断
if (basename($_SERVER['SCRIPT_FILENAME']) == 'captcha.php') {
    session_start();

    // 生成随机验证码
    $code = '';
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    for ($i = 0; $i < 4; $i++) {
        $code .= $chars[mt_rand(0, strlen($chars) - 1)];
    }

    // 保存到Session
    $_SESSION['captcha'] = $code;

    // 创建图片
    $width = 120;
    $height = 40;
    $image = imagecreatetruecolor($width, $height);

    // 颜色
    $bgColor = imagecolorallocate($image, 255, 255, 255);
    $textColor = imagecolorallocate($image, 0, 0, 0);
    $lineColor = imagecolorallocate($image, 200, 200, 200);

    // 填充背景
    imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

    // 添加干扰线
    for ($i = 0; $i < 3; $i++) {
        imageline($image, mt_rand(0, $width), mt_rand(0, $height), 
                  mt_rand(0, $width), mt_rand(0, $height), $lineColor);
    }

    // 添加验证码文字
    for ($i = 0; $i < 4; $i++) {
        $x = ($width / 4) * $i + mt_rand(5, 15);
        $y = mt_rand(5, 15);
        imagestring($image, 5, $x, $y, $code[$i], $textColor);
    }

    // 输出图片
    header('Content-Type: image/png');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    imagepng($image);
    imagedestroy($image);
    exit; // 生成图片后立即退出
}
