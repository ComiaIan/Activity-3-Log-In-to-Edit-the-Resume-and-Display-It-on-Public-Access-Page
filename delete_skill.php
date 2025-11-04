<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit('Unauthorized');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM skills WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
    echo json_encode(['success' => true]);
}
?>
