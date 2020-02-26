  <?php

  require('function.php');


  debug('　コメントページ　');

  $r_id = (!empty($_GET['r_id'])) ? $_GET['r_id'] : '' ;
  debug('リクエストID：'.$r_id);

  // 特定のリクエストデータを変数に格納
  $getRequestData = getRequestOne($r_id);
  debug('取得したリクエストデータ：'.print_r($getRequestData,true));

  // リクエストへの全コメントデータを変数に格納する
  $getCommentData = getComment($r_id);
  debug('取得したコメント：'.print_r($getCommentData,true));
  // 掲示板のidを変数に格納
  $getBoardId = getBoardOne($r_id);
  debug('取得した掲示板id：'.print_r($getBoardId,true));

  if(!empty($_POST)){
    debug('POST送信がありました');

    require('loginCheck.php');

    $comment = (!empty($_POST['comment'])) ? $_POST['comment'] : '';

    validRequire($comment,'comment');

    validMaxLen($comment,'comment');


    if(empty($err_msg)){
      debug('バリデーションOK');

      try{

        $dbh = dbConnect();

        $sql = 'INSERT INTO comment (send_user,board_id,comment,send_date,create_date) VALUES (:s_user,:b_id,:comment,:s_date,:date)';
        debug(':s_user:'.print_r($_SESSION['user_id'],true));
        debug(':b_id:'.print_r($getBoardId));
        debug(':comment:'.print_r($comment,true));

        $data = array(':s_user' => $_SESSION['user_id'] , ':b_id' => $getBoardId['id'] , ':comment' => $comment ,':s_date' => date('Y-m-d H:i:s'),':date' => date('Y-m-d H:i:s')) ;

        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
          $_POST = array();
          debug('コメントページへ遷移します。');
          header("Location:" . $_SERVER['PHP_SELF'].'?r_id='.$r_id);
          exit;
        }
      } catch (Exception $e){
        error_log('エラー発生:' . $e->getMessage());
        $err_log['common'] = MSG07;
      }
    }


  }
  ?>

  <?php
  $title = 'リクエスト詳細';
  require('head.php');
  ?>

  <?php
  require('header.php');
  ?>


  <main>
    <div id="request-container">
      <div id="request-info">
        リクエスト内容<br>
        リクエストしたユーザー：<?php echo sanitize($getRequestData['username']);  ?> <br>
        食べたいもの：<?php echo sanitize($getRequestData['topic']);  ?><br>
        予算：¥<?php echo sanitize(number_format($getRequestData['price'])); ?>円　ジャンル：<?php echo sanitize($getRequestData['name']) ?>　
        場所：<?php echo sanitize($getRequestData['pref_name'].$getRequestData['city']); ?><br>
        その他：<?php echo sanitize(!empty($getRequestData['other'])) ? $getRequestData['other'] : '特になし';  ?>
      </div>
      <div id="comment-container">
        <div id="comment-wrapper">
          <?php foreach($getCommentData as $key => $val){ ?>
          <div class="comment-group">
            <?php echo sanitize($val['username']) ; ?><br>
            <?php echo sanitize($val['send_date']) ; ?>
            <p><?php echo sanitize($val['comment']) ; ?></p>
          </div>
          <?php } ?>
        </div>
     </div>
     <div id="comment-form">
      <form class="" action="" method="post">
        <label class="area-msg <?php getErrClass('comment'); ?>">
          <div class="err-msg">
            <?php if(!empty($err_msg['comment'])) echo getErrMsg('comment'); ?>
          </div>
          コメント
          <textarea name="comment"><?php echo getFormData('comment') ?></textarea><br>
        </label>
        <input id="js-submit" type="submit" name="submit" value="送信">
      </form>
     </div>
  </div>
  </main>

  <?php
    require('footer.php');
   ?>
