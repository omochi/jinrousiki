<p>
  <font color="#FF0000">
   ここはテスト専用サーバです。ログの保全を一切保証しません。<br>
   This server is Japanese only.
   </font>
</p>

<p>Ver. 1.4.0 β1 アップロード (2010/02/02 (Tue) 04:25:52) → <a href="src/">ダウンロード</a><br>
・Ver. 1.4.0 α24 のバグ Fix、設定ファイルの再配置など<br>
</p>

<p>Ver. 1.4.0 α24 アップロード (2010/01/28 (Thu) 21:29:30) → <a href="src/">ダウンロード</a><br>
・憑狼・紅狼・賢狼・紅狐・黒狐・司祭の実装、抗毒狼の仕様変更など<br>
<br>
・バグ Fix<br>
◆game_play.php % 731 行目<br>
× $USERS->GetHandleName($target_uname) . 'さんに投票済み');<br>
○ $USERS->GetHandleName($target_uname, true) . 'さんに投票済み');<br>
<br>
◆include/game_functions.php % 705 行目<br>
×elseif($pseud_self->IsRole('wise_wolf')){<br>
○elseif($virtual_self->IsRole('wise_wolf')){<br>
<br>
◆user_manager.php % 276 行目 (2010/01/30 02:30 追記)<br>
× array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'common', 'poison',<br>
○ array_push($wish_role_list, 'mage', 'necromancer', 'priest', 'guard', 'common', 'poison',<br>
<br>
◆include/game_functions.php % 400 行目付近 (2010/02/01 (Mon) 00:15 追記)<br>
[before]<br>
$said_user = $USERS->ByVirtualUname($talk->uname);<br>
[after]<br>
if(strpos($talk->location, 'heaven') === false)<br>
  $said_user = $USERS->ByVirtualUname($talk->uname);<br>
else<br>
  $said_user = $USERS->ByUname($talk->uname);<br>
<br>
◆include/game_vote_functions % 1865 行目付近<br>
[before]<br>
$target->dead_flag = false; //死亡フラグをリセット<br>
$USERS->Kill($target->user_no, 'WOLF_KILLED');<br>
if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応<br>
[after]<br>
if(isset($target->user_no)){<br>
  $target->dead_flag = false; //死亡フラグをリセット<br>
  $USERS->Kill($target->user_no, 'WOLF_KILLED');<br>
  if($target->revive_flag) $target->Update('live', 'live'); //蘇生対応<br>
}<br>


</p>

<p>Ver. 1.4.0 α23 アップロード (2010/01/10 (Sun) 06:11:24) → <a href="src/">ダウンロード</a><br>
・さとり・銀狼・薬師の仕様変更、鵺・女神の実装など
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
    <li>自動投票機能</li>
    <li>アイコン選択画面の改訂</li>
    <li>I.E. の霊界モードの動作確認</li>
    <li>GM モードの解説ページの作成</li>
  </ul>

  <li>現在作成中 / 公開テスト待ちの役職</li>
  <ul>
    <li>預言者 (「鉄火場」を告げる特殊な司祭)</li>
    <li>天人 (初日に死亡して「鉄火場」に復活する特殊な司祭)</li>
    <li>イタコ (誰か一人を「口寄せ」にするさとりの亜種)</li>
    <li>仙狸 (毒を失った変わりに蘇生率が高くなった猫又)</li>
    <li>仙狐 (一度だけ成功率100%の蘇生ができる妖狐)</li>
  </ul>
</ol>
