<?php
session_start();
require 'db.php';

// Resume basics
$stmt = $pdo->prepare("UPDATE resume 
    SET name=:name, phone=:phone, email=:email, location=:location, summary=:summary 
    WHERE id=1");
$stmt->execute([
    'name' => $_POST['name'],
    'phone' => $_POST['phone'],
    'email' => $_POST['email'],
    'location' => $_POST['location'],
    'summary' => $_POST['summary']
]);

// --- SKILLS ---
if (isset($_POST['skills'])) {
    foreach ($_POST['skills'] as $skill) {
        $category = trim($skill['category'] ?? '');
        $description = trim($skill['description'] ?? '');
        if ($category !== '' || $description !== '') {
            $stmt = $pdo->prepare("UPDATE skills 
                SET category=:c, description=:d 
                WHERE category=:c AND resume_id=1");
            $stmt->execute(['c' => $category, 'd' => $description]);
        }
    }
}

// --- PROJECT TITLES ---
if (isset($_POST['project_titles'])) {
    foreach ($_POST['project_titles'] as $id => $title) {
        $title = trim($title);
        if ($title !== '') {
            $stmt = $pdo->prepare("UPDATE projects SET title=:t WHERE id=:id");
            $stmt->execute(['t' => $title, 'id' => $id]);
        } else {
            // delete projects with empty titles
            $pdo->prepare("DELETE FROM projects WHERE id=:id")->execute(['id' => $id]);
        }
    }
}

// --- PROJECT DETAILS ---
if (isset($_POST['project_details'])) {
    foreach ($_POST['project_details'] as $id => $detail) {
        $detail = trim($detail);
        if ($detail === '') {
            // auto-delete empty bullets
            $pdo->prepare("DELETE FROM project_details WHERE id=:id")->execute(['id' => $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE project_details SET detail=:d WHERE id=:id");
            $stmt->execute(['d' => $detail, 'id' => $id]);
        }
    }
}

// --- EDUCATION ---
if (isset($_POST['education'])) {
    foreach ($_POST['education'] as $id => $edu) {
        $institution = trim($edu['institution'] ?? '');
        $period = trim($edu['period'] ?? '');
        $degree = trim($edu['degree'] ?? '');

        if ($institution === '' && $period === '' && $degree === '') {
            $pdo->prepare("DELETE FROM education WHERE id=:id")->execute(['id' => $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE education 
                SET institution=:i, period=:p, degree=:d WHERE id=:id");
            $stmt->execute([
                'i' => $institution,
                'p' => $period,
                'd' => $degree,
                'id' => $id
            ]);
        }
    }
}

header("Location: index.php");
exit;
?>
