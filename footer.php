<footer id="footer">
  ©️ <a href="">ShokuReQu</a> All Rights Reserved.
</footer>

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript" src="js/jquery.balloon.js"></script>


<script>
  $(function(){
    // footerまでの高さを一定に保つ
    var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    }
    // パスワードの表示・非表示切り替え
    $('.toggle-password').click(function(){
      // fontawesomeの入れ替え
      $(this).toggleClass('fa-eye fa-eye-slash');
      // input['type']の入れ替え
      var $input = $(this).parent().prev('input');
      if($input.attr('type') == 'password'){
        $input.attr('type','text');
      }else{
        $input.attr('type','password');
      }
    })
    // 文字数カウンター(request.phpの食べたいもの)
    $('#js-count-one').keyup(function(){
      var $count_one = $(this).val().length;
      $('.length-one').text($count_one);

      if($count_one > 40){
        $(this).parent('label').addClass('err');
        $(this).prevAll('.err-msg').text('文字数制限を超えています。');
      }else{
        $(this).parent('label').removeClass('err');
        $(this).prevAll('.err-msg').text('');
      }
    })
    // 文字数カウンター(request.phpのその他)
    $('#js-count-two').keyup(function(){
      var $count_two = $(this).val().length;
      $('.length-two').text($count_two);

      if($count_two > 200){
        $(this).parent('label').addClass('err');
        $(this).prevAll('.err-msg').text('文字数制限を超えています。');
      }else{
        $(this).parent('label').removeClass('err');
        $(this).prevAll('.err-msg').text('');
      }
    })

    
    // コメントアイコンマウスオーバー時の吹き出し
    $('a').balloon({position: "top",showDuration: 0, hideDuration:0,minLifetime:0})

  });

</script>

</body>
</html>
