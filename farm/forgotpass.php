<?php
require_once 'header.php';
$error = $user = $new_pass = $sq_one = $sq_two = "";
$redirect_time = 3;

if (isset($_POST['user']))
{
    $user = sanitizeString($_POST['user']);
    $sq_one = sanitizeString($_POST['sq_one']);
    $sq_two = sanitizeString($_POST['sq_two']);
    $new_pass = sanitizeString($_POST['new_pass']);

    if ($user == "" || $new_pass == "" || $sq_one == "" || $sq_two == "")
        $error = 'Not all fields were entered';
    else {
        $result = queryMySQL("SELECT user,sq_one,sq_two FROM members
        WHERE UPPER(user) = UPPER('$user')
        AND UPPER(sq_one) = UPPER('$sq_one')
        AND UPPER(sq_two) = UPPER('$sq_two')");

        if ($result->rowCount() == 0) {
            $error = "Invalid attempt.";
        }
        else {
            queryMysql("UPDATE members SET pass = '$new_pass' 
            WHERE user='$user' AND sq_one='$sq_one' AND sq_two='$sq_two'");
            $_SESSION['user'] = $user;
            $_SESSION['pass'] = $new_pass;
            header( "refresh:$redirect_time;url=login.php" );
            die(
            "<div class='center'>Password updated. Re-directing in $redirect_time seconds...</div></div></body></html>"
            );
        }
    }
}

echo <<<_END
      <form method='post' action='forgotpass.php?r=$randstr'>
        <div data-role='fieldcontain'>
          <label></label>
          <span class='error'>$error</span>
        </div>
        <div data-role='fieldcontain'>
          <label></label>
          Please enter your details to log in
        </div>
        <div data-role='fieldcontain'>
          <label>Username</label><br>
          <input type='text' maxlength='16' name='user' value='$user'>
        </div>
        <div data-role='fieldcontain'>
          <label>What was your favorite subject in school?</label><br>
            <input type='password' maxlength='16' name='sq_one' value='$sq_one' required>
        </div>
        <div data-role='fieldcontain'>
          <label>What is your favorite fruit?</label><br>
            <input type='password' maxlength='16' name='sq_two' value='$sq_two' required>
        </div>
        <div data-role='fieldcontain'>
          <label>New Password</label><br>
            <input type='password' maxlength='16' name='new_pass' value='$new_pass' required>
        </div>
        <div data-role='fieldcontain'>
          <label></label>
          <input data-transition='slide' type='submit' value='Update Password'>
        </div>
      </form>
    </div>
  </body>
</html>
_END;
?>
