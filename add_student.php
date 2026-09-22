<?php
require_once 'database.php';
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $student_id=trim($_POST['student_id']); $first=trim($_POST['first_name']); $last=trim($_POST['last_name']); $father=trim($_POST['father_name']);
  $gender=$_POST['gender']; $phone=trim($_POST['phone']); $address=trim($_POST['address']); $class=trim($_POST['class_name']);
  if($student_id===''||$first===''||$last===''||$father==='') $error='Please fill all required fields.';
  else {
    $stmt=$conn->prepare("INSERT INTO students (student_id,first_name,last_name,father_name,gender,phone,address,class_name) VALUES (?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssssss",$student_id,$first,$last,$father,$gender,$phone,$address,$class);
    if($stmt->execute()){header('Location:index.php');exit;} $error=$stmt->errno===1062?'Student ID already exists.':'Unable to add student.';
  }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Add Student</title><link rel="stylesheet" href="style.css"></head><body><div class="container"><header><h1>Add Student</h1><p>Create a new student record.</p></header><nav><a href="index.php">Students</a><a class="active" href="add_student.php">Add Student</a><a href="attendance.php">Attendance</a><a href="results.php">Results</a></nav><?php if($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?><form class="form-card" method="post"><div class="form-grid"><?php foreach([['student_id','Student ID','required'],['first_name','First Name','required'],['last_name','Last Name','required'],['father_name','Father Name','required'],['phone','Phone',''],['address','Address',''],['class_name','Class','']] as $f): ?><label><?=$f[1]?><input name="<?=$f[0]?>" <?=$f[2]?>></label><?php endforeach; ?><label>Gender<select name="gender"><option value="Male">Male</option><option value="Female">Female</option></select></label></div><button class="button" type="submit">Save Student</button></form></div></body></html>