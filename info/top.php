<p>
  <font color="#FF0000">ここはテスト専用サーバです。</font>
</p>

<p>
<a href="http://satori.crz.jp/">現さとり鯖</a> (Ver. 1.3.0) との主な違いは
<a href="info/history.php">更新履歴</a> を参照してください。<br>
<ol>
  <li>テスト用の仕様変更点</li>
  <ul>
    <li>ソースコードのダウンロード/アップロードページを作成しました
      (アップロードは開発チーム専用です)</li>
    <li>ゲーム一覧にゲーム削除のリンクが出現します
      <font color="#FF0000">(緊急時以外はクリックしないで下さい)</font></li>
  </ul>

  <li>現在確認されているバグ</li>
  <ul>
    <li>ゲーム終了の切り替え時に発言窓が複数出ることがある (SQL 接続エラーに起因する事が判明)</li>
    <li>昼の投票時、まれに投票制限時間前に突然死処理が発生する事がある？ (紫炎鯖でのみ確認)</li>
    <li>狼の噛み投票が複数回できる事がある(投票済みにならない) (再現性不明)</li>
    <li>再投票の結果表示が変 (DB の登録結果がおかしい事に起因していることが判明)</li>
    <li>「異議あり」の音がリロードするたびに鳴る (調査中のため現在音声を止めています) → α20で修正済み</li>
  </ul>

  <li>改定案件</li>
  <ul>
    <li>CSS の I.E. 対応</li>
    <li>CSS の携帯対応</li>
    <li>デバッグ用ログ表示機能の実装</li>
    <li>エラーメッセージの改訂</li>
    <li>仮想 GM は任意で突然死させられるようにする</li>
    <li>トリップ対応 (現在は # を含む文字列に対してエラーを返すだけ)</li>
    <li>過去ログ HTML 化の自動化</li>
  </ul>
</ol>
</p>
