<?php
session_start();
include 'db.php';

/* =========================
   ✅ ADD TASK (ADMIN)
========================= */
if (isset($_POST['add_task'])) {

    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $users = $_POST['users']; // array
    $created_by = $_SESSION['user'];

    // Validation
    if (empty($title) || empty($description) || empty($due_date)) {
        die("All fields are required");
    }

    // Insert task
    $stmt = $conn->prepare("INSERT INTO tasks (title, description, due_date, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $description, $due_date, $created_by);
    $stmt->execute();

    $task_id = $conn->insert_id;

    // Assign multiple users
    foreach ($users as $uid) {
        $stmt2 = $conn->prepare("INSERT INTO task_submissions (task_id, user_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $task_id, $uid);
        $stmt2->execute();
    }

    header("Location: ../admin/dashboard.php");
    exit;
}

/* =========================
   ✏️ UPDATE TASK (ADMIN)
========================= */
if (isset($_POST['update_task'])) {

    $task_id = $_POST['task_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    // Update task info
    $conn->query("
        UPDATE tasks 
        SET title='$title', description='$description', due_date='$due_date' 
        WHERE id=$task_id
    ");

    // Get existing members from DB
    $existing = [];
    $res = $conn->query("SELECT user_id FROM task_submissions WHERE task_id=$task_id");
    while ($row = $res->fetch_assoc()) {
        $existing[] = $row['user_id'];
    }

    // Get selected members from form
    $selected = isset($_POST['users']) ? $_POST['users'] : [];

    // 🔥 Add new members (if not already exists)
    foreach ($selected as $uid) {
        if (!in_array($uid, $existing)) {
            $conn->query("INSERT INTO task_submissions (task_id, user_id)
                          VALUES ($task_id, $uid)");
        }
    }

    // 🔥 Remove only unchecked members
    foreach ($existing as $uid) {
        if (!in_array($uid, $selected)) {
            $conn->query("DELETE FROM task_submissions 
                          WHERE task_id=$task_id AND user_id=$uid");
        }
    }

    header("Location: ../admin/dashboard.php");
}

/* =========================
   ❌ DELETE TASK (ADMIN)
========================= */
if (isset($_POST['delete_task'])) {

    $task_id = $_POST['task_id'];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();

    header("Location: ../admin/dashboard.php");
    exit;
}

/* =========================
   📤 MEMBER SUBMIT TASK
========================= */
if (isset($_POST['submit_task'])) {

    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['user'];
    $text = $_POST['submission_text'];
    $link = $_POST['submission_link'];

    $conn->query("
        UPDATE task_submissions 
        SET status='submitted',
            submission_text='$text',
            submission_link='$link'
        WHERE task_id=$task_id AND user_id=$user_id
    ");

    header("Location: ../member/dashboard.php");
}

/* =========================
   ✅ ADMIN APPROVE TASK
========================= */
if (isset($_POST['approve_task'])) {

    $task_id = $_POST['task_id'];
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("
        UPDATE task_submissions 
        SET status='completed' 
        WHERE task_id=? AND user_id=?
    ");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();

    header("Location: ../admin/dashboard.php");
    exit;
}

/* =========================
   ⚠️ INVALID REQUEST
========================= */
echo "Invalid request";
?>