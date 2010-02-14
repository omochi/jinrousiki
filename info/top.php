<p>
  <font color="#FF0000">
   ここはテスト専用サーバです。ログの保全を一切保証しません。<br>
   This server is Japanese only.
   </font>
</p>

<p>Ver. 1.4.0 β2 アップロード (2010/02/08 (Mon) 03:41:23) → <a href="src/">ダウンロード</a><br>
・預言者、天人、イタコ、仙狸、仙狐の実装<br>
・猫又、出題者の仕様変更<br>
・再入村リンク表示機能の実装 (設定された時間内、過去ログページに出現します)<br>
<br>
・バグ Fix<br>
◆include/game_vote_functions.php % 1188行目<br>
× elseif(! $ROOM->IsOpenCast() && $user->IsGroup('evoke_scanner')){<br>
○ elseif(! $ROOM->IsOpenCast() && $user->IsRole('evoke_scanner')){<br>
<br>
◆game_play.php % 449 行目<br>
× array_push($actor_list, 'poison_cat');<br>
○ array_push($actor_list, '%cat', 'revive_fox');<br>
<br>
</p>

<p>Ver. 1.4.0 β1 アップロード (2010/02/02 (Tue) 04:25:52) → <a href="src/">ダウンロード</a><br>
・Ver. 1.4.0 α24 のバグ Fix、設定ファイルの再配置など<br>
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
    <li>再投票の結果表示が変 (DB の OPTIMIZE で解決します)</li>
    <li>投票結果が重複して表示されることがある</li>
  </ul>

  <li>改定案件</li>
  <ul>
    <li>村名、コメントなどに NG ワードを設定できるようにする</li>
    <li>GM モードの解説ページの作成</li>
    <li>アイコン選択画面の改訂</li>
    <li>前日に死んだ突然死者の表示対応</li>
    <li>過去ログ HTML 化の自動化</li>
    <li>クォートモード時のGM発言対応</li>
    <li>I.E. の霊界モードの動作確認</li>
    <li>トリップ対応 (現在は # を含む文字列に対してエラーを返すだけ)</li>
    <li>CSS の I.E. 対応</li>
    <li>CSS の携帯対応</li>
    <li>自動投票機能</li>
    <li>仮想 GM は任意で突然死させられるようにする</li>
  </ul>

  <li>現在作成中 / 公開テスト待ちの項目</li>
  <ul>
    <li>自動霊界オフオプション</li>
  </ul>
</ol>
