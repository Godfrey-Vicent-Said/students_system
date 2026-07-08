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
$query_courses = "SELECT * FROM courses ORDER BY course_name ASC";
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
            <span>Karibu, <strong class="text-yellow-400"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Mwanafunzi'); ?></strong></span>
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-semibold transition">Ondoka (Logout)</a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-600 h-fit">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Taarifa Zako Binafsi</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-gray-500 font-medium">Jina Kamili:</span>
                    <strong class="text-gray-800 text-base"><?php echo htmlspecialchars($current_student['full_name'] ?? 'N/A'); ?></strong>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Namba ya Usajili:</span>
                    <strong class="text-gray-800"><?php echo htmlspecialchars($current_student['reg_number'] ?? 'N/A'); ?></strong>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Barua Pepe (Email):</span>
                    <strong class="text-gray-800"><?php echo htmlspecialchars($current_student['email'] ?? 'N/A'); ?></strong>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Namba ya Simu:</span>
                    <strong class="text-gray-800"><?php echo htmlspecialchars($current_student['phone_number'] ?? $current_student['phone'] ?? 'N/A'); ?></strong>
                </div>
            </div>
            <div class="mt-6 p-3 bg-green-50 text-green-800 text-xs rounded border border-green-200">
                🛡️ Data zako zimesimbwa (Encrypted) kwenye database kwa usalama wa hali ya juu.
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md md:col-span-2 border-t-4 border-emerald-600">
            <h2 class="text-lg font-bold text-gray-800 mb-2 border-b pb-2">Kozi Zinazopatikana Chuo</h2>
            
            <div class="mb-4">
                <input type="text" id="courseSearch" placeholder="Tafuta kozi kwa jina au herufi fupi (Mfano: BIT)..." 
                       class="w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm">
            </div>
            
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
                        <tbody id="courseTableBody" class="divide-y text-gray-600">
                            <?php foreach ($all_courses as $course): ?>
                                <tr class="course-row hover:bg-gray-50 transition">
                                    <td class="p-3 font-mono font-bold text-blue-600 course-code"><?php echo htmlspecialchars($course['course_code']); ?></td>
                                    <td class="p-3 course-name"><?php echo htmlspecialchars($course['course_name']); ?></td>
                                    <td class="p-3 text-center">
                                        <button class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-xs font-semibold shadow-sm transition">Sajili Kozi</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <tr id="noMatchRow" style="display: none;">
                                <td colspan="3" class="p-8 text-center text-gray-500 italic">
                                    Hakuna kozi inayolingana na ulivyotafuta.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-sm italic p-4 text-center">Hakuna kozi zilizowekwa bado na Msimamizi.</p>
            <?php endif; ?>
        </div>

    </div>

    <script>
    document.getElementById('courseSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase().trim();
        let rows = document.querySelectorAll('.course-row');
        let matchCount = 0;

        rows.forEach(function(row) {
            // Inasoma jina la kozi na kodi yake kwa pamoja
            let name = row.querySelector('.course-name').textContent.toLowerCase();
            let code = row.querySelector('.course-code').textContent.toLowerCase();
            
            if (name.includes(filter) || code.includes(filter)) {
                row.style.display = '';
                matchCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Kuonyesha ujumbe wa "Hakuna kozi" kama ikikosa matokeo
        let noMatchRow = document.getElementById('noMatchRow');
        if (noMatchRow) {
            if (matchCount === 0 && filter !== '') {
                noMatchRow.style.display = '';
            } else {
                noMatchRow.style.display = 'none';
            }
        }
    });
    </script>

</body>
</html>