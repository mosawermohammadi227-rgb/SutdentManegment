<?php
require_once 'database.php';

$totalStudents = $conn->query("SELECT COUNT(*) AS total FROM students")->fetch_assoc()['total'];
$totalAttendance = $conn->query("SELECT COUNT(*) AS total FROM attendance")->fetch_assoc()['total'];
$totalResults = $conn->query("SELECT COUNT(*) AS total FROM results")->fetch_assoc()['total'];
$totalPresent = $conn->query("SELECT COUNT(*) AS total FROM attendance WHERE status='Present'")->fetch_assoc()['total'];

$search = trim($_GET['search'] ?? '');
$searchSql = '';
if ($search !== '') {
    $safeSearch = $conn->real_escape_string($search);
    $searchSql = "WHERE student_id LIKE '%$safeSearch%'
        OR first_name LIKE '%$safeSearch%'
        OR last_name LIKE '%$safeSearch%'
        OR father_name LIKE '%$safeSearch%'
        OR phone LIKE '%$safeSearch%'
        OR class_name LIKE '%$safeSearch%'";
}

$students = $conn->query("SELECT * FROM students $searchSql ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Management System</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<header class="hero">
    <div>
        <h1>Student Management System</h1>
        <p>Manage students, attendance and academic results in one place.</p>
    </div>
    <a class="button" href="add_student.php">+ Add Student</a>
</header>

<nav>
<a class="active" href="index.php">Students</a>
<a href="add_student.php">Add Student</a>
<a href="attendance.php">Attendance</a>
<a href="results.php">Results</a>
</nav>

<section class="cards">
<div class="card"><span>Total Students</span><strong><?= $totalStudents ?></strong></div>
<div class="card"><span>Attendance Records</span><strong><?= $totalAttendance ?></strong></div>
<div class="card"><span>Total Results</span><strong><?= $totalResults ?></strong></div>
<div class="card"><span>Present Records</span><strong><?= $totalPresent ?></strong></div>
</section>

<div class="section-title">
    <div>
        <h2>Students</h2>
        <p class="muted"><?= $search !== '' ? 'Search results for: '.htmlspecialchars($search) : 'All registered students' ?></p>
    </div>
</div>

<form class="search-bar" method="get">
    <input type="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by ID, name, father, phone or class...">
    <button class="button" type="submit">Search</button>
    <?php if ($search !== ''): ?><a class="button secondary" href="index.php">Clear</a><?php endif; ?>
</form>

<div class="table-wrap">
<table>
<thead><tr><th>#</th><th>Student ID</th><th>Name</th><th>Father</th><th>Gender</th><th>Phone</th><th>Class</th><th>Actions</th></tr></thead>
<tbody>
<?php while ($student = $students->fetch_assoc()): ?>
<tr>
<td><?= $student['id'] ?></td>
<td><strong><?= htmlspecialchars($student['student_id']) ?></strong></td>
<td><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></td>
<td><?= htmlspecialchars($student['father_name']) ?></td>
<td><?= htmlspecialchars($student['gender']) ?></td>
<td><?= htmlspecialchars($student['phone'] ?? '') ?></td>
<td><?= htmlspecialchars($student['class_name'] ?? '') ?></td>
<td class="actions">
<a href="student.php?id=<?= $student['id'] ?>">View</a>
<a href="edit_student.php?id=<?= $student['id'] ?>">Edit</a>
<a class="danger" href="delete_student.php?id=<?= $student['id'] ?>" onclick="return confirm('Delete this student and all related records?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
<?php if ($students->num_rows === 0): ?><tr><td colspan="8" class="empty">No students found.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
</div>
</body>
</html>