<?php
/**
 * ملف الإعدادات والثوابت
 */

// معلومات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'voting_system');

// معلومات الموقع
define('SITE_TITLE', 'نظام التصويت الإلكتروني - بني عبيد');
define('SITE_URL', 'http://localhost'); // غيّر هذا عند النشر

// المرشحون
$candidates = [
    ['id' => 1, 'name' => 'أحمد حفظي', 'title' => 'د'],
    ['id' => 2, 'name' => 'أحمد سامي', 'title' => 'مهندس'],
    ['id' => 3, 'name' => 'أشرف الشبراوي', 'title' => 'أ.'],
    ['id' => 4, 'name' => 'سامح عبد الفتاح', 'title' => 'لواء'],
    ['id' => 5, 'name' => 'مكرم رضوان', 'title' => 'أ.د'],
];

// تفعيل الأخطاء للتطوير (أطفئها في الإنتاج)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// ترميز UTF-8
header('Content-Type: application/json; charset=utf-8');

?>
