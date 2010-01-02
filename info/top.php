<p>
  <font color="#FF0000">
   ここはテスト専用サーバです。ログの保全を一切保証しません。<br>
   This server is Japanese only.
   </font>
</p>

<p>Ver. 1.4.0 α21 デバッグ情報 (2009/12/29 (Tue) 04:00)<br>
・admin/setup.php @ 2行目<br>
× require_once(dirname(__FILE__) . '/../include/functions.php');<br>
○ require_once(dirname(__FILE__) . '/../include/init.php');<br>
<br>
・include/init.php @ 7行目<br>
× $DEBUG_MODE = true;<br>
○ $DEBUG_MODE = false;<br>
<br>
・include/game_play_functions.php @ 172行目、183行目を削除<br>
×    if(! $SELF->IsRole('dummy_guard')){ //狩り結果を表示<br>
×    }<br>
<br>
・さとりが複数の時の挙動の確認 (蒼鯖のログ的にバグがあるっぽいです)
</p>

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
    <li>投票結果が重複して表示されることがある</li>
  </ul>

  <li>改定案件</li>
  <ul>
    <li>CSS の I.E. 対応</li>
    <li>CSS の携帯対応</li>
    <li>仮想 GM は任意で突然死させられるようにする</li>
    <li>トリップ対応 (現在は # を含む文字列に対してエラーを返すだけ)</li>
    <li>過去ログ HTML 化の自動化</li>
    <li>クォートモード時のGM発言対応</li>
    <li>初日に突然死が発生した場合のキューピッドの投票処理の対応</li>
    <li>自動投票機能</li>
  </ul>
</ol>
