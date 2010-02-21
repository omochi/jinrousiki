<?php
require_once('include/init.php');
$INIT_CONF->LoadClass('SCRIPT_INFO');
OutputHTMLHeader($SERVER_CONF->title . $SERVER_CONF->comment, 'index');
echo "</head>\n<body>\n";
if($SERVER_CONF->back_page != ''){
  echo '<a href="' . $SERVER_CONF->back_page . '">←戻る</a><br>'."\n";
}
?>
<a href="./"><img src="img/top_title.jpg"></a>
<div class="comment"><?= $SERVER_CONF->comment ?></div>
<noscript>＜＜ JavaScriptを有効にしてください ＞＞</noscript>
<table class="main">
  <tr><td>
    <div class="menu">メニュー</div>
    <ul>
      <li><a href="script_info.php">特徴と仕様</a></li>
      <li><a href="rule.php">ゲームのルール</a></li>
      <li><a href="info/chaos.php">闇鍋モード</a></li>
      <li><a href="info/new_role/">新役職情報</a></li>
      <li><a href="info/">その他の情報一覧</a></li>
      <li>★☆★☆★☆★</li>
      <li><a href="old_log.php">ログ閲覧</a> (<a href="old_log.php?add_role=on">+ 役職表示</a>)</li>
<!-- さとり鯖用
      <li class="log">HTML化ログ</li>
      <li>さとり鯖V3ログ</li>
      <li><a href="log3/">1〜378村</a></li>
      <li><a href="log3_2/">379村〜</a></li>
      <li><a href="log2/">さとり鯖V2ログ</a></li>
      <li><a href="log1/">さとり鯖V1ログ</a></li>
-->
      <li>★☆★☆★☆★</li>
      <li><a href="icon_view.php">アイコン一覧</a></li>
      <li><a href="icon_upload.php">アイコン登録</a></li>
      <li>★☆★☆★☆★</li>
      <li><a href="src/">ソースコードダウンロード</a></li>
      <li><a href="http://sourceforge.jp/projects/jinrousiki/">SourceForge</a></li>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50">開発・バグ報告スレ</a></li>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50">新役職提案スレ</a></li>
    </ul>

    <div class="menu">交流用サイト</div>
    <ul>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1260623018/l50">本スレ</a>(告知はここ)</li>
      <li><a href="http://www27.atwiki.jp/umigamejinnro/">東方ウミガメwiki</a></li>
      <li><a href="http://jbbs.livedoor.jp/netgame/2829/">ウミガメ人狼掲示板</a></li>
      <li><a href="http://umigamejinrou.chatx2.whocares.jp/">ウミガメ雑談村</a></li>
      <!-- <li><a href="http://konoharu.sakura.ne.jp/umigame/yychat/yychat.cgi">旧ウミガメ雑談村</a></li> -->
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1224519836/l50">反省・議論用スレ</a></li>
    </ul>

    <div class="menu">外部リンク</div>
    <ul>
      <li>東方ウミガメ系</li>
      <li><a href="http://alicegame.dip.jp/sanae/">早苗鯖</a></li>
      <!-- <li><a href="http://satori.crz.jp/">さとり鯖</a></li> -->
      <li><a href="http://www7.atpages.jp/izayoi398/">咲夜鯖</a></li>
      <li><a href="http://www12.atpages.jp/cirno/">チルノ鯖（開発チーム）</a></li>
      <li>やる夫系</li>
      <li><a href="http://www16.atpages.jp/sasugabros/">流石弟者鯖</a></li>
      <li><a href="http://alicegame.dip.jp/suisei/">翠星石鯖</a></li>
      <li><a href="http://www37.atwiki.jp/yaruomura/">やる夫wiki</a></li>
      <li>やる夫系予備/保管庫鯖</li>
      <li><a href="http://www12.atpages.jp/yaruo/jinro/">流石兄弟鯖</a></li>
      <li><a href="http://alicegame.dip.jp/sousei/">蒼星石テスト鯖</a></li>
      <li><a href="http://www13.atpages.jp/yaranai/">薔薇姉妹鯖</a></li>
      <li><a href="http://www13.atpages.jp/suigintou/">水銀鯖</a></li>
      <li><a href="http://www15.atpages.jp/kanaria/">金糸雀保管庫</a></li>
      <li><a href="http://www14.atpages.jp/mmr1/">世紀末鯖</a></li>
      <!-- <li><a href="http://www15.atpages.jp/seikima2/jinro_php/">世紀末鯖（テスト鯖）</a></li> -->
      <li>東方陰陽鉄系</li>
      <li><a href="http://bourbonhouse.xsrv.jp/jinro/">バーボンハウス鯖</a></li>
      <li><a href="http://dynamis.xsrv.jp/jinro/">裏世界鯖</a></li>
      <li><a href="http://www16.atpages.jp/bourbonjinro/">旧バーボンハウス鯖</a></li>
      <li>iM@S系</li>
      <li><a href="http://kiterew.tv/jinro/">小鳥鯖</a></li>
      <li>バーボン鯖系</li>
      <li><a href="http://jinro.blue-sky-server.com/">猫又鯖</a></li>
      <li><a href="http://www.freedom.xii.jp/jinro/">バーボン鯖</a></li>
      <li>リンク希望募集中</li>
    </ul>
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
    <?php OutputSharedServerRoom() ?>
    <fieldset>
      <legend>村の作成</legend><?php OutputCreateRoomPage() ?>
    </fieldset>
  </td></tr>
</table>

<div class="footer">
[PHP4 + MYSQLスクリプト　<a href="http://p45.aaacafe.ne.jp/~netfilms/" target="_blank">配布ホームページ</a>]
[システム　<a href="http://sourceforge.jp/projects/mbemulator/" target="_blank">mbstringエミュレータ</a>]<br>
[写真素材　<a href="http://keppen.web.infoseek.co.jp/" target="_blank">天の欠片</a>
　<a href="http://moineau.fc2web.com/" target="_blank">Le moineau - すずめのおやど -</a>
<!-- チルノ鯖のみで使用
　<a href="http://moineau.fc2web.com/" target="_blank">Le moineau - すずめのおやど -</a>
-->
]
[フォント素材　<a href="http://azukifont.mints.ne.jp/" target="_blank">あずきフォント</a>]<br>
[アイコン素材　
 <a href="http://natuhotaru.yukihotaru.com/" target="_blank">夏蛍</a>
 <a href="http://jigizagi.s57.xrea.com/" target="_blank">ジギザギのさいはて</a>
]<br>
<!-- チルノ鯖で使用
[アイコン素材　
 <a href="http://natuhotaru.yukihotaru.com/" target="_blank">夏蛍</a>
 <a href="http://jigizagi.s57.xrea.com/" target="_blank">ジギザギのさいはて</a>
]<br>
-->
<!-- さとり鯖用
[アイコン素材　
 <a href="http://natuhotaru.yukihotaru.com/" target="_blank">夏蛍</a>
 <a href="http://jigizagi.s57.xrea.com/" target="_blank">ジギザギのさいはて</a>
 <a href="http://www.geocities.jp/nwqkp334/" target="_blank">ガタウ屋</a>
 <a href="http://www21.tok2.com/home/foxy/" target="_blank">Foxy〜狐色〜</a>
 <a href="http://kukyo.hp.infoseek.co.jp/" target="_blank">ぶらんけっと</a>
 <a href="http://www8.plala.or.jp/denpa/indexdon.html" target="_blank">神楽丼</a>
]<br>
-->
<?php $SCRIPT_INFO->OutputVersion() ?>
</div>
</body></html>
