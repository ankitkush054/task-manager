<?php
session_start();
include '../backend/db.php';
$user_id = $_SESSION['user'];

$user = $conn->query("SELECT name FROM users WHERE id = $user_id")->fetch_assoc();
$username = $user['name'];

if ($_SESSION['role'] != 'member') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="../css/member.css">
</head>
<body>

<div class="sidebar">
    <h2>Welcome, <?= $username ?> 👋</h2>
    <a href="#">Dashboard</a>
    <a href="#pending">Pending</a>
    <a href="#submitted">Submitted</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">

<h2>My Tasks</h2>

<!-- 🔴 Pending -->
<h3 id="pending">Pending Tasks</h3>
<div class="grid">

<?php
$pending = $conn->query("
    SELECT tasks.id, tasks.title, tasks.description, tasks.due_date
    FROM task_submissions
    JOIN tasks ON tasks.id = task_submissions.task_id
    WHERE task_submissions.user_id = $user_id 
    AND task_submissions.status = 'pending'
");

while ($t = $pending->fetch_assoc()) {

    $today = date("Y-m-d");
    $overdue = ($t['due_date'] < $today) ? "overdue" : "";
?>
    <div class="card <?= $overdue ?>">
        <h3><?= $t['title'] ?></h3>
        <p><?= $t['description'] ?></p>
        <p><strong>Deadline:</strong> <?= $t['due_date'] ?></p>

        <button class="btn-submit" onclick="openModal(<?= $t['id'] ?>)">Submit</button>
    </div>
<?php } ?>

</div>

<!-- 🔵 Submitted -->
<h3 id="submitted">Submitted Tasks</h3>
<div class="grid">

<?php
$submitted = $conn->query("
    SELECT tasks.title, tasks.description, tasks.due_date
    FROM task_submissions
    JOIN tasks ON tasks.id = task_submissions.task_id
    WHERE task_submissions.user_id = $user_id 
    AND task_submissions.status = 'submitted'
");

while ($t = $submitted->fetch_assoc()) {
?>
    <div class="card submitted">
        <h3><?= $t['title'] ?></h3>
        <p><?= $t['description'] ?></p>
        <p><strong>Deadline:</strong> <?= $t['due_date'] ?></p>
        <span class="badge">Submitted</span>
    </div>
<?php } ?>

</div>

</div>

<!-- 🔥 SUBMIT MODAL -->
<div id="submitModal" class="modal">
    <div class="modal-box">

        <div class="modal-header">
            <h3>📤 Submit Task</h3>
            <span class="close-btn" onclick="closeModal()">✖</span>
        </div>

        <form method="POST" action="../backend/task.php">

            <input type="hidden" name="task_id" id="task_id">

            <label>Description</label>
            <textarea name="submission_text" placeholder="Explain your work..." required></textarea>

            <label>Project Link</label>
            <input type="url" name="submission_link" placeholder="Paste Google Drive / GitHub link" required>

            <div class="modal-actions">
                <button type="submit" name="submit_task" class="btn-submit">Submit</button>
                <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
            </div>

        </form>

    </div>
</div>

<script>
function openModal(id){
    document.getElementById("submitModal").style.display = "block";
    document.getElementById("task_id").value = id;
}

// Close on outside click
window.onclick = function(e){
    let modal = document.getElementById("submitModal");
    if(e.target === modal){
        modal.style.display = "none";
    }
}

function closeModal(){
    document.getElementById("submitModal").style.display = "none";
}
</script>

</body>
</html>