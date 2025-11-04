<?php
session_start();
require 'db.php';

$stmt = $pdo->query("SELECT * FROM resume LIMIT 1");
$resume = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resume) die("No resume data found in the database.");

// Fetch skills
$stmt = $pdo->prepare("SELECT id, category, description FROM skills WHERE resume_id = :id");
$stmt->execute(['id' => $resume['id']]);
$skills = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch projects
$stmt = $pdo->prepare("SELECT id, title FROM projects WHERE resume_id = :id");
$stmt->execute(['id' => $resume['id']]);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch project details
$projectData = [];
foreach ($projects as $proj) {
    $stmt = $pdo->prepare("SELECT id, detail FROM project_details WHERE project_id = :pid");
    $stmt->execute(['pid' => $proj['id']]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $projectData[] = [
        'id' => $proj['id'],
        'title' => $proj['title'],
        'details' => $details
    ];
}

// Fetch education
$stmt = $pdo->prepare("SELECT id, institution, period, degree FROM education WHERE resume_id = :id");
$stmt->execute(['id' => $resume['id']]);
$education = $stmt->fetchAll(PDO::FETCH_ASSOC);

$isLoggedIn = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($resume["name"]); ?> - CV</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <div class="topbar">
        <?php if ($isLoggedIn): ?>
            Welcome, <?= htmlspecialchars($_SESSION['user']); ?> |
            <a href="logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="login.php" class="logout-btn">Login</a>
        <?php endif; ?>
    </div>


    <?php if ($isLoggedIn): ?><form method="POST" action="update_resume.php" id="resumeForm"><?php endif; ?>

    <!-- HEADER -->
    <h1>
        <?php if ($isLoggedIn): ?>
            <input type="text" name="name" value="<?= htmlspecialchars($resume['name']); ?>">
        <?php else: ?>
            <?= htmlspecialchars($resume["name"]); ?>
        <?php endif; ?>
    </h1>

    <div class="contact">
        <?php if ($isLoggedIn): ?>
            <label>Phone:</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($resume['phone']); ?>"><br><br>
            <label>Email:</label>
            <input type="text" name="email" value="<?= htmlspecialchars($resume['email']); ?>"><br><br>
            <label>Address:</label>
            <input type="text" name="location" value="<?= htmlspecialchars($resume['location']); ?>">
        <?php else: ?>
            <p>📞 <?= htmlspecialchars($resume["phone"]); ?> |
                <?= htmlspecialchars($resume["email"]); ?> |
                <?= htmlspecialchars($resume["location"]); ?></p>
        <?php endif; ?>
    </div>

    <!-- SUMMARY -->
    <div class="section">
        <h2>SUMMARY</h2>
        <?php if ($isLoggedIn): ?>
            <textarea name="summary" rows="4"><?= htmlspecialchars($resume['summary']); ?></textarea>
        <?php else: ?>
            <p><?= nl2br(htmlspecialchars($resume["summary"])); ?></p>
        <?php endif; ?>
    </div>

    <!-- SKILLS -->
    <div class="section">
        <div class="section-header">
            <h2>TECHNICAL SKILLS</h2>
            <?php if ($isLoggedIn): ?>
                <button type="button" onclick="addSkill()" class="btn btn-confirm">+ Add Skill</button>
            <?php endif; ?>
        </div>
        <ul>
            <?php foreach ($skills as $skill): ?>
                <li>
                    <?php if ($isLoggedIn): ?>
                        <input type="text" name="skills[<?= $skill['id'] ?>][category]" value="<?= htmlspecialchars($skill['category']); ?>" placeholder="Category">
                        <input type="text" name="skills[<?= $skill['id'] ?>][description]" value="<?= htmlspecialchars($skill['description']); ?>" placeholder="Description">
                        <button type="button" onclick="deleteSkill(<?= $skill['id'] ?>)" class="btn btn-cancel">Delete</button>
                    <?php else: ?>
                        <strong><?= htmlspecialchars($skill["category"]); ?>:</strong> <?= htmlspecialchars($skill["description"]); ?>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- PROJECTS -->
    <div class="section">
        <div class="section-header">
            <h2>PROJECTS</h2>
            <?php if ($isLoggedIn): ?>
                <button type="button" onclick="addProject()" class="btn btn-confirm">+ Add Project</button>
            <?php endif; ?>
        </div>

        <?php foreach ($projectData as $p): ?>
        <div class="project-block">
            <p><strong>
                <?php if ($isLoggedIn): ?>
                    <input type="text" name="project_titles[<?= $p['id'] ?>]" value="<?= htmlspecialchars($p['title']); ?>">
                <?php else: ?>
                    <?= htmlspecialchars($p['title']); ?>
                <?php endif; ?>
            </strong></p>
            <ul id="details-<?= $p['id'] ?>">
                <?php foreach ($p['details'] as $detail): ?>
                    <li>
                        <?php if ($isLoggedIn): ?>
                            <input type="text" name="project_details[<?= $detail['id'] ?>]" value="<?= htmlspecialchars($detail['detail']); ?>">
                        <?php else: ?>
                            <?= htmlspecialchars($detail['detail']); ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if ($isLoggedIn): ?>
                <div class="project-actions">
                    <input type="text" id="detail-input-<?= $p['id'] ?>" placeholder="New detail...">
                    <button type="button" onclick="addDetail(<?= $p['id'] ?>)" class="btn btn-info">+ Add Detail</button>
                    <button type="button" onclick="deleteProject(<?= $p['id'] ?>)" class="btn btn-cancel">Delete Project</button>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- EDUCATION -->
    <div class="section">
        <div class="section-header">
            <h2>EDUCATION</h2>
            <?php if ($isLoggedIn): ?>
                <button type="button" onclick="addEducation()" class="btn btn-confirm">+ Add Education</button>
            <?php endif; ?>
        </div>

        <?php foreach ($education as $edu): ?>
            <div class="education-block">
                <?php if ($isLoggedIn): ?>
                    <input type="text" name="education[<?= $edu['id'] ?>][institution]" value="<?= htmlspecialchars($edu['institution']); ?>"><br>
                    <input type="text" name="education[<?= $edu['id'] ?>][period]" value="<?= htmlspecialchars($edu['period']); ?>"><br>
                    <input type="text" name="education[<?= $edu['id'] ?>][degree]" value="<?= htmlspecialchars($edu['degree']); ?>"><br>
                    <button type="button" onclick="deleteEducation(<?= $edu['id'] ?>)" class="btn btn-cancel">Delete</button>
                <?php else: ?>
                    <p><strong><?= htmlspecialchars($edu["institution"]); ?></strong> (<?= htmlspecialchars($edu["period"]); ?>)<br><?= htmlspecialchars($edu["degree"]); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($isLoggedIn): ?>
        <div class="edit-controls">
            <button type="button" class="btn btn-cancel" onclick="window.location.reload()">Cancel</button>
            <button type="submit" class="btn btn-confirm" onclick="cleanEmptyDetails()">Confirm</button>
        </div>
    </form>
    <?php endif; ?>
</div>

<script src="script.js"></script>
</body>
</html>
