<header>
    <div class="left-wrapper">
      <a href="index.php">
        <h1>ShokuReQu</h1>
      </a>
    </div>
    <div class="right-wrapper">
      <?php  if(empty($_SESSION['user_id'])){
         ?>
      <a href="login.php">
        <div class="right-wrapper-left">
          ログイン
        </div>
      </a>
      <a href="signup.php">
        <div class="right-wrapper-right">
          新規登録
        </div>
      </a>
    <?php }else{ ?>
      <a href="logout.php">
        <div class="right-wrapper-left">
          ログアウト
        </div>
      </a>
      <a href="mypage.php">
        <div class="right-wrapper-right">
          マイページ
        </div>
      </a>
    <?php } ?>
    </div>
</header>
