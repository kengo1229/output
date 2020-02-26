<?php
  require('function.php');

  require('loginCheck.php');

  debug('　ログインページ　');

  if(!empty($_POST)){

    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    debug('POST送信がありました');

    validRequire($email,'email');
    validRequire($pass,'pass');

   if(empty($err_msg)){
     debug('未入力チェッククリア');
  // 6文字以上チェック
     validOverSix($pass,'pass');
  // 20文字以下チェック
     validUnderTwenty($pass,'pass');
  // 半角英数字チェック
     validHalf($pass,'pass');
  // Eメール形式チェック
     validEmail($email,'email');
     // Eメール最大文字数チェック
     validMaxLen($email,'email');
   }

   if(empty($err_msg)){
     debug('バリデーションOK');

     try {

       $dbh = dbConnect();
       $sql = 'SELECT pass,id FROM users WHERE email = :email';
       $data = array(':email' => $email);

       $stmt = queryPost($dbh,$sql,$data);
       $result = $stmt->fetch(PDO::FETCH_ASSOC);

       if(!empty($result) && password_verify($pass,array_shift($result))){
         debug('パスワードが一致しました');

         $sesLimit = 60*60;

         $_SESSION['login_time'] = time();

         if($pass_save){
           debug('ログイン保持にチェックがあります。');
           // ログイン保持にチェックがある場合、有効期限を1週間に設定
           $_SESSION['login_limit'] = $sesLimit*24*7;
         }else{
           debug('ログイン保持にチェックはありません。');
           $_SESSION['login_limit'] = $sesLimit;
         }

           $_SESSION['user_id'] = $result['id'];
         debug('セッション情報：'.print_r($_SESSION,true));
         debug('マイページへ飛びます');
         header('Location:mypage.php');
         exit;
       }else{
         debug('パスワードが不一致でした。');
         $err_msg['common'] = MSG10;
       }
     } catch (Exception $e){
       error_log($e->getMessage());
       $err_msg['common'] = MSG09;
     }
   }
  }
 ?>
 <?php
  $title = 'ログイン';
  require('head.php');
  ?>
  <body id="signup" class="color-grray">
    <main>
      <div class="form-container">
        <a href="index.php">
          <h1>ShokuReQu</h1>
        </a>
        <form id="form-wrapper"  action="" method="post">
          <h2>ログイン</h2>
          <div class="area-msg <?php getErrClass('common'); ?>">
            <div class="err-msg">
              <?php echo getErrMsg('common'); ?>
            </div>
          </div>
          <div class="signup-form">
            <label class="area-msg <?php getErrClass('email'); ?>">
              <div class="err-msg">
              <?php if(!empty($err_msg['email'])) echo getErrMsg('email'); ?>
              </div>
              メールアドレス
              <input type="text" name="email" value="<?php echo getFormData('email') ?>"><br>
            </label>
            <label class="area-msg <?php getErrClass('pass'); ?>">
              <div class="err-msg" >
              <?php if(!empty($err_msg['pass'])) echo getErrMsg('pass'); ?>
              </div>
              パスワード:半角英数字6文字以上20文字以下
              <input type="password" name="pass" value="<?php echo getFormData('pass') ?>"><br>
            </label>
            <label>
            <input type="checkbox" name= "pass_save" id="save-login">
              ログインを保持する
            </label>
            <p id="linked-signup"><a href="signup.php">新規登録はこちら</a></p>
            <div class="btn-wrapper">
              <input class="btn" type="submit" name="submit" value="ログイン">
            </div>
          </div>
        </form>
      </div>
    </main>

    <?php
      require('footer.php');
     ?>
