<?php // Do not put any HTML above this line
require_once "pdo.php";
session_start();


$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Pw is meow123


if ( isset($_POST["email"]) && isset($_POST["pass"]) ) {
     unset($_SESSION["account"]);
     $check = hash('md5', $salt.$_POST['pass']);
     $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pw');
    $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
     if ( $row !== false ) {
         $_SESSION['name'] = $row['name'];
         $_SESSION['user_id'] = $row['user_id'];
         // Redirect the browser to index.php
         header("Location: index.php");
         return;
      }if(strpos($_POST['email'],'@') == false){
          $_SESSION['error'] = "Email must have an at-sign (@)";
          header("Location: login.php");
          return;
      }else {
          $_SESSION['error'] = "Incorrect password";
          header("Location: login.php");
          return;
}
}
if ( isset($_POST['cancel'] ) ) {
  // Redirect the browser to game.php
  header("Location: index.php");
  return;
}

if ( isset($_SESSION['success']) ) {
  echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
  unset($_SESSION['success']);
}
// Check to see if we have some POST data, if we do process it
// if ( isset($_POST['who']) && isset($_POST['pass']) ) {
//     if ( strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1 ) {
//         $failure = "User name and password are required";
//     }
//     else {
//         $check = hash('md5', $salt.$_POST['pass']);
//         if (strpos($_POST['who'],'@') == false){
//             $failure = 'Email must have an at-sign (@)';
//         }else {
//               if ( $check == $stored_hash ) {
//               // Redirect the browser to game.php
//               header("Location: view.php?name=".urlencode($_POST['who']));
//               error_log("Login success ".$_POST['who']);
//               exit();
//         }
//             $failure = "Incorrect password";
//             error_log("Login fail ".$_POST['who']." $check");
//
//         }
//     }
// }

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Huanchen Lu's Resume Registry</title>
</head>
<body>
<div class="container">
<h1>Huanchen Lu's Resume Registry</h1>
<?php
// Note triple not equals and think how badly double
// not equals would work here...
if ( isset($_SESSION['error']) ) {
    echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
    unset($_SESSION['error']);
}
?>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        pw = document.getElementById('id_1723').value;
        console.log("Validating pw="+pw);
        if (pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
<form method="POST">
User Name <input type="text" name="email"><br/>
Password <input type="password" name="pass" id="id_1723"></br>
<!-- <label for="nam">User Name</label>
<input type="text" name="email" id="name"><br/>
<label for="id_1723">Password</label>
<input type="text" name="pass" id="id_1723"><br/> -->
<input type="submit" onclick="return doValidate();" value="Log In">
<a href = 'index.php'>Cancel</a>
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the four character sound a cat
makes (all lower case) followed by 123. -->
</p>
</div>
</body>
