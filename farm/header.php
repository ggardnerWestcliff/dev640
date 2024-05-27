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
  else $loggedin = FALSE;

echo <<<_MAIN
    <title>The Farm: $userstr</title>
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

  if ($loggedin)
  {
echo <<<_LOGGEDIN
        <div class='center'>
          <a data-role='button' data-inline='true' data-icon='home'
            data-transition="slide" href='members.php?view=$user&r=$randstr'>Home</a>
          <a data-role='button' data-inline='true' data-icon='user'
            data-transition="slide" href='members.php?r=$randstr'>Members</a>
          <a data-role='button' data-inline='true' data-icon='heart'
            data-transition="slide" href='friends.php?r=$randstr'>Friends</a>
          <a data-role='button' data-inline='true' data-icon='mail'
            data-transition="slide" href='feed.php?r=$randstr'>Feed</a>
          <a data-role='button' data-inline='true' data-icon='edit'
            data-transition="slide" href='profile.php?r=$randstr'>Edit Profile</a>
          <a data-role='button' data-inline='true' data-icon='action'
            data-transition="slide" href='logout.php?r=$randstr'>Log out</a>
        </div>
        
_LOGGEDIN;
  }
  else
  {
echo <<<_GUEST
        <div class='center'>
          <a data-role='button' data-inline='true' data-icon='home'
            data-transition='slide' href='index.php?r=$randstr''>Home</a>
          <a data-role='button' data-inline='true' data-icon='plus'
            data-transition="slide" href='signup.php?r=$randstr''>Sign Up</a>
          <a data-role='button' data-inline='true' data-icon='check'
            data-transition="slide" href='login.php?r=$randstr''>Log In</a>
        </div>
        <p class='info'>(You must be logged in to use this app)</p>
        
_GUEST;
  }
?>
