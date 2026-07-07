<?php
require_once 'Database.php';
require_once 'User.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

// Kuendesha mbinu ya kutoa mtumiaji kwenye session
$user->logout();

// Kumrudisha mtumiaji kwenye ukurasa wa Login
header("Location: login.php");
exit();
?>