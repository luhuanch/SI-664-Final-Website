<?php
require_once "pdo.php";
session_start();
$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);


$f = htmlentities($row['first_name']);
$l = htmlentities($row['last_name']);
$e = htmlentities($row['email']);
$h = htmlentities($row['headline']);
$s = htmlentities($row['summary']);

$stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$p = htmlentities($row['description']);
?>


<html>
<h1>Profile Information</h1>
<p>First Name:<a> <?php echo $f ?> </a></p>
<p>Last Name:<a><?php echo $l ?> </a></p>
<p>Email:<a><?php echo $e ?> </a></p>
<p>Headline:</br>
<a><?php echo $h ?> </a></p>
<p>Summary:</br>
<a><?php echo $s ?> </a></p>
<p>Positions:</br>
<a><?php echo $p ?></a></p>
<p><a href="index.php">Done</a></p>
