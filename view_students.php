<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || trim($_SESSION['role']) !== 'admin') {
    header("Location: login.php"); exit();
}
require_once 'Database.php';
$db = (new Database())->connect();

// Tunachota taarifa za wanafunzi
$query = "SELECT u.username, u.email, c.course_name 
          FROM users u 
          LEFT JOIN student_courses sc ON u.id = sc.user_id 
          LEFT JOIN courses c ON sc.course_id = c.id 
          WHERE u.role = 'student'";
$stmt = $db->prepare($query);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Orodha ya Wanafunzi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Wanafunzi Waliosajiliwa</h2>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">Username</th>
                    <th class="p-2 border">Email</th>
                    <th class="p-2 border">Kozi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($students as $s): ?>
                <tr>
                    <td class="p-2 border"><?php echo htmlspecialchars($s['username']); ?></td>
                    <td class="p-2 border"><?php echo htmlspecialchars($s['email']); ?></td>
                    <td class="p-2 border"><?php echo $s['course_name'] ?? 'Hajasajili kozi'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="admin_dashboard.php" class="mt-4 inline-block text-blue-600 font-bold">← Rudi Nyuma</a>
    </div>
</body>
</html>