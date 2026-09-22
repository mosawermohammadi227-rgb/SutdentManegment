<?php
require_once 'database.php';

$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $sid=trim($_POST['student_id']??'');
    $first=trim($_POST['first_name']??'');$last=trim($_POST['last_name']??'');$father=trim($_POST['father_name']??'');
    $gender=$_POST['gender']??'Male';$phone=trim($_POST['phone']??'');$address=trim($_POST['address']??'');$class=trim($_POST['class_name']??'');
    if($sid===''||$first===''||$last===''||$father==='') $error='Please fill all required fields.';
    elseif(!in_array($gender,['Male','Female'],true)) $error='Invalid gender.';
    else {
        try{
            $stmt=$conn->prepare("INSERT INTO students(student_id,first_name,last_name,father_name,gender,phone,address,class_name) VALUES(?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssssss",$sid,$first,$last,$father,$gender,$phone,$address,$class);
            $stmt->execute();header('Location:index.php');exit;
        }catch(mysqli_sql_exception $e){$error=$e->getCode()===1062?'Student ID already exists.':'Unable to add student.';}
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Add Student</title><link rel="stylesheet" href="style.css"></head>
<body><div class="container"><header class="hero"><div><h1>Add Student</h1><p>Create a new student record.</p></div><a class="button secondary" href="index.php">Back</a></header>
<nav><a href="index.php">Students</a><a class="active" href="add_student.php">Add Student</a><a href="attendance.php">Attendance</a><a href="results.php">Results</a></nav>
<?php if($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
<form class="form-card" method="post"><div class="form-grid">
<label>Student ID<input name="student_id" required></label><label>First Name<input name="first_name" required></label><label>Last Name<input name="last_name" required></label><label>Father Name<input name="father_name" required></label>
<label>Gender<select name="gender"><option value="Male">Male</option><option value="Female">Female</option></select></label><label>Phone<input name="phone"></label><label>Address<input name="address"></label><label>Class<input name="class_name"></label>
</div><button class="button" type="submit">Save Student</button></form></div></body></html>