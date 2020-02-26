<?php

  require('function.php');

  require('loginCheck.php');

  debug('　マイページ　');

  $u_id = $_SESSION['user_id'];
  //
  $currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
  //1ページに表示するリクエスト件数
  $listSpan = 10;
  // ページの最初に表示するリクエストの番数
  $currentMinNum = (($currentPageNum-1)*$listSpan);

  // 自分が投稿したリクエスト情報を変数に格納
  $dbMYRequestData = getMYRequestList($u_id,$currentMinNum);
  debug('取得した自分のリクエスト情報：'.print_r($dbMYRequestData,true));

 ?>

<?php
 $title = 'マイページ';
 require('head.php');
 ?>
  <body>

  <?php
  require('header.php');
   ?>
    <main>


    <div id="request-rist-container">
      <h1>投稿したリクエスト</h1>
      <div id="request-rist-wrapper">
        <table align="center">
          <thead>
            <tr class="border-bottom">
              <th>食べたいもの</th>
              <th>投稿日時</th>
              <th>コメント数</th>
              <th>最新コメント日時</th>
              <th>コメント</th>
            </tr>
          </thead>
          <tbody>
                <?php foreach($dbMYRequestData['data'] as $key => $val){ ?>
                <tr class="border-bottom">
                <td><?php echo mb_strimwidth(sanitize($val['topic']),0,28,'...','utf-8'); ?></td>
                <td><?php echo sanitize($val['create_date']); ?></td>
                <td><?php echo sanitize(getCommentNumber($val['id'])); ?></td>
                <td><?php echo sanitize(newCommentTime($val['id'])['send_date']); ?></td>
                <td><a href=<?php echo 'requestDetail.php?r_id='.sanitize($val['id']); ?> title="コメントを投稿する"><i class="far fa-comment-dots"></i></a></td>
              </tr>
                <?php } ?>
          </tbody>
        </table>
      </div>
      <?php pagination($currentPageNum, $dbMYRequestData['total_page']); ?>
    </div>
  </div>
  <a href="request.php">
    <div id="request-button">
      <i class="fas fa-pen"><br><span id="under-letter">リクエスト</span></i>
    </div>
  </a>

    </main>


    <?php
      require('footer.php');
     ?>
