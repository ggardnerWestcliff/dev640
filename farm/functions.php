  <?php
  $host = '127.0.0.1';
  $data = 'farm';
  $user = 'farm-admin';
  $pass = 'password';
  $chrs = 'utf8mb4';
  $attr = "mysql:host=$host;dbname=$data;charset=$chrs";
  $opts =
      [
          PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES   => false,
      ];

  try {
    $pdo = new PDO($attr, $user, $pass, $opts);
  } catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
  }

  function createTable($name, $query)
  {
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br>";
  }

  function queryMysql($query)
  {
    global $pdo;
    return $pdo->query($query);
  }

  function destroySession()
  {
    $_SESSION=array();

    if (session_id() != "" || isset($_COOKIE[session_name()]))
      setcookie(session_name(), '', time()-2592000, '/');

    session_destroy();
  }

  function sanitizeString($var)
  {
    global $pdo;

    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);

    $result = $pdo->quote($var);          // This adds single quotes
    return str_replace("'", "", $result); // So now remove them
  }

  function showProfile($user)
  {
    global $pdo;

    showProfilePicture($user);

    $result = $pdo->query("SELECT * FROM profiles WHERE user='$user'");

    if (!profilePictureSet($user)) {
      echo "<p>You have not uploaded a profile picture yet.<br>Upload one <a href='profile.php'>here</a>.</p>";
    }

    if ($result->rowCount() > 0) {
      $row = $result->fetch();
      echo "<p>First Name:<br>" . $row['first_name'] . "<br>";
      echo "Last Name:<br>" . $row['last_name'] . "<br>";
      echo "Description:<br>". $row['description'] . "<br></p>";
    } else {
      echo "<p>You have not provided any details about yourself. Update your profile <a href='profile.php'>here</a>.</p><br>";
    }
  }

  function showProfilePicture($user, $with_icon=false): void
  {
    $path = "images/users/";
    $files = glob($path."$user.*");
    if (count($files) > 0) {
      $src_file = $files[0];
    } else {
      $src_file = "images/users/user-default.svg";
    }
    echo "<div class='profile-picture-container'>";
    echo "<img class='user-icon' src=$src_file style='float:left;' alt='$user-profile-picture'>";
    if ($with_icon and count($files) > 0) {
      echo <<<_END
<div class='overlay'>
    <a href="profile.php?erasePicture=1">
        <img src="images/icons-svg/delete-black.svg" class="edit-picture-icon">
    </a>
</div>
_END;
    }
    echo "</div>";
  }

  function profilePictureSet($user) : bool {
    $path = "images/users/";
    $files = glob($path."$user.*");
    return count($files) > 0;
  }

  function clearProfilePictures ($user) : void {
    // Delete any existing files tagged to the user.
    $existing_files = glob("images/users/$user.*");
    foreach ($existing_files as $f) {
      unlink($f);
    }
  }
?>
