  <?php

  require('function.php');

  require('loginCheck.php');

  debug('　リクエスト投稿ページ　');

  $dbGenreData = getGenre();
  $dbPrefData =  getPref();


  if(!empty($_POST)){
    debug('POST送信がありました');

    $topic = $_POST['topic'];
    $genre = $_POST['genre'];
    $price = $_POST['price'];
    $pref = $_POST['pref'];
    $city = $_POST['city'];
    $other =  $_POST['other'];

    // 未入力チェック
    validRequire($topic,'topic');
    validRequire($genre,'genre');
    validRequire($price,'price');
    validRequire($pref,'pref');

    if(empty($err_msg)){
    debug('未入力チェッククリア');

    //トピックの文字数チェック
    validUnderForty($topic,'topic');
    // その他の文字数チェック
    validUnderTwoh($other,'other');
    // 価格の半角数字チェック
    validNumber($price,'price');
    // 市町村の文字数チェック
    validUnderTwenty($city,'city');

    }
    if(empty($err_msg)){
      debug('バリデーションOK');

      try {

        $dbh = dbConnect();
        $sql = 'INSERT INTO request (topic,price,genre,pref,city,other,user_id,create_date) VALUES (:topic , :price , :genre , :pref, :city , :other , :user_id , :create_date )';
        $data = array(':topic' => $topic , ':price' => $price , ':genre' => $genre , ':pref' => $pref , ':city' => $city , ':other' => $other , ':user_id' => $_SESSION['user_id'] , ':create_date' => date('Y/m/d H:i:s') );

        $stmt = querypost($dbh,$sql,$data);
        debug('リクエスト投稿成功');

        $sql = 'INSERT INTO board (request_user,request_id,create_date) VALUES (:r_user,:r_id,:date)';
        $data = array(':r_user' => $_SESSION['user_id'],':r_id' => $dbh->lastInsertID() ,':date' => date('Y-m-d H:i:s'));

        $stmt = queryPost($dbh,$sql,$data);

        if($stmt){
          debug('掲示板作成成功');

          debug('マイページへ遷移します');
          header("Location:mypage.php");
          exit;
        }
      } catch (Exception $e){
        error_log('エラー発生:' .$e->getMessage());
        $err_msg['common'] = MSG08;
      }
    }
    }
   ?>

   <?php
   $title = 'リクエスト投稿';
   require('head.php');
    ?>

   <?php
   require('header.php');
    ?>

    <main id="request-page">
      <div class="form-container">
        <form id="form-wrapper"  action="" method="post">
          <div class="signup-form">
            <h2>リクエスト投稿</h2>
            <div class="area-msg <?php  getErrClass('common'); ?>">
              <?php if(!empty($err_msg['common']))  getErrMsg('common'); ?>
            </div>
            <label class="area-msg <?php  getErrClass('topic'); ?>">
              <div class="err-msg">
                <?php   getErrMsg('topic'); ?>
              </div>
              食べたいもの:40文字以下（必須）<div class="text-counter"><span class="length-one">0</span>/40</div>
              <input type="text" id="js-count-one" name="topic" value="<?php echo getFormData('topic') ?>" placeholder="ニンニクがたっぷり効いたラーメン">
              </label>
            <label class="area-msg <?php getErrClass('genre'); ?>">
              <div class="err-msg">
                <?php   getErrMsg('genre'); ?>
              </div>
              ジャンル（必須）<br>
              <select id="genre" name="genre">
                <option value="0" selected>選択してください</option>
                <?php foreach($dbGenreData as $key => $val){?>
                <option value="<?php echo $val['id']; ?>"<?php if(getFormData('genre') == $val['id']) {echo 'selected' ;} ?>><?php echo $val['name']; ?></option>
                <?php } ?>
              </select>
            </label>
            <label class="area-msg <?php  getErrClass('price'); ?>">
              <div class="err-msg">
                <?php   getErrMsg('price'); ?>
              </div>
              予算（必須）<br>
              <input class="price" type="text" name="price" value="<?php echo getFormData('price') ?>" placeholder="1,000"> 円<br>
            </label>
            <label class="area-msg <?php  getErrClass('pref'); ?>">
              場所<br>
              <div class="err-msg">
                <?php   getErrMsg('pref'); ?>
              </div>
              都道府県（必須）<br>
              <select class="area" name="pref">
                <option value="0" selected>選択してください</option>
                <?php foreach($dbPrefData as $key => $val){ ?>
                <option value="<?php echo $val['id']; ?>"<?php if(getFormData('pref') == $val['id']) {echo 'selected' ;} ?>><?php echo $val['pref_name']; ?></option>
                <?php } ?>
              </select><br>
            </label>
            <label class="area-msg <?php  getErrClass('city'); ?>">
              <div class="err-msg">
                <?php   getErrMsg('city'); ?>
              </div>
              市区町村（任意）<br>
              <input class="area" type="text" name="city" value="<?php echo getFormData('city') ?>">
            </label>
            <label class="area-msg  <?php  getErrClass('other'); ?>">
              <div class="err-msg">
                <?php   getErrMsg('other'); ?>
              </div>
              その他:200文字以下（任意）<div class="text-counter"><span class="length-two">0</span>/200</div>
              <textarea id="js-count-two"class="other" name="other"  placeholder="山形駅から徒歩15分圏内"><?php echo getFormData('other') ?></textarea>
            </label>
            <input type="submit" name="" value="投稿">
            <div class="btn-wrapper">
            </div>
          </div>
        </form>
      </div>

    </main>

    <?php
      require('footer.php');
     ?>
