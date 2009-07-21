<?php require_once('include/setting.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" href="css/index.css">
<title>汝は人狼なりや？<?php echo $server_comment; ?></title>
</head>
<body>
<?php if($back_page != '') echo "<a href=\"$back_page\">←戻る</a>"; ?>
<a href="index.php"><img src="img/top_title.jpg"></a>
<div class="comment"><?php echo $server_comment; ?></div>
<noscript>＜＜ JavaScriptを有効にしてください ＞＞</noscript>
<table class="main">
  <tr><td>
    <div class="menu">メニュー</div>
    <ul>
      <li><a href="script_info.php">特徴と仕様</a></li>
      <li><a href="rule.php">ゲームのルール</a></li>
      <li><a href="old_log.php">ログ閲覧</a></li>
      <li><a href="old_log.php?add_role=on">役職表示ログ閲覧</a></li>
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
      <li>更新履歴 <a href="src/diff.txt">1.4 系</a>
          <a href="src/diff-1.3.txt">1.3 系</a><br>(テキストファイル)</li>
      <li><a href="http://sourceforge.jp/projects/jinrousiki/">SourceForge</li>
    </ul>

    <div class="menu">交流用サイト</div>
    <ul>
      <li><a href="http://jbbs.livedoor.jp/netgame/2829/">ウミガメ人狼掲示板</a></li>
      <li><a href="http://konoharu.sakura.ne.jp/umigame/yychat/yychat.cgi">ウミガメ雑談村</a></li>
    </ul>

    <div class="menu">外部リンク</div>
    <ul>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1241277178/l50">本スレ</a></li>
      <li><a href="http://jbbs.livedoor.jp/bbs/read.cgi/game/43883/1224519836/l50">反省・議論用スレ</a></li>
      <li><a href="http://www27.atwiki.jp/umigamejinnro/">東方ウミガメwiki</a></li>
      <li><a href="http://jinro.ebb.jp/">ニコ生専用鯖</a></li>
      <li><a href="http://jinro.s369.xrea.com/">ニコ生専用テスト鯖</a></li>
      <li><a href="http://www12.atpages.jp/yaruo/jinro/">流石兄弟鯖</a></li>
      <li><a href="http://www13.atpages.jp/yaranai/">薔薇姉妹鯖</a></li>
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
    <? OutputSharedServerRoom(); ?>
    <fieldset>
      <legend>村の作成</legend><? OutputCreateRoom(); ?>
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
