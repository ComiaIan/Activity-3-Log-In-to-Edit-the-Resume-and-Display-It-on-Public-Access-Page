function addDetail(projectId) {
    const input = document.getElementById('detail-input-' + projectId);
    const detail = input.value.trim();
    if (!detail) return alert("Enter a detail first.");

    fetch('add_detail.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'project_id='+encodeURIComponent(projectId)+'&detail='+encodeURIComponent(detail)
    }).then(r=>r.ok?location.reload():alert("Error adding detail"));
}

function addProject() {
    const title = prompt("Enter project title:");
    if (!title) return;
    fetch('add_project.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'title='+encodeURIComponent(title)
    }).then(r=>r.ok?location.reload():alert("Error adding project"));
}

function deleteProject(id) {
    if (!confirm("Delete this project?")) return;
    fetch('delete_project.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id='+encodeURIComponent(id)
    }).then(r=>r.ok?location.reload():alert("Error deleting project"));
}

function addSkill() {
    fetch('add_skill.php').then(r => r.ok ? location.reload() : alert("Error adding skill"));
}

function deleteSkill(id) {
    if (!confirm("Delete this skill?")) return;
    fetch('delete_skill.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id='+encodeURIComponent(id)
    }).then(r=>r.ok?location.reload():alert("Error deleting skill"));
}

function addEducation() {
    fetch('add_education.php').then(r => r.ok ? location.reload() : alert("Error adding education"));
}

function deleteEducation(id) {
    if (!confirm("Delete this education entry?")) return;
    fetch('delete_education.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id='+encodeURIComponent(id)
    }).then(r=>r.ok?location.reload():alert("Error deleting education"));
}
