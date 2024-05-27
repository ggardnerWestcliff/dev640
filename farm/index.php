<?php
  session_start();
  require_once 'header.php';

  echo "<div class='center'>Welcome to The Farm";

  if ($loggedin) {
    echo " $user!<br>";
    echo <<<_END
Check out your <a href="feed.php?view=$user">feed</a> or edit your <a href="profile.php">profile</a>.
_END;
    echo "</div>";
    showProfile($user, $user);

  }
  else           echo ' please sign up or log in';

  echo <<<_END
      <br>
    </div>
    <div data-role="footer" style="vertical-align: bottom; text-align: center">
      <h4>A Web App from <i><a href='https://github.com/ggardnerWestcliff/dev640'
      target='_blank'>DEV 640</a></i></h4>
    </div>
  </body>
</html>
_END;
?>
