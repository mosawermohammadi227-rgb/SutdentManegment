<?php
require_once 'database.php';
$id=(int)($_GET['id']??0);
$stmt=$conn->prepare("DELETE FROM students WHERE id=?");$stmt->bind_param("i",$id);$stmt->execute();
header('Location:index.php');exit;
?>