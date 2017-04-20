<?php
require_once "pdo.php";

session_start();

if ( ! isset($_SESSION['user_id']) ) {
    die('ACCESS DENIED');
}

if ( isset($_POST['cancel']) ) {
    header('Location: index.php');
    return;
}

if(! isset($_REQUEST["profile_id"])){
    $_SESSION['error'] = 'Missing profile_id';
    header('Location:index.php');
    return;
}

$stmt = $pdo -> prepare('SELECT * FROM Profile WHERE profile_id = :prof AND user_id = :user_id');
$stmt -> execute(array(':prof' => $_REQUEST['profile_id'],
    ':user_id' => $_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ($profile === false){
    $_SESSION['error'] = "Could not load profile";
    header('Location:index.php');
    return;
}

if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) &&isset($_POST['headline']) &&isset($_POST['summary']) ) {
    // $msg = validateProfile();
    // if (is_string($msg)){
    //     $_SESSION["error"]=$msg;
    //     header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
    //     return;
    // }
    //
    // $msg = validatePos();
    // if(is_string($msg)){
    //     $_SESSION["error"] = $msg;
    //     header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
    //     return;
    // }
    for ($i=1; $i<=9; $i++) {
      if(!isset($_POST['year'.$i]))continue;
      if(!isset($_POST['desc'.$i]))continue;
      $year = $_POST['year'.$i];
      $desc = $_POST['desc'.$i];
      if( strlen($year) == 0 || strlen($desc) == 0){
        $_SESSION['error'] = "All fields are required";
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
            return;
      }if( !is_numeric($year)){
        $_SESSION['error'] = "Position year ust be numeric";
        header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
            return;
      }
    }



    $stmt = $pdo->prepare('UPDATE Profile SET first_name=:fn,last_name=:ln,email=:em,headline=:he,summary=:su WHERE profile_id = :pid AND user_id=:user_id');
    $stmt->execute(array(
        ':pid' => $_REQUEST["profile_id"],
        ':user_id' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );

// Clear out the old position entries
    $stmt = $pdo->prepare('DELETE FROM Position
        WHERE profile_id=:pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

    $rank = 1;
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        $stmt = $pdo->prepare('INSERT INTO Position
            (profile_id, rank, year, description)
        VALUES ( :pid, :rank, :year, :desc)');
        $stmt->execute(array(
            ':pid' => $_REQUEST['profile_id'],
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
        );
        $rank++;
    }


    $_SESSION['success']="Profile updated";
    header("location:index.php");
    return;
}

// $positions = loadPos($pdo, $_REQUEST['profile_id']);
$stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :xyz ORDER BY rank");
$stmt->execute(array(":xyz" => $_REQUEST['profile_id']));
$positions = array();
while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
  $position[] = $row;
}


?>
<html>
 <head>
    <?php require_once "bootstrap.php"; ?>
    <style type="text/css">
    body {
        margin-left: 10%;
    }
    </style>
    <title>Huanchen Lu</title>
 </head>
 <body>
<h1>Edit Profile for <?php echo $_SESSION['name']?> </h1>
<?php
if ( isset($_SESSION['error']))
    {
     echo('<p style="color:red">'.$_SESSION['error']."</p>\n");
     unset($_SESSION['error']);
    }

if ( isset($_SESSION['success']))
    {
      echo('<p style="color:green">'.$_SESSION['success']."</p>\n");
      unset($_SESSION['success']);
    } ?>

<form method="post" action = 'edit.php'>
    <input type="hidden" name="profile_id" value="<?= htmlentities($_REQUEST['profile_id']); ?>"/>
    <p>First Name:<input type="text" name="first_name" size="60" value="<?= htmlentities($profile['first_name']) ?>"/></p>
    <p>Last Name:<input type="text" name="last_name" size="60" value="<?= htmlentities($profile['last_name']) ?>"/></p>
    <p>Email:<input type="text" name="email" size="60" value="<?= htmlentities($profile['email']) ?>"/></p>
    <p>Headline:<br/>
    <input type="text" name="headline" size="60" value="<?= htmlentities($profile['headline']) ?>"/></p>
    <p>Summary:<br/>
    <textarea rows="8" cols="80" name="summary" ><?= htmlentities($profile['summary']) ?></textarea>
    </p>
    <?php
    $countPos = 0;
    echo ('<p>Position:<input type="submit" id="addPos" value="+">'."\n");
    echo ('<div id="position_fields">'."\n");
    if (count($positions) > 0){
        foreach($positions as $position){
            $countPos++;
            echo('<div class="position" id="position'.$countPos.'">');
            echo('<p>Year: <input type="text" name="year'.$countPos.'" value="'.$position['year'].'" />
            <input type="button" value="-" onclick="$(\'#position'.$countPos.'\').remove();return false;"></p>');
            echo '<textarea name="desc'.$countPos .'" rows="8" cols="80">'."\n";
            echo htmlentities($position['description'])."\n";
            echo "\n</textarea>\n</div>\n";
        }
    }
    echo "</div></p>\n";
    ?>
    <input type="submit" value="Save">
    <input type="submit" name="cancel" value="Cancel">
</form>


<script type="text/javascript">
countPos = <?= $countPos ?>;
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        event.preventDefault();
        if (countPos >= 9){
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
            window.console && console.log("Adding position"+countPos);
            $('#position_fields').append(
                '<div id="position' + countPos +'">\
                <p>Year: <input type="text" name="year' + countPos + '" value="" />\
                <input type="button" value="-" \
                onclick="$(\'#position' + countPos +'\').remove();return false;"></p>\
                <textarea name="desc'+countPos +'" rows="8" cols="80"></textarea>\
                </div>');
    });
});
</script>
</body>
</html>
