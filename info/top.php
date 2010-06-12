<p>
<h1>このサーバについて</h1>
<font color="#FF0000">
ここは「<a href="http://sourceforge.jp/projects/jinrousiki/">人狼式 〜汝は人狼なりや？ 式神研究同好会版</a>」公開テスト専用サーバです。<br>
ログの保全を一切保証しません。<br>
This server is Japanese only. -&gt; <a href="http://sourceforge.jp/projects/jinrousiki/">SourceForge</a>
</font>
</p>

<h1>TOPIC</h1>

<h2>Ver. 1.4.0 β9 アップロード (2010/05/29 (Sat) 05:29:15) → <a href="src/">ダウンロード</a></h2>
<ul>
  <li>Twitter 投稿機能の実装</li>
  <li>BBS 表示機能の実装</li>
  <li>決闘村の配役変更システムの実装</li>
  <li>「執行者」「反魂師」「蝕暗殺者」「河童」「天狼」「土蜘蛛」「犬神」<br>
    「憑狐」「大蝙蝠」「奇術師」「熱病」「自信家」実装
  </li>
  <li>「夢守人」「夢毒者」「獏」「天狐」「妖精系」の仕様変更</li>
</ul>

<h1>開発状況</h1>
<pre>
% 広告BOT緊急対応コード %
room_manager.php % 45行目〜
  //入力データのエラーチェック
  $room_name    = $_POST['room_name'];
  $room_comment = $_POST['room_comment'];
  EscapeStrings($room_name);
  EscapeStrings($room_comment);
  if($room_name == '' || $room_comment == ''){ //未入力チェック
    OutputRoomAction('empty');
    return false;
  }

  //文字列チェック
  $ng_word = '/http:\/\//i';
  if(preg_match($ng_word, $room_name) ||
     preg_match($ng_word, $room_comment)){
    OutputActionResult('村作成 [入力エラー]', '無効な村名・説明です。');
  }
</pre>

<h2>新規実装 / 仕様変更</h2>
<ul>
  <li>なし</li>
</ul>

<h2>現在作成中 / 公開テスト待ち</h2>
<ul>
  <li>人形遣い / 上海人形：他の国で言う貴族 / 奴隷</li>
  <li>死神：投票した相手に死の宣告を付加する暗殺者</li>
  <li>死の宣告：一定日数後に死亡する</li>
</ul>
