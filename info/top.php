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

  <li>Ver. 1.4.0α19 向けデバッグ情報 (2009/09/03 03:30 追加)</li>
  <ul>
    <li>include/game_functions.php % 1112行目 (2009/09/03 03:30 追加)<br>
      × global $GAME_CONF, $room_no, $ROOM<font color="#FF0000">, $vote_times</font>;<br>
      ○ global $GAME_CONF, $room_no, $ROOM;<br>
    </li>
    <li>include/game_functions.php % 1153行目 (2009/09/03 03:30 追加)<br>
      × elseif($check_draw && <font color="#FF0000">$vote_times</font> >= $GAME_CONF->draw) //引き分け<br>
      ○ elseif($check_draw && <font color="#FF0000">GetVoteTimes()</font> >= $GAME_CONF->draw) //引き分け<br>
    </li>
    <li>include/config.php % 251行目<br>
      × var $<font color="#FF0000">week</font>_replace_list = array( ...<br>
      ○ var $<font color="#FF0000">weekly</font>_replace_list = array( ...<br>
    </li>
    <li>include/game_functions.php % 459行目<br>
      × &lt;param name=&quot;movie&quot; value=&quot;{$SOUND-><font color="#FF0000">type</font>}&quot;&gt;<br>
      ○ &lt;param name=&quot;movie&quot; value=&quot;{$SOUND-><font color="#FF0000">$type</font>}&quot;&gt;<br>
    </li>
    <li>include/game_functions.php % 461行目<br>
      × &lt;embed src=&quot;{$SOUND-><font color="#FF0000">type</font>}&quot; type= ...<br>
      ○ &lt;embed src=&quot;{$SOUND-><font color="#FF0000">$type</font>}&quot; type= ...<br>
    </li>
    <li>game_vote.php % 679行目<br>
      × $this_voted_number .&quot;\t&quot; . $this_vote_number . &quot;\t&quot; . $RQ_ARGS->vote_times;<br>
      ○ <font color="#FF0000">(int)</font>$this_voted_number .&quot;\t&quot; . <font color="#FF0000">(int)</font>$this_vote_number . &quot;\t&quot; . $RQ_ARGS->vote_times;<br>
    </li>
    <li>game_vote.php % 1423行目<br>
      × $this_new_role = str_replace('mania', $this_result, $this_<font color="#FF0000">target</font>->role) . ' copied';<br>
      ○ $this_new_role = str_replace('mania', $this_result, $this_<font color="#FF0000">user</font>->role) . ' copied';<br>
    </li>
  </ul>

  <li>現在確認されているバグ</li>
  <ul>
    <li>ゲーム終了の切り替え時に発言窓が複数出ることがある (SQL 接続エラーに起因する事が判明)</li>
    <li>昼の投票時、まれに投票制限時間前に突然死処理が発生する事がある？ (紫炎鯖でのみ確認)</li>
    <li>狼の噛み投票が複数回できる事がある(投票済みにならない) (再現性不明)</li>
    <li>再投票の結果表示が変 (DB の登録結果がおかしい事に起因していることが判明)</li>
    <li>「異議あり」の音がリロードするたびに鳴る (調査中のため現在音声を止めています)</li>
  </ul>

  <li>改定案件</li>
  <ul>
    <li>CSS の I.E. 対応</li>
    <li>CSS の携帯対応</li>
    <li>デバッグ用ログ表示機能の実装</li>
    <li>エラーメッセージの改訂</li>
    <li>仮想 GM は任意で突然死させられるようにする</li>
    <li>クイズ村専用処理の実装 → Ver. 1.4 系で実装進行中</li>
    <li>トリップ対応 (現在は # を含む文字列に対してエラーを返すだけ)</li>
    <li>mysql_query() のラッパ関数作成 (エラー対策)</li>
    <li>過去ログ HTML 化の自動化</li>
  </ul>
</ol>
</p>
