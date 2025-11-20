<?php
/**
 * ملف دوال المساعدة
 */

/**
 * دالة لإنشاء بصمة الجهاز
 */
function generateDeviceFingerprint() {
    $fingerprint = [
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        'ip' => getClientIP(),
        'timestamp' => date('Y-m-d'),
    ];
    
    $hash = md5(json_encode($fingerprint));
    return 'device-' . $hash;
}

/**
 * دالة للحصول على عنوان IP الحقيقي
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    return trim($ip);
}

/**
 * دالة للتحقق من صحة الإدخال
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * دالة للتحقق من صحة JSON
 */
function isValidJSON($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * دالة لإرسال استجابة JSON
 */
function sendJSON($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

?>
