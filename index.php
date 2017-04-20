<?php
require_once "pdo.php";
session_start();

if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";

}

if (isset($_SESSION['name'])){
    echo"<h1>Huanchen Lu's Resume Registry</h1>";
    unset($_SESSION['success']);
    echo('<table border="1">'."\n");
    $stmt = $pdo->query('SELECT profile_id, first_name, last_name, email, headline, summary FROM Profile');
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

    echo("<tr><td>");
    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.$row['first_name'].$row['last_name'].'</a> ');
    echo("</td><td>");
    echo(htmlentities($row['headline']));
    echo("</td><td>");
    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
    echo("</td></tr>\n");
}

echo ('</table>')
?>
<p>
<a href='add.php'>Add New Entry</a>
</p>
<p>
<a href = 'logout.php'>Logout</a>
</p>
<?php
}else{
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Huanchen Lu's Resume Registry</title>
</head>
<h1>Huanchen Lu's Resume Registry</h1>
<p><a href = 'login.php'>Please log in</a></p>
<?php
echo('<table border="1">'."\n");
$stmt = $pdo->query('SELECT profile_id, first_name, last_name, email, headline, summary FROM Profile');
    while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {

    echo("<tr><td>");
    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.$row['first_name'].$row['last_name'].'</a> ');
    echo("</td><td>");
    echo(htmlentities($row['headline']));
    echo("</td></tr>");
}
echo ('</table>')
?>
<p><b>Note:</b>
  Your implementation should retain data across multiple logout/login sessions. This sample implementation clears all its data periodically - which you should not do in your implementation.</p>
<?php
}
?>
