<?php
  require_once 'header.php';

  if (!$loggedin) die("</div></body></html>");

  echo "<h3>Your Profile</h3>";

  $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");
  $first_name = null;
  $last_name = null;
  $description = null;

  if ($result->rowCount()) {
    $row = $result->fetch();
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $description = $row['description'];
  } else {
    echo "You have not set any profile details yet.";
  }

  if (isset($_POST['submit']))
  {
    $first_name = sanitizeString($_POST['firstName']);
    $last_name = sanitizeString($_POST['lastName']);
    $description = sanitizeString($_POST['description']);

    if ($result->rowCount()) {
      queryMysql("UPDATE profiles SET
                      first_name='$first_name',
                      last_name='$last_name',
                      description='$description'
                  where user='$user'"
      );
    }
    else queryMysql("INSERT INTO profiles VALUES(
                              '$user', '$first_name', '$last_name', '$description'
                              )");
  }

  if (isset($_GET['erasePicture'])) {
    clearProfilePictures($user);
  }

  if (isset($_FILES['image']['name']) and $_FILES['image']['name'] !== "")
  {
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $saveto = "images/users/$user.$ext";

    clearProfilePictures($user);

    // Move the new file into the active directory.
    move_uploaded_file($_FILES['image']['tmp_name'], $saveto);
  }

  showProfilePicture($user, true);
  echo <<<_END
<form method='post' action='profile.php?view=$user&r=$randstr' enctype="multipart/form-data">
    <label for="img">Upload a new profile picture:</label><br>
    <input type="file" id="image" name="image" accept="image/*"><br>
    <label for="firstName">First Name:</label><br>
    <input type="text" name="firstName" id="firstName" value="$first_name"><br>
    <label for="lastName">Last Name:</label><br>
    <input type="text" name="lastName" id="lastName" value="$last_name"><br>
    <label for="description">Tell us more about your interests</label><br>
    <textarea name='description' id="description">$description</textarea><br>
    <input data-transition='slide' type='submit' name="submit" value='Update details'>
</form>
<br>
_END;

    

?>
