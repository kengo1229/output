<?php

  require('function.php');

  debug('　トップページ　');

  $currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
  debug('現在のページ：'.$currentPageNum);
  // GETパラメータにジャンルidがある場合、それを取得する。なかったら、空をつめる
  $genre = (!empty($_GET['g_id'])) ? $_GET['g_id'] : '';
  // GETパラメータに都道府県の指定がある場合、それを取得する。なかったら、空をつめる
  $pref = (!empty($_GET['pref_id'])) ? $_GET['pref_id'] : '';
  // GETパラメータにソートの指定がある場合、それを取得する。なかったら、空をつめる
  $sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
  //1ページに表示するリクエスト件数
  $listSpan = 10;
  // ページの最初に表示するリクエストの番数
  $currentMinNum = (($currentPageNum-1)*$listSpan);

  $dbRequestData = getRequestList($currentMinNum,$genre,$pref,$sort);
  debug('$dbRequestDataの中身'.print_r($dbRequestData,true));

  $dbGenreData = getGenre();

  $dbPrefData = getPref();
 ?>

<?php
 $title = 'トップページ';
 require('head.php');
 ?>
  <body>

  <?php
  require('header.php');
   ?>
    <main>

      <section id="sidebar">
        <form name="" method="get">
          <h1 class="title">ジャンル</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="g_id" id="">
              <!-- getFormDataにc_idを渡してそれを基に表示させている（） -->
              <option value="0" <?php if(getSelectData('g_id') == 0){echo 'selected';} ?> >選択してください</option>
              <?php
              foreach($dbGenreData as $key => $val){
               ?>
              <option value="<?php echo $val['id']; ?>" <?php if(getSelectData('g_id') == $val['id']){echo 'selected';} ?>>
                <?php echo sanitize($val['name']);  ?>
              <?php } ?>
              </option>
            </select>
          </div>
          <h1 class="title">都道府県</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="pref_id" id="">
              <!-- getFormDataにpref_idを渡してそれを基に表示させている（） -->
              <option value="0" <?php if(getSelectData('pref_id') == 0){echo 'selected';} ?>>選択してください</option>
              <?php
              foreach($dbPrefData as $key => $val){
               ?>
              <option value="<?php echo $val['id']; ?>" <?php if(getSelectData('pref_id') == $val['id']){echo 'selected';} ?>>
                <?php echo sanitize($val['pref_name']);  ?>
              <?php } ?>
              </option>
            </select>
          </div>
          <h1 class="title">表示順</h1>
          <div class="selectbox">
            <span class="icn_select"></span>
            <select name="sort">
              <option value="0"<?php if(getSelectData('sort') == 0){ echo 'selected'; } ?>>選択してください</option>
              <option value="1"<?php if(getSelectData('sort') == 1){ echo 'selected'; } ?>>予算が少ない順</option>
              <option value="2"<?php if(getSelectData('sort') == 2){ echo 'selected'; } ?>>予算が多い順</option>
            </select>
          </div>
          <input type="submit" value="検索">
        </form>

      </section>

    <div id="request-rist-container">
      <h1>リクエスト一覧</h1>
      <div id="request-rist-wrapper">
        <table align="center">
          <thead>
            <tr class="border-bottom">
              <th class="text-center">食べたいもの</th>
              <th class="text-center">ジャンル</th>
              <th class="text-center">予算</th>
              <th class="text-center">場所</th>
              <th>コメント</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($dbRequestData['data'] as $key => $val){?>
              <tr class="border-bottom">
                <td class="content-title"><?php echo mb_strimwidth(sanitize($val['topic']),0,30,'...','utf-8'); ?></td>
                <td class="content-genre"><?php echo sanitize($val['name']); ?></td>
                <td class="content-price">¥<?php echo sanitize(number_format($val['price'])); ?>円</td>
                <td id="pref_city" class="content-area" value=""><?php echo sanitize($val['pref_name']).$val['city']; ?></td>
                <td><a href="requestDetail.php<?php echo '?r_id='.$val['id']; ?>" title="コメントを投稿する"><i class="far fa-comment-dots"></i></a></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
      <?php pagination($currentPageNum, $dbRequestData['total_page'],$link='&g_id='.$genre.'&pref_id='.$pref.'&sort='.$sort); ?>


    </main>

    <?php
      require('footer.php');
     ?>
