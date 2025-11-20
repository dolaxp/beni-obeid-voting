<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام التصويت الإلكتروني - بني عبيد</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>
            <span class="icon">🗳️</span>
            نظام التصويت الإلكتروني
            <span class="icon">🗳️</span>
        </h1>
        <p>انتخابات مجلس النواب - مركز بني عبيد</p>
    </div>

    <!-- Main Container -->
    <div class="container">
        <!-- Alert Messages -->
        <div id="alert" class="alert"></div>

        <!-- Candidates Grid -->
        <div id="candidatesGrid" class="candidates-grid">
            <div style="text-align: center; grid-column: 1/-1; padding: 40px;">
                <div class="spinner"></div>
            </div>
        </div>

        <!-- Vote Button -->
        <div id="voteSection" class="vote-section">
            <button class="vote-btn">تأكيد التصويت</button>
        </div>

        <!-- Results Section -->
        <div class="results-section">
            <h2>ملخص النتائج</h2>
            <div id="results"></div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>© 2025 نظام التصويت الإلكتروني - مركز بني عبيد</p>
        <p>جميع الحقوق محفوظة</p>
    </div>

    <!-- Scripts -->
    <script src="js/app.js"></script>
</body>
</html>
