<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
    header("Location: login.php"); exit();
}
require_once 'Database.php';
$db = (new Database())->connect();

// Chota idadi ya wanafunzi waliosajiliwa
$stmt = $db->query("SELECT COUNT(*) FROM student_courses");
$student_count = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-slate-900 text-white p-4 flex justify-between items-center">
        <h1 class="font-bold text-xl">Admin Panel 🛠️</h1>
        <a href="logout.php" class="bg-red-600 px-4 py-2 rounded text-sm">Logout</a>
    </nav>

    <div class="max-w-5xl mx-auto mt-10 p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="manage_courses.php" class="p-6 bg-white border-l-4 border-emerald-500 shadow hover:shadow-lg transition">
            <h3 class="font-bold text-emerald-800 text-lg">Usimamizi wa Kozi</h3>
            <p class="text-sm text-gray-600">Ongeza, hariri au futa kozi za chuo.</p>
        </a>
        <a href="view_students.php" class="p-6 bg-white border-l-4 border-blue-500 shadow hover:shadow-lg transition">
            <h3 class="font-bold text-blue-800 text-lg">Wanafunzi (<?php echo $student_count; ?>)</h3>
            <p class="text-sm text-gray-600">Angalia orodha ya wanafunzi waliojisajili.</p>
        </a>
    </div>
</body>
</html>