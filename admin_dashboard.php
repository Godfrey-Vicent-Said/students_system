<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Ulinzi: Kama si admin, kataa moja kwa moja
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'Database.php';
$db = (new Database())->connect();
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | CBE Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-slate-900 text-white p-4 shadow-lg flex justify-between items-center">
        <h1 class="font-bold text-xl">Admin Panel 🛠️</h1>
        <a href="logout.php" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded text-sm transition">Logout</a>
    </nav>

    <div class="max-w-4xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Karibu, Admin!</h2>
        <p class="text-gray-600">Mfumo umekamilika na unafanya kazi kikamilifu.</p>
        
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="p-6 border border-emerald-200 bg-emerald-50 rounded-lg">
                <h3 class="font-bold text-emerald-800">Usimamizi wa Kozi</h3>
                <p class="text-sm text-gray-600 mt-2">Dhibiti kozi zote zilizopo chuoni.</p>
            </div>
            <div class="p-6 border border-blue-200 bg-blue-50 rounded-lg">
                <h3 class="font-bold text-blue-800">Wanafunzi</h3>
                <p class="text-sm text-gray-600 mt-2">Angalia wanafunzi waliojisajili.</p>
            </div>
        </div>
    </div>
</body>
</html>