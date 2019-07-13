<!DOCTYPE html>
<?php
require_once "pdo.php";
require_once "bootstrap.php";
session_start();
if(isset($_POST['cancel'])){
header("location: index.php");
return;
}
if(isset($_POST['email']) && isset($_POST['pass'])){
$_SESSION['email'] = $_POST['email'];
$_SESSION['pass'] = $_POST['pass'];
$salt = 'XyZzy12*_';
$check = hash('md5', $salt.$_POST['pass']);
$stmt = $pdo->prepare('SELECT user_id, name FROM users
    WHERE email = :em AND password = :pw');
$stmt->execute(array( ':em' => $_SESSION['email'], ':pw' => $check));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row !== false ) {
    $_SESSION['name'] = $row['name'];
    $_SESSION['user_id'] = $row['user_id'];
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}
}
?>
<head>
<title>Peerapong</title>
</head>
<body>
<header><h1>Please Log In</h1></header>
<form method = "POST">
<p>Email
<input type="text" size ="40" name="email" id="email"></p>
<p>Password
<input type="password" size="40" name="pass" id="pass"></p>
<p><input type="submit" onclick="return doValidate();" value="Log In"/> <input type="submit" name="cancel" value="Cancel"/></p>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        addr = document.getElementById('email').value;
        pw = document.getElementById('pass').value;
        console.log("Validating addr="+addr+" pw="+pw);
        if (addr == null || addr == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if ( addr.indexOf('@') == -1 ) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</body>
