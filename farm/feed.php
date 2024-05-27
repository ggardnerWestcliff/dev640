<?php
  require_once 'header.php';

  if (!$loggedin) die("</div></body></html>");

  if (isset($_GET['view'])) $view = sanitizeString($_GET['view']);
  else                      $view = $user;

  if (isset($_POST['text']) and isset($_FILES['file']))
  {
    $text = sanitizeString($_POST['text']);
    if ($text != "")
    {
      $img = file_get_contents($_FILES['file']['tmp_name']);
      $file_type = $_FILES['file']['type'];
      $time = time();
      queryMysql("INSERT INTO posts VALUES(
        NULL, '$user', '$view', $time, '$text', 0x".bin2hex($img).", '$file_type'
        )");
    }
    $_POST = array();
    $_FILES = array();
  }

  if ($view != "")
  {
    if ($view == $user) $name1 = $name2 = "Your";
    else
    {
      $name1 = "<a href='members.php?view=$view&r=$randstr'>$view</a>'s";
      $name2 = "$view's";
    }
    echo "<h4>Create a new post:</h4>";
    echo <<<_END
<form method='post' action='feed.php?view=$view&r=$randstr' enctype="multipart/form-data">
    <label for="img">Select image:</label><br>
    <input type="file" id="img" name="file" accept="image/*" required><br>
    <label for="text">Add a caption:</label><br>
    <textarea name='text'></textarea><br>
    <input data-transition='slide' type='submit' name="submit" value='Publish Post'>
</form>
<br>
_END;

    date_default_timezone_set('UTC');

    if (isset($_GET['erase']))
    {
      $erase = sanitizeString($_GET['erase']);
      queryMysql("DELETE FROM posts WHERE id='$erase' AND recip='$user'");
    }

    echo "<br>";
    $query  = "SELECT * FROM posts ORDER BY time DESC";
    $result = queryMysql($query);
    $num    = $result->rowCount();



    echo "<h1>$name1 Feed</h1>";
    while ($row = $result->fetch())
    {
      if ($row['auth'] == $user || in_array($row['auth'], $friends, true))
      {
        echo " <a href='members.php?view=" . $row['auth'] .
             "&r=$randstr'>" . $row['auth'] . "</a> ";
        echo "[". date('M jS \'y g:ia', $row['time']) . "]:";
        echo "<br>&quot;" . $row['message'] . "&quot;";
        if ($row['auth'] == $user)
          echo "[<a href='feed.php?view=$view" .
              "&erase=" . $row['id'] . "&r=$randstr'>erase</a>]";
        $img_content = base64_encode($row['img']);
        $img_type = $row['file_type'];
        $row_id = $row['id'];
        echo <<<_END
    <br>
    <div class="center">
    <img 
        class="post"
        style="max-width: 500px; max-height: 700px"
        src=data:$img_type;base64,$img_content 
        alt="post-$row_id"
    >
    </div>
<br>
_END;

      }
    }
  }

  if (!$num)
    echo "<br><span class='info'>No posts yet</span><br><br>";

  echo "<br><a data-role='button'
        href='feed.php?view=$view&r=$randstr'>Refresh feed</a>";
?>

    </div><br>
  </body>
</html>
