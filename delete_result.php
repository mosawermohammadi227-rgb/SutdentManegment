<?php
require_once 'database.php';
$id=(int)($_GET['id']??0);
$stmt=$conn->prepare("DELETE FROM results WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
header('Location: results.php');
exit;
?>