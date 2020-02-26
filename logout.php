<?php

  require('function.php');

  debug('　ログアウトページ　');
  debug('　ログアウトします。　');

  $_SESSION = array();
  
  if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000);
  }
  session_destroy();

  debug('　ログインページに飛びます。　');
  header("Location:login.php");
  exit;
 ?>
