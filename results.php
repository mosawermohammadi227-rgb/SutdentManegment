<?php
require_once 'database.php';

$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $sid=(int)($_POST['student_id']??0);
    $subject=trim($_POST['subject']??'');
    $marks=(float)($_POST['marks']??-1);
    if($sid<=0||$subject===''||$marks<0||$marks>100) $error='Please enter valid result information.';
    else {$s=$conn->prepare("INSERT INTO results(student_id,subject,marks) VALUES(?,?,?)");$s->bind_param("isd",$sid,$subject,$marks);$s->execute();header('Location: results.php');exit;}
}
$students=$conn->query("SELECT id,student_id,first_name,last_name FROM students ORDER BY first_name,last_name");
$records=$conn->query("SELECT r.*,s.student_id,s.first_name,s.last_name FROM results r JOIN students s ON s.id=r.student_id ORDER BY r.id DESC");
function gradeFromMarks(float $m):string{if($m>=90)return'A+';if($m>=80)return'A';if($m>=70)return'B';if($m>=60)return'C';if($m>=50)return'D';return'F';}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Results</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header class="hero"><div><h1>Results</h1><p>Manage subjects, marks and grades.</p></div><a class="button secondary" href="index.php">Back to Students</a></header>
<nav><a href="index.php">Students</a><a href="add_student.php">Add Student</a><a href="attendance.php">Attendance</a><a class="active" href="results.php">Results</a></nav>
<?php if($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form class="form-card" method="post"><div class="form-grid"><label>Student<select name="student_id" required><?php while($s=$students->fetch_assoc()): ?><option value="<?=$s['id']?>"><?=htmlspecialchars($s['student_id'].' - '.$s['first_name'].' '.$s['last_name'])?></option><?php endwhile; ?></select></label><label>Subject<input name="subject" required></label><label>Marks<input type="number" name="marks" min="0" max="100" step="0.01" required></label></div><button class="button">Save Result</button></form>
<div class="section-title"><h2>Results History</h2></div><div class="table-wrap"><table><thead><tr><th>Student</th><th>Subject</th><th>Marks</th><th>Grade</th><th>Action</th></tr></thead><tbody>
<?php while($r=$records->fetch_assoc()): ?><tr><td><?=htmlspecialchars($r['student_id'].' - '.$r['first_name'].' '.$r['last_name'])?></td><td><?=htmlspecialchars($r['subject'])?></td><td><?=htmlspecialchars($r['marks'])?></td><td><span class="badge"><?=gradeFromMarks((float)$r['marks'])?></span></td><td><a href="delete_result.php?id=<?=$r['id']?>" onclick="return confirm('Delete this result?')">Delete</a></td></tr><?php endwhile; ?>
<?php if($records->num_rows===0): ?><tr><td colspan="5" class="empty">No results yet.</td></tr><?php endif; ?></tbody></table></div></div></body></html>