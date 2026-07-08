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
    <title>CBE Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="min-h-screen flex flex-col">
        <header class="bg-emerald-700 text-white p-6 shadow-lg">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <h1 class="text-2xl font-bold">CBE Student Management System</h1>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg font-semibold transition">Logout</a>
            </div>
        </header>

        <main class="max-w-6xl mx-auto p-6 w-full">
            <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold mb-4 text-emerald-800">Karibu Msimamizi, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                <p class="text-gray-600">Hapa unaweza kusimamia kozi na wanafunzi waliosajiliwa kwenye mfumo.</p>
                
                <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 border rounded-lg bg-emerald-50">
                        <h3 class="font-bold text-emerald-900">Idadi ya Kozi</h3>
                        <p class="text-3xl font-bold text-emerald-700">--</p>
                    </div>
                    <div class="p-4 border rounded-lg bg-blue-50">
                        <h3 class="font-bold text-blue-900">Wanafunzi</h3>
                        <p class="text-3xl font-bold text-blue-700">--</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>