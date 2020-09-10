<?php
// to check the user entered by method login button.
if (isset($_POST['login-submit'])) {

  require 'dbh.inc.php';

  // data from signup.inc.php
  $mailuid = $_POST['mailuid'];
  $password = $_POST['pwd'];
//error handlers.
  if (empty($mailuid) || empty($password)) {
    header("Location: ../index.php?error=emptyfields&mailuid=".$mailuid);
    exit();
  }
  else {

    //no error bitchess

    //code to get info from database and conncet it to the database...soo sql time..

    $sql = "SELECT * FROM users WHERE uidUsers=? OR emailUsers=?;";
    // new statement from dbh.inc.php.
    $stmt = mysqli_stmt_init($conn);
    // sql error check.
    if (!mysqli_stmt_prepare($stmt, $sql)) {
      // If there is an error we send the user back to the signup page.
      header("Location: ../index.php?error=sqlerror");
      exit();
    }
    else {

      //login info collection.
      mysqli_stmt_bind_param($stmt, "ss", $mailuid, $mailuid);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      //  store the result into a variable.
      if ($row = mysqli_fetch_assoc($result)) {
        // to check the password is right
        $pwdCheck = password_verify($password, $row['pwdUsers']);
        //  error message!
        if ($pwdCheck == false) {
          // back to signup.
          header("Location: ../index.php?error=wrongpwd");
          exit();
        }
        else if ($pwdCheck == true) {
          session_start();
          // And NOW we create the session variables.
          $_SESSION['id'] = $row['idUsers'];
          $_SESSION['uid'] = $row['uidUsers'];
          $_SESSION['email'] = $row['emailUsers'];
          // Now the user is registered as logged in and we can now take them back to the front page! :)
          header("Location: ../index.php?login=success");
          exit();
        }
      }
      else {
        header("Location: ../index.php?login=wronguidpwd");
        exit();
      }
    }
  }
  // close the prepared statement and the database connection!
  mysqli_stmt_close($stmt);
  mysqli_close($conn);
}
else {
  // If the user tries to access this page an inproper way, we send them back to the signup page.
  header("Location: ../signup.php");
  exit();
}
