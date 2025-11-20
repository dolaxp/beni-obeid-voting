<?php
/**
 * API للتصويت
 */

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// الحصول على البيانات من الطلب
$data = json_decode(file_get_contents('php://input'), true);

// التحقق من البيانات المطلوبة
if (!isset($data['candidateId']) || !isset($data['deviceFingerprint'])) {
    sendJSON(['success' => false, 'message' => 'بيانات ناقصة']);
}

$candidateId = (int)$data['candidateId'];
$fingerprint = $conn->real_escape_string($data['deviceFingerprint']);
$ipAddress = getClientIP();
$userAgent = $conn->real_escape_string($_SERVER['HTTP_USER_AGENT'] ?? '');

// التحقق من صحة المرشح
if ($candidateId < 1 || $candidateId > 5) {
    sendJSON(['success' => false, 'message' => 'معرف المرشح غير صحيح']);
}

// التحقق من عدم التصويت سابقاً
$check_sql = "SELECT id FROM votes WHERE device_fingerprint = '$fingerprint' LIMIT 1";
$check_result = $conn->query($check_sql);

if ($check_result && $check_result->num_rows > 0) {
    sendJSON(['success' => false, 'message' => 'لقد قمت بالتصويت مسبقاً من هذا الجهاز']);
}

// إدراج الصوت
$insert_sql = "INSERT INTO votes (candidate_id, device_fingerprint, ip_address, user_agent) 
               VALUES ($candidateId, '$fingerprint', '$ipAddress', '$userAgent')";

if ($conn->query($insert_sql)) {
    sendJSON(['success' => true, 'message' => 'تم تسجيل صوتك بنجاح']);
} else {
    sendJSON(['success' => false, 'message' => 'خطأ في تسجيل الصوت']);
}

?>
