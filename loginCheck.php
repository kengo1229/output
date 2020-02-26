<?php

  if(!empty($_SESSION['login_time'])){
    debug('ログイン済みユーザーです。');
    debug('user_id：'.print_r($_SESSION['user_id'],true));
    debug('login_time：'.print_r($_SESSION['login_time'],true));
    debug('login_limit：'.print_r($_SESSION['login_limit'],true));

    if($_SESSION['login_time'] + $_SESSION['login_limit'] <time()){
      debug('ログイン有効期限オーバーです。');

      session_destroy();

      header("Location:login.php");
    }else{
      debug('ログイン有効期限以内です。');

      $_SESSION['login_time'] = time();
// 無限ループ回避
      if(basename($_SERVER['PHP_SELF']) === 'login.php'){
        debug('トップページへ遷移します。');
        header("Location:index.php");
        exit;
      }
    }
  }else{
    debug('未ログインユーザーです。');

    if(basename($_SERVER['PHP_SELF']) !== 'login.php'){
      debug('トップページへ遷移します。');
      header("Location:login.php");
      exit;
    }

  }
 ?>
