<!DOCTYPE html>
<head>
<title>Peerapong</title>
<?php
require_once "pdo.php";
require_once "bootstrap.php";
session_start();
?>
</head>

<body>
<header><h1>Peerapong's Resume Registry</h1></header>

<?
if(isset($_SESSION['success'])) {
echo("<p style=color:green>".$_SESSION['success']."</p>");
unset($_SESSION['success']);
}
?>
<?php
$stmt = $pdo->query("SELECT profile_id, first_name, headline FROM profile");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo('<table border = "1">'."\n");
echo('<thead><tr><th>Name</th><th>Headline</th><th>Action</th></tr></thead>');
foreach ( $rows as $row ) {
    echo("<tr><td>");
    echo('<a href = "view.php?profile_id='.$row['profile_id'].'">');
    echo(htmlentities($row['first_name']));
    echo("</a>");
    echo("</td><td>");
    echo('<a href = "view.php?profile_id='.$row['profile_id'].'">');
	  echo(htmlentities($row['headline']));
    echo("</a>");
    echo("</td><td>");
	echo('<a href = "edit.php?profile_id='.$row['profile_id'].'">Edit</a>/');
	echo('<a href = "delete.php?profile_id='.$row['profile_id'].'">Delete</a>/');
	echo("\n</form>\n");
	echo("</td></tr>\n");
}
echo("</table>");
?>
<?php
if (isset($_SESSION['email'])){
echo("<p id = 'logout'><a href = 'logout.php'>Logout</a></p>\n<p id = 'three'><a href='add.php'>Add New Entry</a></p>");
} else {echo("<p id = 'one'><a href = 'login.php'>Please log in</a></p>");
}
?>
</body>
