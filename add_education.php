<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit('Unauthorized');
}

// Get resume ID
$stmt = $pdo->query("SELECT id FROM resume LIMIT 1");
$resume = $stmt->fetch(PDO::FETCH_ASSOC);
$resume_id = $resume['id'];

// Insert blank education entry
$stmt = $pdo->prepare("INSERT INTO education (resume_id, institution, period, degree) VALUES (:rid, '', '', '')");
$stmt->execute(['rid' => $resume_id]);

header("Location: index.php");
exit;
?>
