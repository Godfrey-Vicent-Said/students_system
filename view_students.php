<?php
// 1. Ulinzi: Anza session na hakikisha ni admin pekee anayeweza kuona
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Unganisha na database
require_once 'Database.php';
$db = (new Database())->connect();

try {
    // 3. Query iliyorekebishwa (tumeondoa u.email ili isilete error)
    $query = "SELECT u.username, c.course_name 
              FROM users u 
              LEFT JOIN student_courses sc ON u.id = sc.user_id 
              LEFT JOIN courses c ON sc.course_id = c.id 
              WHERE u.role = 'student'";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orodha ya Wanafunzi | Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Wanafunzi Waliosajiliwa</h2>
        
        <table class="w-full text-left border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="p-3 border">Username</th>
                    <th class="p-3 border">Kozi Aliyosajili</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach($students as $s): ?>
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="p-3 border"><?php echo htmlspecialchars($s['username']); ?></td>
                        <td class="p-3 border"><?php echo htmlspecialchars($s['course_name'] ?? 'Hajasajili kozi'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2" class="p-4 text-center text-gray-500">Hakuna wanafunzi waliosajiliwa kwa sasa.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="mt-6">
            <a href="admin_dashboard.php" class="bg-slate-700 text-white px-6 py-2 rounded hover:bg-slate-800 transition">← Rudi Dashboard</a>
        </div>
    </div>
</body>
</html>