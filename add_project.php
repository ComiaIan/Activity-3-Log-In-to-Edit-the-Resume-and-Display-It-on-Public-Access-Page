<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $detail1 = trim($_POST['detail1']);
    $detail2 = trim($_POST['detail2']);
    $detail3 = trim($_POST['detail3']);

    // Get resume ID (assuming only one resume)
    $stmt = $pdo->query("SELECT id FROM resume LIMIT 1");
    $resume = $stmt->fetch(PDO::FETCH_ASSOC);
    $resume_id = $resume['id'];

    // Insert project
    $stmt = $pdo->prepare("INSERT INTO projects (resume_id, title) VALUES (:resume_id, :title)");
    $stmt->execute(['resume_id' => $resume_id, 'title' => $title]);

    // Get the new project ID
    $project_id = $pdo->lastInsertId();

    // Insert project details
    $details = [$detail1, $detail2, $detail3];
    $stmt = $pdo->prepare("INSERT INTO project_details (project_id, detail) VALUES (:pid, :detail)");
    foreach ($details as $d) {
        $stmt->execute(['pid' => $project_id, 'detail' => $d]);
    }

    header("Location: index.php");
    exit;
}
?>
