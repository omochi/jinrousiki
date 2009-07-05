<p>
  <font color="#FF0000">ここはテスト専用サーバです。</font>
</p>

<p>
<a href="http://satori.crz.jp/">現さとり鯖</a> (Ver. 1.2.2a) との主な違いは以下です。<br>
<ol>
  <li>改善点</li>
  <ul>
    <li>全面的に外部 CSS に置換することで HTML 出力のサイズが 20% 小さくなりました</li>
    <li>引き分け設定回数に吊り決定した場合でも引き分けになる仕様を改善 (吊りが決まればゲーム続行)</li>
  </ul>

  <li>追加機能 (1.3 系)</li>
  <ul>
    <li>ゲーム中、昼間は自分の投票済み状況が表示されます</li>
    <li>ゲーム中、身代わり君の発言がシステムメッセージ相当に変わります(仮想 GM 機能 / 調整中)</li>
    <li>システムメッセージを設定ファイルで変更できるようになりました (進行中)</li>
    <li>遺言は昼/夜の切り替えの影響を受けずに保存できるようになりました</li>
    <li>埋毒者を噛んで巻き込まれる狼の対象決定方法を任意で変更できるようにしました</li>
    <li>過去ログを逆順で表示できるようになりました (デフォルトは設定ファイルで変更可能)</li>
    <li>仮想 GM (身代わり君) は単独(一票)で Kick 処理を行えます</li>
  </ul>

  <li>追加機能 (1.4 系)</li>
  <ul>
    <li>クイズ村モードの実装 (画像以外はほぼ完成)</li>
    <li>闇鍋モードの実装 (調整中) → <a href="info/chaos.php">闇鍋村について</a><br>
        Ver. 1.4.0 alpha11 よりゲームオプションが一部強制的にオフになります</li>
    <li>役職を大量に追加 → <a href="info/new_role.php">新役職について</a></li>
    <li>複数キューピッドの実装</li>
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

  <li>現在確認されているバグ (Ver. 1.2 - 1.4 系共通)</li>
  <ul>
    <li>ゲーム終了の切り替え時に発言窓が複数出ることがある (SQL 接続エラーに起因する事が判明)</li>
    <li>突然死の処理にタイムラグがある (突然死のタイマー設定が怪しい？)</li>
    <li>昼の投票時、まれに投票制限時間前に突然死処理が発生する事がある？ (紫炎鯖でのみ確認)</li>
  </ul>

  <li>現在確認されているバグ (Ver. 1.3 〜のみ)</li>
  <ul>
    <li>狼の噛み投票が複数回できる事がある(投票済みにならない) (再現性不明)</li>
    <li>再投票の結果表示が変 (DB の登録結果がおかしい事に起因していることが判明)</li>
  </ul>

  <li>改定案件</li>
  <ul>
    <li>CSS の I.E. 対応</li>
    <li>CSS の携帯対応</li>
    <li>デバッグ用ログ表示機能の実装</li>
    <li>エラーメッセージの改訂</li>
    <li>多人数対応 (クッキーの処理の改善)</li>
    <li>埋毒者を吊った際の巻き込まれる対象を変更できるようにする</li>
    <li>仮想 GM は任意で突然死させられるようにする</li>
    <li>クイズ村専用処理の実装 → Ver. 1.4 系で実装進行中</li>
    <li>霊能者の結果表示ルーチンの改訂</li>
    <li>トリップ対応 (現在は # を含む文字列に対してエラーを返すだけ)</li>
    <li>mysql_query() のラッパ関数作成 (エラー対策)</li>
    <li>過去ログ HTML 化の自動化</li>
  </ul>
<ol>
</p>
