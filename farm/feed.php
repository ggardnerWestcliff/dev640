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
    echo <<<_END
<div class="two-col">
<div class="col1" style="border: 1px solid black">
<h1><u>Create A New Post</u></h1>
<form method='post' action='feed.php?view=$view&r=$randstr' enctype="multipart/form-data">
    <label for="img">Select image:</label><br>
    <input type="file" id="img" name="file" accept="image/*" required><br>
    <label for="text">Add a caption:</label><br>
    <textarea name='text' style="width: 80%"></textarea><br>
<input data-transition='slide' type='submit' name="submit" value='Publish Post'>
</form>
<br>
<h1><u>Suggested Friends</u></h1>
_END;

    date_default_timezone_set('UTC');

    if (isset($_GET['erase']))
    {
      $erase = sanitizeString($_GET['erase']);
      queryMysql("DELETE FROM posts WHERE id='$erase' AND recip='$user'");
    }

    $suggestedFriendsQuery = <<<_FRIENDS
    SELECT
    DISTINCT
    m.user
    , COUNT(DISTINCT p.id) as posts
    FROM members m
    LEFT JOIN posts p ON m.user = p.auth
    WHERE m.user NOT IN (
        SELECT
        DISTINCT
        f.friend
        FROM friends f
        WHERE f.user = '$user'
    )
    AND m.user != '$user'
    GROUP BY m.user
    ORDER BY m.user
_FRIENDS;
    $result = queryMysql($suggestedFriendsQuery);
    $suggestedFriendsCount    = $result->rowCount();
    while ($row = $result->fetch()) {
      echo "<a href='members.php?view=" . $row['user'] . "&r=$randstr'>" . $row['user'] . "</a>" . " (" . $row['posts'] . " posts)<br>";
    }
    echo "</div>";


    $postsQuery  = <<<_POSTS
    SELECT
    DISTINCT
    p.*
    FROM posts p 
    LEFT JOIN friends f on p.auth = f.user
    WHERE p.auth = '$user' OR f.friend = '$user'
    ORDER BY time DESC
_POSTS;
    $result = queryMysql($postsQuery);
    $postCount    = $result->rowCount();



    echo "<div class='col2' style='border: 1px solid black'><h1><u>$name1 Feed</u></h1>";
    while ($row = $result->fetch())
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

  if (!$postCount)
    echo "<br><span class='info'>No posts yet</span><br><br>";

  echo "<br><a data-role='button'
        href='feed.php?view=$view&r=$randstr'>Refresh feed</a>";
?>

    </div>
    </div>
  </body>
</html>
