<?php
/**
 * ملف الاتصال بقاعدة البيانات
 */

require_once 'config.php';

// إنشاء الاتصال
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// التحقق من الاتصال
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'خطأ في الاتصال بقاعدة البيانات']));
}

// تعيين الترميز
$conn->set_charset("utf8mb4");

/**
 * دالة لإنشاء جداول قاعدة البيانات
 */
function initializeDatabase($conn) {
    // جدول المرشحين
    $sql_candidates = "CREATE TABLE IF NOT EXISTS candidates (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        title VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (!$conn->query($sql_candidates)) {
        return false;
    }
    
    // جدول الأصوات
    $sql_votes = "CREATE TABLE IF NOT EXISTS votes (
        id INT PRIMARY KEY AUTO_INCREMENT,
        candidate_id INT NOT NULL,
        device_fingerprint VARCHAR(255) NOT NULL UNIQUE,
        ip_address VARCHAR(45),
        user_agent TEXT,
        voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (candidate_id) REFERENCES candidates(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if (!$conn->query($sql_votes)) {
        return false;
    }
    
    return true;
}

/**
 * دالة لإضافة المرشحين الأوليين
 */
function seedCandidates($conn) {
    global $candidates;
    
    // حذف البيانات القديمة
    $conn->query("DELETE FROM votes");
    $conn->query("DELETE FROM candidates");
    
    // إضافة المرشحين
    foreach ($candidates as $candidate) {
        $name = $conn->real_escape_string($candidate['name']);
        $title = $conn->real_escape_string($candidate['title']);
        
        $sql = "INSERT INTO candidates (name, title) VALUES ('$name', '$title')";
        $conn->query($sql);
    }
    
    // إضافة صوت واحد لكل مرشح
    for ($i = 1; $i <= count($candidates); $i++) {
        $fingerprint = "initial-vote-$i-" . time();
        $sql = "INSERT INTO votes (candidate_id, device_fingerprint, user_agent) 
                VALUES ($i, '$fingerprint', 'System Initialization')";
        $conn->query($sql);
    }
}

?>
