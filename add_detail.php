<?php
session_start();
require 'db.php';

// Must be logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $project_id = intval($_POST['project_id']);
    $detail = trim($_POST['detail']);

    if ($project_id > 0 && !empty($detail)) {
        $stmt = $pdo->prepare("INSERT INTO project_details (project_id, detail) VALUES (:pid, :detail)");
        $stmt->execute(['pid' => $project_id, 'detail' => $detail]);
    }
}

header("Location: index.php");
exit;
?>
