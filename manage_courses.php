<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
    header("Location: login.php"); exit();
}
require_once 'Database.php';
$db = (new Database())->connect();

// Kuongeza kozi mpya
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $stmt = $db->prepare("INSERT INTO courses (course_code, course_name) VALUES (?, ?)");
    $stmt->execute([strtoupper($_POST['code']), $_POST['name']]);
}

// Kuchota kozi zote
$courses = $db->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Usimamizi wa Kozi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded shadow mb-6">
            <h2 class="text-xl font-bold mb-4">Ongeza Kozi Mpya</h2>
            <form method="POST" class="flex gap-4">
                <input type="text" name="code" placeholder="Code (mfano: BIT)" required class="p-2 border rounded w-1/4">
                <input type="text" name="name" placeholder="Jina la Kozi" required class="p-2 border rounded w-full">
                <button type="submit" name="add_course" class="bg-emerald-600 text-white px-4 py-2 rounded">Hifadhi</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4">Orodha ya Kozi</h2>
            <table class="w-full text-left">
                <tr class="bg-gray-200"><th class="p-2">Code</th><th class="p-2">Jina</th></tr>
                <?php foreach($courses as $c): ?>
                <tr class="border-b">
                    <td class="p-2 font-bold"><?php echo htmlspecialchars($c['course_code']); ?></td>
                    <td class="p-2"><?php echo htmlspecialchars($c['course_name']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <a href="admin_dashboard.php" class="mt-4 inline-block text-blue-600 font-bold">← Rudi Dashboard</a>
    </div>
</body>
</html>