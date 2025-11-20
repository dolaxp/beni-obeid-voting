<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعداد النظام</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            text-align: right;
            background: linear-gradient(135deg, #ce1126 0%, #ce1126 33%, #ffffff 33%, #ffffff 66%, #000000 66%, #000000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .setup-container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        
        h1 {
            color: #000;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 1em;
            font-family: inherit;
        }
        
        input:focus {
            outline: none;
            border-color: #ce1126;
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ce1126, #000000);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .success {
            background-color: #d1fae5;
            border: 2px solid #10b981;
            color: #047857;
        }
        
        .error {
            background-color: #fee2e2;
            border: 2px solid #ef4444;
            color: #991b1b;
        }
        
        .info {
            background-color: #dbeafe;
            border: 2px solid #3b82f6;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>🔧 إعداد نظام التصويت</h1>
        
        <?php
        require_once 'includes/config.php';
        require_once 'includes/db.php';
        require_once 'includes/helpers.php';

        $message = '';
        $messageType = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db_host = sanitize($_POST['db_host'] ?? 'localhost');
            $db_user = sanitize($_POST['db_user'] ?? 'root');
            $db_pass = sanitize($_POST['db_pass'] ?? '');
            $db_name = sanitize($_POST['db_name'] ?? 'voting_system');

            // اختبار الاتصال
            $test_conn = new mysqli($db_host, $db_user, $db_pass);

            if ($test_conn->connect_error) {
                $message = 'خطأ في الاتصال بقاعدة البيانات: ' . $test_conn->connect_error;
                $messageType = 'error';
            } else {
                // إنشاء قاعدة البيانات
                $test_conn->query("CREATE DATABASE IF NOT EXISTS `$db_name`");
                $test_conn->select_db($db_name);

                // إنشاء الجداول
                if (initializeDatabase($test_conn)) {
                    seedCandidates($test_conn);
                    $message = '✅ تم إعداد النظام بنجاح! يمكنك الآن الدخول إلى <a href="index.php" style="color: inherit; text-decoration: underline;">الصفحة الرئيسية</a>';
                    $messageType = 'success';
                } else {
                    $message = 'خطأ في إنشاء الجداول';
                    $messageType = 'error';
                }

                $test_conn->close();
            }
        }

        if ($message) {
            echo "<div class='message $messageType'>$message</div>";
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="db_host">عنوان قاعدة البيانات:</label>
                <input type="text" id="db_host" name="db_host" value="localhost" required>
            </div>

            <div class="form-group">
                <label for="db_user">اسم المستخدم:</label>
                <input type="text" id="db_user" name="db_user" value="root" required>
            </div>

            <div class="form-group">
                <label for="db_pass">كلمة المرور:</label>
                <input type="password" id="db_pass" name="db_pass">
            </div>

            <div class="form-group">
                <label for="db_name">اسم قاعدة البيانات:</label>
                <input type="text" id="db_name" name="db_name" value="voting_system" required>
            </div>

            <button type="submit">إعداد النظام</button>
        </form>

        <div class="message info" style="margin-top: 20px;">
            ⚠️ ملاحظة: احذف ملف setup.php بعد الإعداد لأسباب أمنية
        </div>
    </div>
</body>
</html>
