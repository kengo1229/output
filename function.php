<?php
require('database.php');
// エラーログ出力設定
ini_set('log_errors','on');
ini_set('error_log','php.log');

// エラーログ関数
$debug_flg = true;
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

// セッションファイル
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime',60*60*24*30);
ini_set('session.cookie_lifetime',60*60*24*30);
session_start();
// セッションidを常に新しいものに更新する。なりすまし対策
session_regenerate_id();

// エラーメッセージ定義
define('MSG01','未入力です。');
define('MSG02','20文字以内で入力してください。');
define('MSG03','Eメールの形式ではありません。');
define('MSG04','6文字以上で入力してください。');
define('MSG05','半角英数字で入力してください。');
define('MSG06','255文字以内で入力してください。');
define('MSG07','入力いただいたユーザーネームはご使用いただけません。');
define('MSG08','入力いただいたメールアドレスはご使用いただけません。');
define('MSG09','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG10','メールアドレスまたはパスワードが違います。');
define('MSG11','40文字以内で入力してください。');
define('MSG12','半角数字で入力してください。');
define('MSG13','200文字以内で入力してください。');
// エラーメッセージ変数
$err_msg = array();

// バリデーション関数
// 未入力チェック
function validRequire($str,$key){
  if(empty($str)){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}
// 20文字以下チェック
function validUnderTwenty($str,$key){
  if(mb_strlen($str) > 20){
    global $err_msg;
    $err_msg[$key] = MSG02;
  }
}
// 40文字以下チェック
function validUnderForty($str,$key){
  if(mb_strlen($str) > 40){
    global $err_msg;
    $err_msg[$key] = MSG11;
  }
}
// 200文字チェック
function validUnderTwoh($str,$key){
  if(mb_strlen($str) > 200){
    global $err_msg;
    $err_msg[$key] = MSG13;
  }
}
// メール形式チェック
function validEmail($str,$key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}
// 6文字以上チェック
function validOverSix($str,$key){
  if(mb_strlen($str) < 6){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}
// 半角英数字チェック
function validHalf($str,$key){
  if(!preg_match("/^[a-zA-Z0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG05;
  }
}
// 半角数字チェック
function validNumber($str,$key){
  if(!preg_match("/^[0-9]+$/",$str)){
    global $err_msg;
    $err_msg[$key] = MSG12;
  }
}
// 最大文字数チェック
function validMaxLen($str,$key){
  if(mb_strlen($str) > 255){
    global $err_msg;
    $err_msg[$key] = MSG06;
  }
}
// Eメール重複チェック
function validEmailDup($email){
  global $err_msg;

  try {

    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM users WHERE email = :email';
    $data = array(':email' => $email);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG08;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' .$e->getMessage());
    $err_msg['common'] = MSG09;
  }
}
// ユーザーネーム重複チェック
function validNamelDup($username){
  global $err_msg;

  try {

    $dbh = dbConnect();
    $sql = 'SELECT count(*) FROM users WHERE username = :username';
    $data = array(':username' => $username);
    $stmt = queryPost($dbh,$sql,$data);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!empty(array_shift($result))){
      $err_msg['username'] = MSG07;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' .$e->getMessage());
    $err_msg['common'] = MSG09;
  }
}

  // sql実行
  function queryPost($dbh,$sql,$data){

    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute($data)){
      debug('クエリに失敗しました。');
      debug('失敗したSQL：'.print_r($stmt,true));
      global $err_msg;
      $err_msg['common'] = MSG09;
      return 0;
    }

    debug('クエリ成功。');
    return $stmt;
  }
  // errクラス追加
  function getErrClass($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
      echo 'err';
    }
  }
  // エラーメッセージ表示
  function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
      echo $err_msg[$key];
    }
  }
  // ジャンルの名前を取ってくる
  function getGenre(){

    try {
      $dbh = dbConnect();
      $sql = 'SELECT id,name FROM genre';
      $data = array();

      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
        debug('ジャンル情報取得に成功しました');
        return $stmt->fetchAll();
        debug('ジャンル情報取得に成功しました');
      }else{
        debug('ジャンル情報取得に失敗しました');
        return false;
      }

    } catch (Exception $e){
      error_log('エラー発生:' .$e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
  // 都道府県名を取ってくる
  function getPref(){

    try {
      $dbh = dbConnect();
      $sql = 'SELECT id,pref_name FROM pref';
      $data = array();

      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
        debug('都道府県情報取得に成功しました');
        return $stmt->fetchAll();
      }else{
        debug('都道府県情報取得に失敗しました');
        return false;
      }

    } catch (Exception $e){
      error_log('エラー発生:' .$e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
  //dbに登録されているリクエストの情報を取得する
  function getRequestList($currentMinNum = 1 ,$genre , $pref , $sort , $span = 10){

    try {

      $dbh = dbConnect();
      // requestテーブルにある全id取得する
      $sql = 'SELECT id FROM request';
      // ジャンル・都道府県・検索順の指定があれば、sqlに足す
      if(!empty($genre) && !empty($pref)){
        $sql.=' WHERE genre = '.$genre.' AND pref = '.$pref;
      }elseif(!empty($genre)){
        $sql.=' WHERE genre = '.$genre;
      }elseif(!empty($pref)){
        $sql.=' WHERE pref = '.$pref;
      }

      if(!empty($sort)){
      switch($sort){
          case 1:
          // 昇順
            $sql .= ' ORDER BY price ASC';
            break;
          case 2:
          // 降順
            $sql .= ' ORDER BY price DESC';
            break;
        }
      }
      $data = array();

      $stmt = queryPost($dbh,$sql,$data);
      // 取得したデータ総数をrowCount使って格納
      $result['total'] = $stmt->rowCount();
      // データ総数を10で割って切り上げた数(総ページ数)を格納
      $result['total_page'] = ceil($result['total']/$span);

      if(!$stmt){
        return flase;
      }

      $sql = 'SELECT r.id,r.topic,r.price,r.genre,r.pref,r.city,r.other,r.user_id,g.name,p.pref_name FROM request AS r LEFT JOIN genre as g ON r.genre = g.id
              LEFT JOIN pref as p ON p.id = r.pref ';

              if(!empty($genre) && !empty($pref)){
                $sql.=' WHERE genre = '.$genre.' AND pref = '.$pref;
              }elseif(!empty($genre)){
                $sql.=' WHERE genre = '.$genre;
              }elseif(!empty($pref)){
                $sql.=' WHERE pref = '.$pref;
              }

      if(!empty($sort)){
        switch($sort){
          case 1:
          // 昇順
            $sql .= ' ORDER BY price ASC';
            break;
          case 2:
          // 降順
            $sql .= ' ORDER BY price DESC';
            break;
        }
      }

      $sql .=' LIMIT '.$span.' OFFSET '.$currentMinNum;
      $data = array();

      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
      // 取得した全データを格納する
        $result['data'] = $stmt->fetchAll();
        return $result;
      }else{
        return false;
      }
    } catch (Exception $e){
      error_log('エラー発生:' .$e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
  //dbに登録されている自分のリクエストの情報を取得する
  function getMYRequestList($u_id,$currentMinNum = 1 , $span = 10){

    try {

      $dbh = dbConnect();
      // requestテーブルにある全id取得する
      $sql = 'SELECT id FROM request WHERE user_id = :u_id';
      $data = array(':u_id' => $u_id);

      $stmt = queryPost($dbh,$sql,$data);
      // 取得したデータ総数をrowCount使って格納
      $result['total'] = $stmt->rowCount();
      // データ総数を10で割って切り上げた数(総ページ数)を格納
      $result['total_page'] = ceil($result['total']/$span);

      if(!$stmt){
        return flase;
      }

      $sql = 'SELECT id,topic, create_date FROM request WHERE user_id = :u_id'.' LIMIT '.$span.' OFFSET '.$currentMinNum;
      $data = array(':u_id' => $u_id);

      $stmt = queryPost($dbh,$sql,$data);

      if($stmt){
      // 取得した全データを格納する
        $result['data'] = $stmt->fetchAll();
        return $result;
      }else{
        return false;
      }
    } catch (Exception $e){
      error_log('エラー発生:' .$e->getMessage());
      $err_msg['common'] = MSG08;
    }
  }
// 特定のリクエスト情報を取得する
function getRequestOne($r_id){

  try {

    $dbh = dbConnect();

    $sql = 'SELECT r.id,r.topic,r.price,r.genre,r.pref,r.city,r.other,r.user_id,g.name,p.pref_name,u.username FROM request AS r LEFT JOIN genre as g ON r.genre = g.id
            LEFT JOIN pref as p ON p.id = r.pref LEFT JOIN users AS u ON r.user_id = u.id  WHERE r.id = :r_id ';

    $data = array(':r_id' => $r_id);

    $stmt = queryPost($dbh,$sql,$data);
    // 取得したデータ総数をrowCount使って格納

    if($stmt){
      return $stmt -> fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生:' .$e->getMessage());
    $err_msg['common'] = MSG08;
  }
}
// 自分のリクエスト情報を取得する
function getMYRequest($u_id){

  try {

    $dbh = dbConnect();

    $sql = 'SELECT id,topic, create_date FROM request WHERE user_id = :u_id';

    $data = array(':u_id' => $u_id);

    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      debug('自分のリクエスト情報取得成功');
      return $stmt -> fetchAll();
    } else{
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生:' .$e->getMessage());
    $err_msg['common'] = MSG08;
  }
}
// 特定リクエストのコメント数を取得する
function getCommentNumber($r_id){

  try {

    $dbh = dbConnect();

    $sql = 'SELECT c.id FROM comment as c LEFT JOIN board AS b ON c.board_id = b.id  WHERE b.request_id = :r_id';

    $data = array(':r_id' => $r_id);

    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      debug('コメント数取得成功');
      return $stmt -> rowCount();
    }else{
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生:' .$e->getMessage());
    $err_msg['common'] = MSG08;
  }
}
// 特定の掲示板情報を取得する
function getBoardOne($r_id){

  try {

    $dbh = dbConnect();

    $sql = 'SELECT id FROM board WHERE request_id = :r_id';

    $data = array(':r_id' => $r_id);

    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      debug('掲示板id取得成功');
      return $stmt -> fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生:' .$e->getMessage());
    $err_msg['common'] = MSG08;
  }
}
// 特定リクエストへの全コメントを取得する
function getComment($r_id){

  try {

    $dbh = dbConnect();

    $sql = 'SELECT  c.comment , c.send_date , u.username FROM comment AS c LEFT JOIN users AS u ON c.send_user = u.id LEFT JOIN board AS b ON c.board_id = b.id WHERE b.request_id = :r_id ORDER BY c.send_date DESC ';

    $data = array(':r_id' => $r_id);

    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      debug('コメント取得成功');
      return $stmt -> fetchAll();
    }else{
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生:' .$e->getMessage());
    $err_msg['common'] = MSG08;
  }
}
// 最新コメントの投稿日時を取得する
function newCommentTime($r_id){

  try {

    $dbh = dbConnect();

    $sql = 'SELECT  c.send_date FROM comment AS c LEFT JOIN board AS b ON c.board_id = b.id WHERE b.request_id = :r_id ORDER BY c.send_date DESC ';

    $data = array(':r_id' => $r_id);

    $stmt = queryPost($dbh,$sql,$data);

    if($stmt){
      debug('最新コメント日時取得成功：');
      return $stmt -> fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
  } catch (Exception $e){
    error_log('エラー発生:' .$e->getMessage());
    $err_msg['common'] = MSG08;
  }
}
// フォームに入力した値の保持
function getFormData($str){

  if(!empty($_POST[$str])){
    return $_POST[$str] ;
  }
}
// セレクトで選択した値の保持
function getSelectData($str){

  if(!empty($_GET[$str]))
  return $_GET[$str];
}
// サニタイズ出力
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}
// ページネーション
function pagination($currentPageNum, $totalPageNum,$link = '',$pageColNum = 5){
  // 現在のページが、総ページ数と同じでかつ、総ページ数が表示ページ数以上の場合、左に４つだす
  if( $currentPageNum == $totalPageNum && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum -4;
    $maxPageNum = $currentPageNum;
  // 現在ページ数が総ページ-1の場合、左に３つ右に１つだす
  }elseif($currentPageNum == ($totalPageNum-1) && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum -3;
    $maxPageNum = $currentPageNum +1;
  // 現在ページ数が2の場合、左に1つ右に3つだす
}elseif($currentPageNum == 2 && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum -1;
    $maxPageNum = $currentPageNum +3;
  // 現在ページが１のとき、右に４つだす
}elseif($currentPageNum == 1 && $totalPageNum >= $pageColNum){
    $minPageNum = $currentPageNum;
    $maxPageNum = 5;
  // 総ページ数が表示ページ数よりも少ない場合、一番左は1、一番右は総ページ数
}elseif($totalPageNum < $pageColNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  // それ以外は左に２つ右に２つだす
}else{
    $minPageNum = $currentPageNum -2;
    $maxPageNum = $currentPageNum +2;
}
// echo使ってhtmlタグの呼び出し
echo '<div class="pagination">';
  echo '<ul class="pagination-list">';
// 現在ページが１ページ目でない時、左端に１ページ目に飛ぶ＜を表示する
// デフォルトで''を指定している$linkをURLの後ろにくっつけているのはindex.phpのGETパラメータにproductDetailの情報もくっついているから
  if($currentPageNum != 1 && $totalPageNum > $pageColNum){
    echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
  }
  // 最小ページから最大ページまで{}の処理を繰り返す
  for($i = $minPageNum; $i <= $maxPageNum; $i++){
    echo '<li class="list-item ';
    if($currentPageNum == $i){ echo 'active';}
    echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
  }
  // 現在ページが表示ページ数の最後でない時、右端に最終ページに飛ぶ＞を表示する
  if($currentPageNum != $totalPageNum && $totalPageNum > $pageColNum){
    echo '<li class="list-item"><a href="?p='.$totalPageNum.$link.'">&gt;</a></li>';
  }
  echo '</ul>';
echo '</div>';
}
 ?>
