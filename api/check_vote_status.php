<?php
/**
 * API للتحقق من هل المستخدم صوت سابقاً
 */

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// الحصول على بصمة الجهاز من الطلب
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['deviceFingerprint'])) {
    sendJSON(['success' => false, 'message' => 'بصمة الجهاز مفقودة']);
}

$fingerprint = $conn->real_escape_string($data['deviceFingerprint']);

// البحث عن صوت من نفس بصمة الجهاز
$sql = "SELECT id FROM votes WHERE device_fingerprint = '$fingerprint' LIMIT 1";
$result = $conn->query($sql);

$hasVoted = $result && $result->num_rows > 0;

sendJSON([
    'success' => true,
    'hasVoted' => $hasVoted
]);

?>
