<?php
require_once "pdo.php";
require_once "util.php";
require_once "bootstrap.php";
session_start();

if(isset($_POST['cancel'])){
header("location: index.php");
return false;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
     && isset($_POST['headline']) && isset($_POST['summary']) && isset($_POST['profile_id'])) {

    // Data validation
    $msg = validateProfile();
    if (is_string($msg) ) {
      $_SESSION['error'] = $msg;
      header("location: edit.php");
      return;
    }

    // POS validation
      $msg = validatePos();
      if (is_string($msg) ) {
        $_SESSION['error'] = $msg;
        header("location: edit.php");
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

        // Clear out the old position entries
        $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

        //Insert Positions
        insertPositions($pdo, $_REQUEST['profile_id']);

        // Clear out the old Education entries
        $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
        $stmt->execute(array(':pid' => $_REQUEST['profile_id']));

        //Insert education
        InsertEducations($pdo, $_REQUEST['profile_id']);

        // Update data
        $_SESSION['success'] = "Profile updated";
        header("Location: index.php");
        return;
}

// Guardian: Make sure that profile_id is present
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

$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hl = htmlentities($row['headline']);
$sm = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

$positions = loadPos($pdo,$_REQUEST['profile_id']);
$educations = loadEdu($pdo, $_REQUEST['profile_id']);
?>
<div class="container">
<h1>Editing Profile for <?=$_SESSION['name']?></h1>
<?php
flashMessages();
 ?>
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


<?php
//education
$countEdu = 0;

echo('<p>Education: <input type="submit" id="addEdu" value="+">'."\n");
echo('<div id="edu_fields">'."\n");
if ( count($educations) > 0 ) {
  foreach ($educations as $education) {
    $countEdu++;
    echo('<div id="edu'.$countEdu.'">');
    echo('<p>Year: <input type="text" name="edu_year'.$countEdu.'" value="'.$education['year'].'"\>
    <input type = "button" value = "-" onclick="$(\'#edu'.$countEdu.'\').remove(); return false;"></p>
    <p>School: <input type = "text" name = "edu_school'.$countEdu.'" value="'.$education['name'].'"/>');
    echo("\n</div>\n");
  }
}
echo("</div></p>\n");

//Position
$countPos = 0;

echo('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
echo('<div id="position_fields">'."\n");
if ( count($positions) > 0 ) {
  foreach ($positions as $position) {
    $countPos++;
    echo('<div id="position'.$countPos.'">');
    echo('<p>Year: <input type="text" name="year'.$countPos.'" value="'.$position['year'].'"\>
    <input type = "button" value = "-" onclick="$(\'#position'.$countPos.'\').remove(); return false;"></p>
    <p>Description: <input type = "text" name = "desc'.$countPos.'" value="'.$position['description'].'"/>');
    echo("\n</div>\n");
  }
}
echo("</div></p>\n");

 ?>

 <p>
 <input type="submit" value="Save">
 <input type="submit" name="cancel" value="Cancel">
 </p>

</form>

<script src="js/jquery-1.10.2.js"></script>
<script src="js/jquery-ui-1.11.4.js"></script>
<script>
});
</script>
<script>
countPos = <?=$countPos?>;
countEdu = <?=$countEdu?>;

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
    // http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
    $(document).ready(function(){
        window.console && console.log('Document ready called');
        $('#addEdu').click(function(event){
            // http://api.jquery.com/event.preventdefault/
            event.preventDefault();
            if ( countEdu >= 9 ) {
                alert("Maximum of nine education entries exceeded");
                return;
            }
            countEdu++;
            window.console && console.log("Adding Education "+countEdu);
            $('#edu_fields').append(
                '<div id="edu'+countEdu+'"> \
                <p>Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
                <input type="button" value="-" \
                    onclick="$(\'#edu'+countEdu+'\').remove();return false;"></p> \
                <p>School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" /></p> \
                </div>');
                $('.school').autocomplete({ source: "school.php" });
        });
});
</script>
</div>
