<?php
/**
 * API للحصول على قائمة المرشحين مع عدد الأصوات
 */

require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

// إنشاء جداول قاعدة البيانات إذا لم تكن موجودة
if (!$conn->query("SHOW TABLES LIKE 'candidates'")) {
    initializeDatabase($conn);
    seedCandidates($conn);
}

// الحصول على المرشحين مع عدد الأصوات
$sql = "SELECT c.id, c.name, c.title, COUNT(v.id) as vote_count 
        FROM candidates c 
        LEFT JOIN votes v ON c.id = v.candidate_id 
        GROUP BY c.id 
        ORDER BY c.id ASC";

$result = $conn->query($sql);

if (!$result) {
    sendJSON(['success' => false, 'message' => 'خطأ في جلب البيانات']);
}

$candidates = [];
while ($row = $result->fetch_assoc()) {
    $candidates[] = [
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'title' => $row['title'],
        'voteCount' => (int)$row['vote_count']
    ];
}

sendJSON([
    'success' => true,
    'candidates' => $candidates
]);

?>
