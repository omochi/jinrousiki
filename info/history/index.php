<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
OutputInfoPageHeader('サーバ更新履歴', 1);
?>
<h2>Ver. 1.3.0 → Ver. 1.4.0</h2>
<h3>改善点</h3>
<ul>
  <li>多人数対応 (クッキーの処理の改善)</li>
  <li>リアルタイム表示 JavaScript の自動補正処理 (時計合わせが不要になります)</li>
  <li>ユーザアイコン表示/登録の機能強化</li>
  <li>ユーザ入村画面の改訂</li>
  <li>再入村リンクの表示機能実装 (過去ログ)</li>
</ul>

<h3>追加機能</h3>
<ul>
  <li>埋毒者を吊った / 噛んだ際の巻き込まれる対象を限定できる</li>
  <li>闇鍋モードの実装 → <a href="../chaos.php">闇鍋モード</a></li>
  <li>役職を大量に追加 → <a href="../new_role/" target="_top">新役職情報</a></li>
  <li>ゲームオプションを大量に追加 → <a href="../game_option.php">ゲームオプション</a></li>
</ul>

<h3>仕様変更</h3>
<ul>
  <li>複数キューピッドに対応</li>
</ul>

<h2>Ver. 1.2.2a → Ver. 1.3.0</h2>
<h3>改善点</h3>
<ul>
  <li>全面的に外部 CSS に置換することで HTML 出力のサイズが 20% 小さくなりました</li>
  <li>引き分け設定回数に吊り決定した場合でも引き分けになる仕様を改善 (吊りが決まればゲーム続行)</li>
</ul>

<h3>追加機能</h3>
<ul>
  <li>ゲーム中、昼間は自分の投票済み状況が表示されます</li>
  <li>ゲーム中、身代わり君の発言がシステムメッセージ相当に変わります(仮想 GM 機能 / 調整中)</li>
  <li>システムメッセージを設定ファイルで変更できるようになりました (進行中)</li>
  <li>遺言は昼/夜の切り替えの影響を受けずに保存できるようになりました</li>
  <li>埋毒者を噛んで巻き込まれる狼の対象決定方法を任意で変更できるようにしました</li>
  <li>過去ログを逆順で表示できるようになりました (デフォルトは設定ファイルで変更可能)</li>
  <li>仮想 GM (身代わり君) は単独(一票)で Kick 処理を行えます</li>
</ul>

<h3>バグフィックス</h3>
<ul>
  <li>Kick 投票エラーメッセージを改善</li>
  <li>身代わり君の占い・ゲーム開始投票を無効化</li>
  <li>占い師、狩人を複数設定しても正しく機能しするようにしたつもり</li>
  <li>夜の未投票者の突然死が全員巻き込まれるバグ修正</li>
  <li>埋毒者を噛んでも50%の確率で狼が巻き込まれないバグ修正</li>
</ul>

<h3>テスト用の仕様変更点</h3>
<ul>
  <li>ソースコードのダウンロード/アップロードページを作成しました
	(アップロードは開発チーム専用です)</li>
  <li>ゲーム一覧にゲーム削除のリンクが出現します
	<font color="#FF0000">(緊急時以外はクリックしないで下さい)</font></li>
</ul>
</body></html>
