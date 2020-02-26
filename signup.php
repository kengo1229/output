<?php

require('function.php');

// フォームの各項目の変数作成
debug('　ユーザー登録ページ　');

if(!empty($_POST)){

  $username = $_POST['username'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];

  debug('POST送信がありました');
// 未入力チェック
  validRequire($username,'username');
  validRequire($email,'email');
  validRequire($pass,'pass');

 if(empty($err_msg)){
   debug('未入力チェッククリア');
// 20文字以下チェック
   validUnderTwenty($username,'username');
   validUnderTwenty($pass,'pass');
// 6文字以上チェック
   validOverSix($pass,'pass');
// 半角英数字チェック
   validHalf($pass,'pass');
// Eメール形式チェック
   validEmail($email,'email');
   // Eメール最大文字数チェック
   validMaxLen($email,'email');
 }
 if(empty($err_msg)){
   // 重複チェック
   validEmailDup($email);
   validNamelDup($username);
 }

 debug('バリデーションOK');
 if(empty($err_msg)){

   try {

     $dbh = dbConnect();
     $sql = 'INSERT INTO users (username,email,pass,login_time,create_date) VALUES (:username,:email,:pass,:login_time,:create_date)';
     $data = array(':username' => $username , ':email' => $email , ':pass' => password_hash($pass,PASSWORD_DEFAULT) , ':login_time' => date("Y/m/d H:i:s") , ':create_date' => date("Y/m/d H:i:s") );

     $stmt = queryPost($dbh,$sql,$data);

     debug('ユーザー登録成功');

     if(!empty($stmt)){

       $sesLimit = 60*60;
       $_SESSION['login_time'] = time();
       $_SESSION['login_limit'] = $sesLimit;
       $_SESSION['user_id'] = $dbh->lastInsertId();

       debug('セッション情報：'.print_r($_SESSION,true));
       debug('マイページへ飛びます');
       header('Location:mypage.php');
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
  $title = 'ユーザー登録';
  require('head.php');
  ?>
  <body id="signup" class="color-grray">
    <main>
      <div class="form-container">
        <a href="index.php">
          <h1>ShokuReQu</h1>
        </a>
        <form id="form-wrapper"  action="" method="post">
          <h2>ユーザー登録</h2>
          <div class="area-msg <?php  getErrClass('common'); ?>">
            <?php if(!empty($err_msg['common']))  getErrMsg('common'); ?>
          </div>
          <div class="signup-form">
            <label class="area-msg  <?php  getErrClass('username'); ?>">
              <div class="err-msg">
                <?php if(!empty($err_msg['username'])) echo getErrMsg('username'); ?>
              </div>
              ユーザーネーム:20文字以下（必須）
              <input type="text" name="username" value="<?php echo getFormData('username') ?>" placeholder="例：食リク君"><br>
            </label>
            <label class="area-msg  <?php   getErrClass('email'); ?>">
              <div class="err-msg">
                <?php if(!empty($err_msg['email'])) echo getErrMsg('email'); ?>
              </div>
              メールアドレス(必須)
              <input type="text" name="email" value="<?php echo getFormData('email') ?>" placeholder="例：mail@shokurequ.be"><br>
            </label>
            <label class="area-msg  <?php  getErrClass('pass'); ?>">
              <div class="err-msg">
                <?php if(!empty($err_msg['pass'])) echo getErrMsg('pass'); ?>
              </div>
              パスワード:半角英数字6文字以上20文字以下(必須)
              <p>
                <input type="password" name="pass" value="<?php echo getFormData('pass') ?>" placeholder="例：aaaaaaaaaaa">
                <span class="field-icon"><i class="fas fa-eye-slash toggle-password"></i></span>
              </p>
            </label>
            <div class="btn-wrapper">
              <input class="btn" type="submit" name="submit" value="登録する">
            </div>
          </div>
        </form>
      </div>
    </main>

    <?php

    require('footer.php');

    ?>
