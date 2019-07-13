<!DOCTYPE html>
<?php
require_once "pdo.php";
require_once "util.php";
require_once "bootstrap.php";
session_start();

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

 ?>
<head>
  <title>Peerapong</title>
</head>

<body>
  <header><h1>Profile information</h1></header>
  <p>First Name: <?echo($row['first_name'])?></p>
  <p>Last Name: <?echo($row['last_name'])?></p>
  <p>Email: <?echo($row['email'])?></p>
  <p>Headline: <?echo($row['headline'])?></p>
  <p>Summary: <?echo($row['summary'])?></p>

  <?php
  $stmt = $pdo->prepare('SELECT * FROM education
  JOIN institution
  ON Education.institution_id = Institution.institution_id
  WHERE profile_id = :xyz
  ORDER BY rank');
  $stmt->execute(array(":xyz" => $_GET['profile_id']));
  $rows = $stmt -> fetchall(PDO::FETCH_ASSOC);
  if ($rows == true) {
    echo ("<p>Education: </p>");
    echo ("<ul>");
    foreach ($rows as $row) {
      echo("<li>");
      echo($row['year']);
      echo(": ");
      echo($row['name']);
      echo("</li>");
    } echo("</ul>");
  }
   ?>
  <?php
    $stmt = $pdo->prepare('SELECT * FROM position where profile_id=:xyz');
    $stmt->execute(array(":xyz" => $_GET['profile_id']));
    $rows = $stmt->fetchall(PDO::FETCH_ASSOC);
    if ($rows == true) {
    echo("<p>Position: </p>");
    echo("<ul>");
    foreach ($rows as $row) {
      echo("<li>");
      echo($row['year']);
      echo(": ");
      echo($row['description']);
      echo("</li>");
    } echo("</ul>");
  }
   ?>
    <p><a href = "index.php">Done</a></p>
</body>
