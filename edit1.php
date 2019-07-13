<?php
require_once "pdo.php";
require_once "bootstrap.php";
session_start();

if(isset($_POST['cancel'])){
header("location: index.php");
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
     && isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['profile_id'])) {

    // Data validation
    if ( strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1
		|| strlen($_POST['headline'])<1 || strlen($_POST['summary'])<1 ) {
        $_SESSION['error'] = 'Missing data';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    if ( strpos($_POST['email'],'@') === false ) {
        $_SESSION['error'] = 'Email address must contain @';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    $sql = "UPDATE profile SET first_name = :fn, last_name = :ln,
            email = :em, headline = :hl, summary = :sm
            WHERE profile_id = :profile_id" ;
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':fn' => $_POST['first_name'],
		    ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':hl' => $_POST['headline'],
	      ':sm' => $_POST['summary'],
        ':profile_id' => $_POST['profile_id']));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;


    $profile_id = $pdo->lastinsertID();

    // Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $profile_id));


    $rank = 1;
    for($i=1; $i<=9; $i++) {
      if (!isset($_POST['year'.$i])) continue;
      if (!isset($_POST['desc'.$i])) continue;
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];

      $stmt = $pdo->prepare('INSERT INTO position
        (profile_id, rank, year, description)
         VALUES (:pid,:rank,:year,:desc)');
      $stmt->execute(array(
        ':pid' => $profile_id,
        ':rank'=> $rank,
        ':year'=> $year,
        ':desc'=>$desc
      ));
      $rank++;
}

      if (!is_numeric($_POST['year'.$i])) {
        $_SESSION['error'] = "Position year must be numeric";
        header("location: add.php");
        return;
      } if (strlen($_POST['year'.$i]) ==0 || strlen($_POST['desc'.$i])==0) {
        $_SESSION['error'] = "All feilds are required";
        header("location: add.php");
        return;
      }

}

// Guardian: Make sure that user_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hl = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

?>

<div class="container">
<h1>Editing Profile for UMSI</h1>
<form method="post" action="edit.php">
<input type="hidden" name="profile_id"
value="<?=$profile_id?>"
/>
<p>First Name:
<input type="text" name="first_name" size="60"
value="<?=$fn?>"
/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"
value="<?=$ln?>"
/></p>
<p>Email:
<input type="text" name="email" size="30"
value="<?=$em?>"
/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"
value="<?=$hl?>"
/></p>
<p>Summary:<br/>
<input type="text" name="summary" value = "<?=$sm?>"/></p>


<p>Position: <input type="submit" id="addPos" value="+">
<div id="position_fields">
</div></p>

<p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
countPos = 0;

// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script>
</div>
