<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $details = trim($_POST['details'] ?? '');

    if ($id <= 0 || empty($title)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid input"]);
        exit;
    }

    // Update title
    $stmt = $pdo->prepare("UPDATE projects SET title = :title WHERE id = :id");
    $stmt->execute(['title' => $title, 'id' => $id]);

    // Delete old details
    $pdo->prepare("DELETE FROM project_details WHERE project_id = :id")->execute(['id' => $id]);

    // Add new details
    if (!empty($details)) {
        $lines = array_filter(array_map('trim', explode("\n", $details)));
        foreach ($lines as $line) {
            $pdo->prepare("INSERT INTO project_details (project_id, detail) VALUES (:pid, :detail)")
                ->execute(['pid' => $id, 'detail' => $line]);
        }
    }

    echo json_encode(["success" => true, "message" => "Project updated successfully!"]);
}
?>
