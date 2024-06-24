<?php
  if (!isset($_SESSION)) {
    session_start();
  }

echo <<<_INIT
<!DOCTYPE html> 
<html>
  <head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1'> 
    <link rel='stylesheet' href='styles.css' type='text/css'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Freeman">
    <script src='javascript.js'></script>

_INIT;

  require_once 'functions.php';

  $curPageName = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);

  $userstr = 'Welcome Guest';
  $randstr = substr(md5(rand()), 0, 7);

  if (isset($_SESSION['user']))
  {
    $user     = $_SESSION['user'];
    $loggedin = TRUE;
    $userstr  = "Logged in as: $user";

    $friends = array();
    $query = "SELECT friend FROM friends WHERE user='$user' UNION SELECT user FROM friends WHERE friend='$user'";
    $result = queryMysql($query);
    while ($row = $result->fetch()) {
      array_push($friends, $row['friend']);
    }
  }
  else {
    $loggedin = FALSE;
    if (
        $curPageName !== "index.php"
        and $curPageName !== "login.php"
        and $curPageName !== "signup.php"
        and $curPageName !== "forgotpass.php"
    ) {
      header('Location: index.php');
    }
  }

echo <<<_MAIN
    <title>The Farm</title>
  </head>
  <body style="width: auto; height: 100%">
    <div data-role='page' style="height: auto; display: flex; flex-flow: column">
      <div data-role='header'>
        <div id='logo' class='center'>The Farm
        <img class='icon' id='icon' src='images/icons-svg/sheep.svg' alt="the-farm-logo">
        </div>
        <div class='username'>$userstr</div>
      </div>
      <div data-role='content' style="margin: 10px">

_MAIN;

  if ($loggedin) {
    $search_user = "";
    if (isset($_GET["search_user"])) {
      $search_user = sanitizeString($_GET["search_user"]);
      header( "refresh:0;url=members.php?view=$search_user" );
    }
echo <<<_LOGGEDIN
        <div class="header-container">
        <div class="header-container-left"></div>
        <div class='header-container-center'>
          <nav>
            <a data-role='button' data-inline='true' data-transition="slide" href='index.php?r=$randstr'>Home</a>
            <a data-role='button' data-inline='true' data-transition="slide" href='members.php?r=$randstr'>Members</a>
            <a data-role='button' data-inline='true' data-transition="slide" href='friends.php?r=$randstr'>Friends</a>
            <a data-role='button' data-inline='true' data-transition="slide" href='feed.php?r=$randstr'>Feed</a>
            <a data-role='button' data-inline='true' data-transition="slide" href='profile.php?r=$randstr'>Edit Profile</a>
            <a data-role='button' data-inline='true' data-transition="slide" href='logout.php?r=$randstr'>Log out</a>
          </nav>
        </div>
        <div class="header-container-right">
            <form action="$curPageName?r=$randstr" method="GET">
              <input id="search" type="text" placeholder="Type here" name="search_user" value=$search_user>
              <input id="submit" type="submit" value="Search">
            </form>
        </div>
        </div>
        
_LOGGEDIN;
  }
  else
  {
echo <<<_GUEST
        <div class='center'>
        <nav>
          <a data-role='button' data-inline='true' data-icon='home'
            data-transition='slide' href='index.php?r=$randstr''>Home</a>
          <a data-role='button' data-inline='true' data-icon='plus'
            data-transition="slide" href='signup.php?r=$randstr''>Sign Up</a>
          <a data-role='button' data-inline='true' data-icon='check'
            data-transition="slide" href='login.php?r=$randstr''>Log In</a>
        </nav>
        </div>
        <p class='info'>(You must be logged in to use this app)</p>
        
_GUEST;
  }
?>
