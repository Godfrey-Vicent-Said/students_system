<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kuzuia watu wasioingia kwenye mfumo au ma-admin wasiingie hapa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require_once 'Database.php';
require_once 'Student.php';

$database = new Database();
$db = $database->connect();
$studentObj = new Student($db);

// Kuchukua taarifa za mwanafunzi (Zitafunguliwa - Decrypted ndani ya class)
$current_student = $studentObj->getProfile($_SESSION['user_id']);

// Kuchukua kozi zote zilizopo kwa ajili ya kuonyesha (CRUD: Read)
$query_courses = "SELECT * FROM courses";
$stmt_courses = $db->prepare($query_courses);
$stmt_courses->execute();
$all_courses = $stmt_courses->fetchAll();
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ya Mwanafunzi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-blue-900 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-xl font-bold">CBE Portal | Mwanafunzi</h1>
        <div class="flex items-center space-x-4">
            <span>Karibu, <strong class="text-yellow-400"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-semibold transition">Ondoka (Logout)</a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-600">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Taarifa Zako Binafsi</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-gray-500 font-medium">Jina Kamili:</span>
                    <strong class="text-gray-800 text-base"><?php echo htmlspecialchars($current_student['full_name']); ?></strong>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Namba ya Usajili:</span>
                    <strong class="text-gray-800"><?php echo htmlspecialchars($current_student['reg_number']); ?></strong>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Barua Pepe (Email):</span>
                    <strong class="text-gray-800"><?php echo htmlspecialchars($current_student['email']); ?></strong>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Namba ya Simu:</span>
                    <strong class="text-gray-800"><?php echo htmlspecialchars($current_student['phone']); ?></strong>
                </div>
            </div>
            <div class="mt-6 p-3 bg-green-50 text-green-800 text-xs rounded border border-green-200">
                🛡️ Data zako zimesimbwa (Encrypted) kwenye database kwa usalama wa hali ya juu.
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md md:col-span-2 border-t-4 border-emerald-600">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Kozi Zinazopatikana Chuo</h2>
            
            <?php if (count($all_courses) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                                <th class="p-3 border-b">Siri ya Kozi</th>
                                <th class="p-3 border-b">Jina la Kozi</th>
                                <th class="p-3 border-b text-center">Hatua</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y text-gray-600">
                            <?php foreach ($all_courses as $course): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 font-mono font-bold text-blue-600"><?php echo htmlspecialchars($course['course_code']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($course['course_name']); ?></td>
                                    <td class="p-3 text-center">
                                        <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-xs font-semibold">Sajili Kozi</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-sm">Hakuna kozi zilizowekwa bado na Msimamizi.</p>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>