<?php
  require_once 'header.php';

  $redirect_time = 3;
echo <<<_END
  <script>
    function checkUser(user)
    {
      if (user.value == '')
      {
        $('#used').html('&nbsp;')
        return
      }

      $.post
      (
        'checkuser.php',
        { user : user.value },
        function(data)
        {
          $('#used').html(data)
        }
      )
    }
  </script>
_END;

  $error = $user = $pass = $sq_one = $sq_two = "";
  if (isset($_SESSION['user'])) destroySession();

  if (isset($_POST['user'])) {
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);
    $sq_one = sanitizeString($_POST['sq_one']);
    $sq_two = sanitizeString($_POST['sq_two']);

    if ($user == "" || $pass == "" || $sq_one == "" || $sq_two == "")
      $error = 'Not all fields were entered<br><br>';
    else
    {
      $result = queryMysql("SELECT * FROM members WHERE user='$user'");

      if ($result->rowCount())
        $error = 'That username already exists<br><br>';
      else
      {
        queryMysql("INSERT INTO members VALUES('$user', '$pass', '$sq_one', '$sq_two')");
        header( "refresh:$redirect_time;url=index.php" );
        $_SESSION['user'] = $user;
        $_SESSION['pass'] = $pass;
        die("<h4>Account created</h4>Redirecting you in $redirect_time seconds.</div></body></html>");
      }
    }
  }

echo <<<_END
      <form method='post' action='signup.php?r=$randstr'>$error
        <label></label>
        Please enter your details to sign up
      </div>
      <div class="two-col">
      <div data-role='fieldcontain' class="col1">
      <div data-role='fieldcontain'>
        <label>Username</label><br>
        <input type='text' maxlength='16' name='user' value='$user' required
          onBlur='checkUser(this)'>
        <label></label><div id='used'>&nbsp;</div>
      </div>
      </div>
      <div class="col2">
      <div data-role='fieldcontain'>
        <label>Password</label><br>
        <input type='password' maxlength='16' name='pass' value='$pass' required>
      </div>
      </div>
      </div>
      <div class="two-col">
      <div data-role='fieldcontain' class="col1">
      <div data-role='fieldcontain'>
      <label>What was your favorite subject in school?</label><br>
        <input type='password' maxlength='16' name='sq_one' value='$sq_one' required>
      </div></div>
      <div class="col2">
      <div data-role='fieldcontain'>
      <label>What is your favorite fruit?</label><br>
        <input type='password' maxlength='16' name='sq_two' value='$sq_two' required>
      </div>
          <label></label>
          <input data-transition='slide' type='submit' value='Sign Up'>
      </div>
      </div>
      </form>
    </div>
  </body>
</html>
_END;
?>
