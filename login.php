<head>
<title>Peerapong</title>
</head>
<?php
require_once "pdo.php";
require_once "bootstrap.php";
$message = false;

// p' OR '1' = '1

if ( isset($_POST['who']) && isset($_POST['pass'])  ) {
    #echo("<p>Handling POST data...</p>\n");

    $sql = "SELECT name FROM users 
        WHERE email = :em AND password = :pw";

    #echo "<p>$sql</p>\n";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':em' => $_POST['who'], 
        ':pw' => $_POST['pass']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		var_dump($row);


  if ( $row === FALSE ) {
     if ((strlen($_POST['who']<1)) || (strlen($_POST['pass']<1))){
	  $message = "Email and password are required!";}
	 else if (($_POST['who']==$row['email'])&&($_POST['pass']!==$row['password'])){
	 $message = "Incorrect password";}
	 else if (strpos($_POST['who'],'@')===false){
	 $message = "Email must have an at-sign (@)";}
  }
   else{ 
      echo "<p>Login success.</p>\n";
	  header("Location: autos.php?name=".urlencode($_POST['who']));	  
   }	


}
?>
<h1>Please Log In</h1>
<p style=color:red>
<?php
	if($message !== false){
	echo($message);
	}
?>
</p>
<form method="post">
<p>Email:
<input type="text" size="40" name="who"></p>
<p>Password:
<input type="text" size="40" name="pass"></p>
<p><input type="submit" value="Log In"/>
<a href="<?php echo($_SERVER['PHP_SELF']);?>">Refresh</a></p>
</form>
