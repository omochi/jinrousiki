<?php require_once('../include/setting.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta http-equiv="Content-Style-Type" content="text/css">
<link rel="stylesheet" href="../css/index.css">
<title><?php echo $SERVER_CONF->title . $SERVER_CONF->comment; ?></title>
</head>
<body>
<a href="../">TOP に戻る</a>
<ul>
  <li>Ver. 1.3.0 → Ver. 1.4.0</li>
  <ol>
    <li>改善点</li>
    <ul>
      <li>多人数対応 (クッキーの処理の改善)</li>
    </ul>

    <li>追加機能</li>
    <ul>
      <li>埋毒者を吊った際の巻き込まれる対象を変更できる</li>
      <li>クイズ村モードの実装 (画像以外はほぼ完成)</li>
      <li>闇鍋モードの実装 (調整中) → <a href="chaos.php">闇鍋村について</a><br>
        Ver. 1.4.0 alpha11 よりゲームオプションが一部強制的にオフになります</li>
      <li>役職を大量に追加 → <a href="new_role.php">新役職について</a></li>
      <li>ゲームオプションを大量に追加</li>
      <li>複数キューピッドの実装</li>
    </ul>
  </ol>

  <li>Ver. 1.2.2a → Ver. 1.3.0</li>
  <ol>
    <li>改善点</li>
    <ul>
      <li>全面的に外部 CSS に置換することで HTML 出力のサイズが 20% 小さくなりました</li>
      <li>引き分け設定回数に吊り決定した場合でも引き分けになる仕様を改善 (吊りが決まればゲーム続行)</li>
    </ul>

    <li>追加機能</li>
    <ul>
      <li>ゲーム中、昼間は自分の投票済み状況が表示されます</li>
      <li>ゲーム中、身代わり君の発言がシステムメッセージ相当に変わります(仮想 GM 機能 / 調整中)</li>
      <li>システムメッセージを設定ファイルで変更できるようになりました (進行中)</li>
      <li>遺言は昼/夜の切り替えの影響を受けずに保存できるようになりました</li>
      <li>埋毒者を噛んで巻き込まれる狼の対象決定方法を任意で変更できるようにしました</li>
      <li>過去ログを逆順で表示できるようになりました (デフォルトは設定ファイルで変更可能)</li>
      <li>仮想 GM (身代わり君) は単独(一票)で Kick 処理を行えます</li>
    </ul>

    <li>バグフィックス</li>
    <ul>
      <li>Kick 投票エラーメッセージを改善</li>
      <li>身代わり君の占い・ゲーム開始投票を無効化</li>
      <li>占い師、狩人を複数設定しても正しく機能しするようにしたつもり</li>
      <li>夜の未投票者の突然死が全員巻き込まれるバグ修正</li>
      <li>埋毒者を噛んでも50%の確率で狼が巻き込まれないバグ修正</li>
    </ul>

    <li>テスト用の仕様変更点</li>
    <ul>
      <li>ソースコードのダウンロード/アップロードページを作成しました
	(アップロードは開発チーム専用です)</li>
      <li>ゲーム一覧にゲーム削除のリンクが出現します
	<font color="#FF0000">(緊急時以外はクリックしないで下さい)</font></li>
    </ul>
  </ol>
</ul>
</body></html>
