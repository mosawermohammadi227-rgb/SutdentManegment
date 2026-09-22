<?php
require_once 'database.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    http_response_code(404);
    die('Student not found.');
}

$attendance = $conn->query("SELECT * FROM attendance WHERE student_id=$id ORDER BY attendance_date DESC");
$results = $conn->query("SELECT * FROM results WHERE student_id=$id ORDER BY id DESC");

$attendanceTotal = $attendance->num_rows;
$present = $conn->query("SELECT COUNT(*) AS total FROM attendance WHERE student_id=$id AND status='Present'")->fetch_assoc()['total'];
$absent = $conn->query("SELECT COUNT(*) AS total FROM attendance WHERE student_id=$id AND status='Absent'")->fetch_assoc()['total'];
$attendancePercent = $attendanceTotal > 0 ? round(($present / $attendanceTotal) * 100, 1) : 0;

$resultRows = [];
$resultTotal = 0;
$resultCount = 0;
while ($row = $results->fetch_assoc()) {
    $resultRows[] = $row;
    $resultTotal += (float)$row['marks'];
    $resultCount++;
}
$average = $resultCount > 0 ? round($resultTotal / $resultCount, 2) : 0;

function gradeFromMarks(float $marks): string {
    if ($marks >= 90) return 'A+';
    if ($marks >= 80) return 'A';
    if ($marks >= 70) return 'B';
    if ($marks >= 60) return 'C';
    if ($marks >= 50) return 'D';
    return 'F';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Profile</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
<header class="hero">
    <div>
        <h1><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></h1>
        <p>Student ID: <?= htmlspecialchars($student['student_id']) ?></p>
    </div>
    <div class="actions">
        <a class="button secondary" href="index.php">Back</a>
        <a class="button" href="edit_student.php?id=<?= $id ?>">Edit Student</a>
    </div>
</header>

<nav>
<a href="index.php">Students</a>
<a href="add_student.php">Add Student</a>
<a href="attendance.php">Attendance</a>
<a href="results.php">Results</a>
</nav>

<div class="profile-grid">
<section class="form-card">
<h2>Student Information</h2>
<div class="info-grid">
<div><span>Student ID</span><strong><?= htmlspecialchars($student['student_id']) ?></strong></div>
<div><span>Full Name</span><strong><?= htmlspecialchars($student['first_name'].' '.$student['last_name']) ?></strong></div>
<div><span>Father Name</span><strong><?= htmlspecialchars($student['father_name']) ?></strong></div>
<div><span>Gender</span><strong><?= htmlspecialchars($student['gender']) ?></strong></div>
<div><span>Phone</span><strong><?= htmlspecialchars($student['phone'] ?: '-') ?></strong></div>
<div><span>Class</span><strong><?= htmlspecialchars($student['class_name'] ?: '-') ?></strong></div>
<div class="wide"><span>Address</span><strong><?= htmlspecialchars($student['address'] ?: '-') ?></strong></div>
</div>
</section>

<section class="cards compact">
<div class="card"><span>Present</span><strong><?= $present ?></strong></div>
<div class="card"><span>Absent</span><strong><?= $absent ?></strong></div>
<div class="card"><span>Attendance</span><strong><?= $attendancePercent ?>%</strong></div>
<div class="card"><span>Average Marks</span><strong><?= $average ?></strong></div>
</section>
</div>

<div class="two-column">
<section>
<h2>Attendance History</h2>
<div class="table-wrap">
<table>
<thead><tr><th>Date</th><th>Status</th></tr></thead>
<tbody>
<?php while ($row = $attendance->fetch_assoc()): ?>
<tr><td><?= htmlspecialchars($row['attendance_date']) ?></td><td><span class="badge <?= strtolower($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td></tr>
<?php endwhile; ?>
<?php if ($attendanceTotal === 0): ?><tr><td colspan="2" class="empty">No attendance records.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
</section>

<section>
<h2>Results</h2>
<div class="table-wrap">
<table>
<thead><tr><th>Subject</th><th>Marks</th><th>Grade</th></tr></thead>
<tbody>
<?php foreach ($resultRows as $row): ?>
<tr>
<td><?= htmlspecialchars($row['subject']) ?></td>
<td><?= htmlspecialchars($row['marks']) ?></td>
<td><span class="badge"><?= gradeFromMarks((float)$row['marks']) ?></span></td>
</tr>
<?php endforeach; ?>
<?php if ($resultCount === 0): ?><tr><td colspan="3" class="empty">No results.</td></tr><?php endif; ?>
</tbody>
</table>
</div>
</section>
</div>
</div>
</body>
</html>