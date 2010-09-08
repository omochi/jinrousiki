<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('SCRIPT_INFO', 'MENU_LINK', 'BBS_CONF');
OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment, 'index');
echo "</head>\n<body>\n";
if($SERVER_CONF->back_page != ''){
  echo '<a href="' . $SERVER_CONF->back_page . '">←戻る</a><br>'."\n";
}
?>
<a href="./"><img src="img/top_title.jpg"></a>
<div class="comment"><?php echo $SERVER_CONF->comment ?></div>
<noscript>&lt;&lt; JavaScriptを有効にしてください &gt;&gt;</noscript>
<table class="main"><tr>
<td>
  <div class="menu">メニュー</div>
  <ul>
    <li><a href="info/script_info.php">特徴と仕様</a></li>
    <li><a href="info/rule.php">ゲームのルール</a></li>
    <li><a href="info/chaos.php">闇鍋モード</a></li>
    <li><a href="info/new_role/">新役職情報</a></li>
    <li><a href="info/shared_room.php">関連サーバ村情報</a></li>
    <li><a href="info/">その他の情報一覧</a></li>
    <li>★☆★☆★☆★</li>
    <li><a href="old_log.php">ログ閲覧</a></li>
    <li>★☆★☆★☆★</li>
    <li><a href="icon_view.php">アイコン一覧</a></li>
    <li><a href="icon_upload.php">アイコン登録</a></li>
    <li>★☆★☆★☆★</li>
    <li><a href="src/">ソースコードダウンロード</a></li>
  </ul>
  <?php $MENU_LINK->Output() ?>
</td>
<td>
  <fieldset>
    <legend>Information <a href="info/history/top.php">〜過去のinformationはこちら〜</a></legend>
    <div class="information"><?php include_once 'info/top.php' ?></div>
  </fieldset>
  <fieldset>
    <legend>ゲーム一覧</legend>
    <div class="game-list"><?php include_once 'room_manager.php' ?></div>
  </fieldset>
  <fieldset>
    <legend>村の作成</legend><?php OutputCreateRoomPage() ?>
  </fieldset>
</td>
</tr></table>

<div class="footer"><?php $SCRIPT_INFO->OutputVersion() ?></div>
</body></html>
