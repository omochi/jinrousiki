<?php require_once('include/setting.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" href="css/index.css">
<title><?php echo $SERVER_CONF->title . $SERVER_CONF->comment; ?></title>
</head>
<body>
<?php if($SERVER_CONF->back_page != '') echo "<a href=\"$SERVER_CONF->back_page\">←戻る</a>"; ?>
<a href="index.php"><img src="img/top_title.jpg"></a>
<div class="comment"><?php echo $SERVER_CONF->comment; ?></div>
<noscript>＜＜ JavaScriptを有効にしてください ＞＞</noscript>
<table class="main">
  <tr><td>
    <div class="menu">メニュー</div>
    <ul>
      <li><a href="script_info.php">特徴と仕様</a></li>
      <li><a href="rule.php">ゲームのルール</a></li>
      <li><a href="info/history.php">更新履歴</a></li>
      <li><a href="old_log.php">ログ閲覧</a></li>
      <li><a href="old_log.php?add_role=on">役職表示ログ閲覧</a></li>
      <li>★☆★☆★☆★</li>
      <li><a href="info/new_role.php">新役職について</a></li>
      <li><a href="info/chaos.php">闇鍋モードについて</a></li>
<!-- さとり鯖用
      <li>★☆★☆★☆★</li>
      <li class="log">HTML化ログ</li>
      <li>さとり鯖V3ログ</li>
      <li><a href="log3/index.html">1〜378村</a></li>
      <li><a href="log3_2/index.html">379村〜</a></li>
      <li><a href="log2/index.html">さとり鯖V2ログ</a></li>
      <li><a href="log1/index.html">さとり鯖V1ログ</a></li>
-->
      <li>★☆★☆★☆★</li>
      <li><a href="icon_view.php">アイコン一覧</a></li>
      <li><a href="icon_upload.php">アイコン登録</a></li>
      <li>★☆★☆★☆★</li>
      <!-- <li><a href="paparazzi.php">デバッグモード</a></li> -->
      <li><a href="src/">開発版ソースダウンロード</a></li>
      <li><a href="src/diff.txt">開発履歴</a></li>
      <li><a href="http://sourceforge.jp/projects/jinrousiki/">SourceForge</a></li>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1240771280/l50">開発・バグ報告スレ</a></li>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/netgame/2829/1246414115/l50">新役職提案スレ</a></li>
    </ul>

    <div class="menu">交流用サイト</div>
    <ul>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1252065305/l50">本スレ</a>(告知はここ)</li>
      <li><a href="http://www27.atwiki.jp/umigamejinnro/">東方ウミガメwiki</a></li>
      <li><a href="http://jbbs.livedoor.jp/netgame/2829/">ウミガメ人狼掲示板</a></li>
      <li><a href="http://konoharu.sakura.ne.jp/umigame/yychat/yychat.cgi">ウミガメ雑談村</a></li>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1224519836/l50">反省・議論用スレ</a></li>
    </ul>

    <div class="menu">外部リンク</div>
    <ul>
      <li>東方ウミガメ系</li>
      <li><a href="http://alicegame.dip.jp/sanae/">早苗鯖</a></li>
      <li><a href="http://satori.crz.jp/">さとり鯖</a></li>
      <li><a href="http://www7.atpages.jp/izayoi398/">咲夜鯖</a></li>
      <!-- <li><a href="http://www12.atpages.jp/cirno/">チルノ鯖（開発チーム）</a></li> -->
      <li>やる夫系</li>
      <li><a href="http://www12.atpages.jp/yaruo/jinro/">流石兄弟鯖</a></li>
      <li><a href="http://alicegame.dip.jp/suisei/">翠星石鯖</a></li>
      <li><a href="http://www13.atpages.jp/yaranai/">薔薇姉妹鯖</a></li>
      <li><a href="http://www13.atpages.jp/suigintou/">水銀鯖</a></li>
      <li><a href="http://www15.atpages.jp/kanaria/">金糸雀保管庫</a></li>
      <li><a href="http://www12.atpages.jp/yagio/jinro_php_files/jinro_php/">世紀末鯖（テスト鯖）</a></li>
      <li><a href="http://www37.atwiki.jp/yaruomura/">やる夫wiki</a></li>
      <li>リンク希望募集中</li>
    </ul>
  </td>

  <td>
    <fieldset>
      <legend>Information <a href="info/index.php">〜過去のinformationはこちら〜</a></legend>
      <div class="information"><?php include 'info/top.php'; ?></div>
    </fieldset>

    <fieldset>
      <legend>ゲーム一覧</legend>
      <div class="game-list"><?php include 'room_manager.php'; ?></div>
    </fieldset>
    <?php OutputSharedServerRoom(); ?>
    <fieldset>
      <legend>村の作成</legend><?php OutputCreateRoom(); ?>
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
<?php echo 'PHP Ver. ' . PHP_VERSION . ', ' . $script_version . ', LastUpdate: ' . $script_lastupdate; ?>
</div>
</body>
</html>
