<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../backend/db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="#">Dashboard</a>
    <a href="#tasks">Tasks</a>
    <a href="#submissions">Submissions</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- Main -->
<div class="main">

    <div class="topbar">
        <h2>Admin Dashboard</h2>
    </div>

    <!-- ================= CREATE TASK ================= -->
    <div class="card">
        <h3>Create Task</h3>

        <form action="../backend/task.php" method="POST">

            <input type="text" name="title" placeholder="Task Title" required>

            <textarea name="description" class="desc-box" placeholder="Task Description" required></textarea>

            <label>Deadline</label>
            <input type="date" name="due_date" required>

            <label>Select Members</label>
            <div class="member-box">
                <?php
                $users = $conn->query("SELECT * FROM users WHERE role='member'");
                while ($u = $users->fetch_assoc()) {
                    echo "
                    <label class='member-item'>
                        <input type='checkbox' name='users[]' value='{$u['id']}'>
                        {$u['name']}
                    </label>
                    ";
                }
                ?>
            </div>

            <button class="btn-add" name="add_task">Add Task</button>
        </form>
    </div>

    <!-- ================= TASK LIST ================= -->
    <div class="card" id="tasks">
        <h3>All Tasks + Status</h3>

        <table>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Deadline</th>
                <th>Members</th>
                <th>Action</th>
            </tr>

            <?php
            $tasks = $conn->query("SELECT * FROM tasks");

            while ($t = $tasks->fetch_assoc()) {

                echo "<tr>";

                echo "<td>{$t['title']}</td>";
                echo "<td>{$t['description']}</td>";
                echo "<td>{$t['due_date']}</td>";

                echo "<td>";

                $subs = $conn->query("
                    SELECT users.name, task_submissions.status
                    FROM task_submissions
                    JOIN users ON users.id = task_submissions.user_id
                    WHERE task_id = {$t['id']}
                ");

                while ($s = $subs->fetch_assoc()) {
                    echo "<p>{$s['name']} → <span class='{$s['status']}'>{$s['status']}</span></p>";
                }

                echo "</td>";

                echo "<td>
                <form action='../backend/task.php' method='POST' style='display:inline'>
                    <input type='hidden' name='task_id' value='{$t['id']}'>
                    <button class='btn-delete' name='delete_task'>Delete</button>
                </form>
                </td>";

                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <!-- ================= 🔥 TASK SUBMISSIONS ================= -->
    <div class="card" id="submissions">
        <h3>Task Submissions</h3>

        <table>
            <tr>
                <th>Member</th>
                <th>Task</th>
                <th>Description</th>
                <th>Link</th>
                <th>Status</th>
            </tr>

            <?php
            $subs = $conn->query("
                SELECT users.name AS user_name,
                       tasks.title AS task_title,
                       task_submissions.submission_text,
                       task_submissions.submission_link,
                       task_submissions.status
                FROM task_submissions
                JOIN users ON users.id = task_submissions.user_id
                JOIN tasks ON tasks.id = task_submissions.task_id
                WHERE task_submissions.status = 'submitted'
            ");

            while ($s = $subs->fetch_assoc()) {
                echo "<tr>";

                echo "<td>{$s['user_name']}</td>";
                echo "<td>{$s['task_title']}</td>";
                echo "<td>{$s['submission_text']}</td>";

                echo "<td>
                    <a href='{$s['submission_link']}' target='_blank'>View</a>
                </td>";

                echo "<td>
                    <span class='submitted'>Submitted</span>
                </td>";

                echo "</tr>";
            }
            ?>
        </table>
    </div>

</div>

</body>
</html>