<?php
//-- ユーザ登録コントローラー --//
class UserManager {
  //ユーザ登録
  static function Entry() {
    extract(RQ::ToArray()); //引数を展開
    $url = sprintf('user_manager.php?room_no=%d', $room_no); //ベースバックリンク
    if ($user_no > 0) $back_url .= sprintf('&user_no=%d', $user_no); //登録情報変更モード
    $back_url = sprintf('<br><a href="%s">戻る</a>', $url); //バックリンク
    if (GameConfig::TRIP && $trip != '') $uname .= Text::ConvertTrip('#' . $trip); //トリップ変換

    //記入漏れチェック
    $title = '村人登録 [入力エラー]';
    $str   = 'が空です (空白と改行コードは自動で削除されます)。' . $back_url;
    $empty = 'が入力されていません。' . $back_url;
    if ($user_no < 1) {
      if ($uname     == '') HTML::OutputResult($title, 'ユーザ名'     . $str);
      if ($password  == '') HTML::OutputResult($title, 'パスワード'   . $str);
    }
    if ($handle_name == '') HTML::OutputResult($title, '村人の名前'   . $str);
    if ($profile     == '') HTML::OutputResult($title, 'プロフィール' . $str);
    if (! is_int($icon_no)) HTML::OutputResult($title, 'アイコン番号' . $empty);
    if (empty($sex))        HTML::OutputResult($title, '性別'         . $empty);

    //文字数制限チェック
    $format = '%sは%d文字まで' . $back_url;
    $limit_list = array(
      array('str' => $uname,        'name' => 'ユーザ名',     'config' => GameConfig::LIMIT_UNAME),
      array('str' => $handle_uname, 'name' => '村人の名前',   'config' => GameConfig::LIMIT_UNAME),
      array('str' => $profile,      'name' => 'プロフィール', 'config' => GameConfig::LIMIT_PROFILE)
			);
    foreach ($limit_list as $limit) {
      if (strlen($limit['str']) > $limit['config']) {
	HTML::OutputResult($title, sprintf($format, $limit['name'], $limit['config']));
      }
    }

    //例外チェック
    if ($uname == 'dummy_boy' || $uname == 'system') {
      $format = 'ユーザ名「%s」は使用できません。%s';
      HTML::OutputResult($title, sprintf($format, $uname, $back_url));
    }
    if ($user_no < 1 && ($handle_name == '身代わり君' || $handle_name == 'システム')) {
      $format = '村人名「%s」は使用できません。%s';
      HTML::OutputResult($title, sprintf($format, $handle_name, $back_url));
    }
    if ($sex != 'male' && $sex != 'female') {
      HTML::OutputResult($title, '無効な性別です。' . $back_url);
    }
    $format = 'SELECT icon_no FROM user_icon WHERE disable IS NOT TRUE AND icon_no = %d';
    if ($icon_no < ($user_no > 0 ? 0 : 1) || DB::Count(sprintf($format, $icon_no)) < 1) {
      HTML::OutputResult($title, '無効なアイコン番号です' . $back_url);
    }

    if (! DB::Transaction()) { //トランザクション開始
      $str = 'サーバが混雑しています。<br>再度登録してください。' . $back_url;
      HTML::OutputResult('村人登録 [サーバエラー]', $str);
    }

    DB::$ROOM = RoomDataSet::LoadEntryUser($room_no); //現在の村情報を取得 (ロック付き)
    if (! DB::$ROOM->IsBeforeGame() || DB::$ROOM->status != 'waiting') { //ゲーム開始判定
      HTML::OutputResult('村人登録 [入村不可]', 'すでにゲームが開始されています。');
    }
    DB::$ROOM->ParseOption(); //名前・トリップ必須オプション用

    //DB から現在のユーザ情報を取得 (ロック付き)
    RQ::Load('RequestBase', true);
    RQ::$get->room_no = $room_no;
    RQ::$get->retrive_type = 'entry_user';
    DB::$USER = new UserDataSet(RQ::$get);

    $user_count = DB::$USER->GetUserCount(); //現在の KICK されていない住人の数を取得
    if ($user_no < 1 && $user_count >= DB::$ROOM->max_user) { //定員オーバー判定
      HTML::OutputResult('村人登録 [入村不可]', '村が満員です。');
    }

    //重複チェック (比較演算子は大文字・小文字を区別しないのでクエリで直に判定する)
    $query_count = sprintf('SELECT uname FROM user_entry WHERE room_no = %d AND', $room_no);
    $footer = '<br>別の名前にしてください。' . $back_url;

    //キックされた人と同じユーザ名
    if (DB::Count(sprintf("%s uname = '%s' AND live = 'kick'", $query_count, $uname)) > 0) {
      $str = 'キックされた人と同じユーザ名は使用できません (村人名は可)。';
      HTML::OutputResult('村人登録 [キックされたユーザ]', $str . $footer);
    }

    if ($user_no > 0) { //登録情報変更モード
      $query = 'SELECT uname, handle_name, sex, profile, role, icon_no, u.session_id, ' .
	'color, icon_name FROM user_entry AS u INNER JOIN user_icon USING (icon_no) ' .
	'WHERE room_no = %d AND user_no = %d';
      $target = DB::FetchObject(sprintf($query, $room_no, $user_no), 'User', true);
      if ($target->session_id != Session::GetID()) {
	HTML::OutputResult('村人登録 [セッションエラー]', 'セッション ID が一致しません。');
      }

      if (! $target->IsDummyBoy() && ($handle_name == '身代わり君' || $handle_name == 'システム')) {
	$format = '村人名「%s」は使用できません%s';
	HTML::OutputResult($title, sprintf($format, $handle_name, $back_url));
      }

      $query = "%s user_no <> '%d' AND handle_name = '%s' AND live = 'live'";
      if (DB::Count(sprintf($query, $query_count, $user_no, $handle_name)) > 0) {
	$str = '村人名が既に登録されています。';
	HTML::OutputResult('村人登録 [重複登録エラー]', $str . $footer);
      }

      $str   = sprintf('%s さんが登録情報を変更しました。', $target->handle_name);
      $stack = array();
      if ($target->handle_name != $handle_name) {
	$stack[] = sprintf("handle_name = '%s'", $handle_name);
	$str    .= sprintf("\n村人の名前：%s → %s", $target->handle_name, $handle_name);
      }
      if ($target->icon_no != $icon_no) {
	if (! $target->IsDummyBoy() && $icon_no == 0) {
	  HTML::OutputResult($title, '無効なアイコン番号です' . $back_url);
	}
	$query     = 'SELECT icon_name FROM user_icon WHERE icon_no = %d';
	$icon_name = DB::FetchResult(sprintf($query, $icon_no));
	$stack[]   = sprintf("icon_no = '%d'", $icon_no);
	$format    = "\nアイコン：No. %d (%s) → No. %d (%s)";
	$str      .= sprintf($format, $target->icon_no, $target->icon_name, $icon_no, $icon_name);
      }

      foreach (array('sex', 'profile', 'role') as $value) {
	if ($target->$value != $$value) $stack[] = sprintf("%s = '%s'", $value, $$value);
      }
      //Text::p($stack);
      if (count($stack) < 1) {
	$str = '変更点はありません。' . $back_url;
	HTML::OutputResult('村人登録 [登録情報変更]', $str);
      }
      DB::$ROOM->TalkBeforeGame($str, $target->uname, $target->handle_name, $target->color);

      $format = 'UPDATE user_entry SET %s WHERE room_no = %d AND user_no = %d';
      $query  = sprintf($format, implode(', ', $stack), $room_no, $user_no);
      if (DB::ExecuteCommit($query)) {
	$str = <<<EOF
登録データを変更しました。<br>
<form action="#" method="post">
<input type="button" value="ウィンドウを閉じる" onClick="window.close()">
</form>
EOF;
	HTML::OutputResult('村人登録 [登録情報変更]', $str);
      }
      else {
	$str = 'サーバが混雑しています。<br>再度登録してください。' . $back_url;
	HTML::OutputResult('村人登録 [サーバエラー]', $str);
      }
    }

    //ユーザ名・村人名
    if (DB::$ROOM->IsOption('necessary_name') && strpos($uname, '◆') === 0) {
      HTML::OutputResult($title, 'ユーザ名がありません (トリップのみは不可)');
    }
    if (DB::$ROOM->IsOption('necessary_trip') && strpos($uname, '◆') === false) {
      HTML::OutputResult($title, 'トリップがありません');
    }
    $query_count .= " live = 'live' AND";
    $query = sprintf("%s (uname = '%s' OR handle_name = '%s')", $query_count, $uname, $handle_name);
    if (DB::Count($query) > 0) {
      $str = 'ユーザ名、または村人名が既に登録してあります。';
      HTML::OutputResult('村人登録 [重複登録エラー]', $str . $footer);
    }
    //HTML::OutputResult('トリップテスト', $uname.'<br>'.$handle_name.$back_url); //テスト用

    //IP アドレスチェック
    $ip_address = Security::GetIP(); //ユーザの IP アドレスを取得
    if (! ServerConfig::DEBUG_MODE) {
      $query = sprintf("%s ip_address = '%s'", $query_count, $ip_address);
      if (GameConfig::LIMIT_IP && DB::Count($query) > 0) {
	HTML::OutputResult('村人登録 [多重登録エラー]', '多重登録はできません。');
      }
      elseif (Security::CheckBlackList()) {
	HTML::OutputResult('村人登録 [入村制限]', '入村制限ホストです。');
      }
    }

    //DB にユーザデータを登録
    $user_no = count(DB::$USER->name) + 1; //KICK された住人も含めた新しい番号を振る
    if (DB::InsertUser($room_no, $uname, $handle_name, $password, $user_no, $icon_no, $profile,
		       $sex, $role, Session::GetID(true))) {
      //クッキーの初期化
      DB::$ROOM->system_time = Time::Get(); //現在時刻を取得
      $cookie_time = DB::$ROOM->system_time - 3600;
      setcookie('scene',      '', $cookie_time);
      setcookie('vote_times', '', $cookie_time);
      setcookie('objection',  '', $cookie_time);

      DB::$ROOM->Talk($handle_name . ' ' . Message::$entry_user); //入村メッセージ
      DB::$ROOM->UpdateTime();
      DB::Commit();

      $url = sprintf('game_frame.php?room_no=%d', $room_no);
      $str = '%d 番目の村人登録完了、村の寄り合いページに飛びます。<br>' .
	'切り替わらないなら <a href="%s">ここ</a> 。';
      HTML::OutputResult('村人登録', sprintf($str, ++$user_count, $url), $url);
    }
    else {
      $str = 'データベースサーバが混雑しています。<br>時間を置いて再度登録してください。';
      HTML::OutputResult('村人登録 [データベースサーバエラー]', $str);
    }
  }

  //ユーザ登録画面表示
  static function Output() {
    if (RQ::$get->user_no > 0) { //登録情報変更モード
      $stack = UserManagerDB::GetUser();
      if ($stack['session_id'] != Session::GetID()) {
	HTML::OutputResult('村人登録 [セッションエラー]', 'セッション ID が一致しません');
      }
      foreach ($stack as $key => $value) {
	if (array_key_exists($key, RQ::$get)) RQ::Set($key, $value);
      }
    }

    DB::$ROOM = RoomDataSet::LoadEntryUserPage(RQ::$get->room_no);
    $str = sprintf('%d 番地の村は', RQ::$get->room_no);
    if (is_null(DB::$ROOM->id)) {
      HTML::OutputResult('村人登録 [村番号エラー]', $str . '存在しません');
    }
    if (DB::$ROOM->IsFinished()) {
      HTML::OutputResult('村人登録 [入村不可]', $str . '終了しました');
    }
    if (DB::$ROOM->status != 'waiting') {
      HTML::OutputResult('村人登録 [入村不可]', $str . 'すでにゲームが開始されています。');
    }
    DB::$ROOM->ParseOption(true);

    UserManagerHTML::Output();
  }
}

//-- データベースアクセス (UserManager 拡張) --//
class UserManagerDB {
  //ユーザ情報取得
  static function GetUser() {
    $query = 'SELECT * FROM user_entry WHERE room_no = ? AND user_no = ?';
    DB::Prepare($query, array(RQ::$get->room_no, RQ::$get->user_no));
    return DB::FetchAssoc(null, true);
  }
}

//-- HTML 生成クラス (UserManager 拡張) --//
class UserManagerHTML {
  const PATH = 'img/entry_user';
  const URL  = 'user_manager.php?room_no=%d';
  const UNAME_INPUT = '<td><input type="text" id="uname" name="uname" size="30" maxlength="30" value="%s"></td>';
  const UNAME_EXPLAIN_HEADER = '普段は表示されず、他のユーザ名がわかるのは';
  const UNAME_EXPLAIN_FOOTER = '死亡したときとゲーム終了後のみです';
  const UNAME_TRIP = '<br>＃の右側はトリップ専用入力欄です';

  //出力
  static function Output() {
    HTML::OutputHeader(ServerConfig::TITLE . '[村人登録]', 'entry_user');
    HTML::OutputJavaScript('submit_icon_search');
    HTML::OutputBodyHeader();
    self::OutputHeader();
    self::OutputForm();
    self::OutputWishRole();
    self::OutputSubmit();
    self::OutputIcon();
    Text::Output('</table></div></form>');
    HTML::OutputFooter();
  }

  //ヘッダ出力
  private static function OutputHeader() {
    $format = <<<EOF
<a href="./">←戻る</a><br>
<form method="POST" action="%s">
<div align="center">
<table class="main">
<tr><td><img src="%s/title.gif" alt="申請書"></td></tr>
<tr><td class="title">%s 村<img src="%s/top.gif" alt="への住民登録を申請します"></td></tr>
<tr><td class="number">～%s～ [%d 番地]</td></tr>

EOF;
    $url = sprintf(self::URL, DB::$ROOM->id);
    if (RQ::$get->user_no > 0) $url .= sprintf('&user_no=%d', RQ::$get->user_no);

    printf($format,
	   $url, self::PATH, DB::$ROOM->name, self::PATH, DB::$ROOM->comment, DB::$ROOM->id);
  }

  //フォーム出力
  private static function OutputForm() {
    $format = <<<EOF
<tr><td>
<table class="input">
%s
<tr>
<td class="img"><label for="handle_name"><img src="%s/handle_name.gif" alt="村人の名前"></label></td>
<td><input type="text" id="handle_name" name="handle_name" size="30" maxlength="30" value="%s"></td>
<td class="explain">村で表示される名前です</td>
</tr>
%s
<tr>
<td class="img"><img src="%s/sex.gif" alt="性別"></td>
<td class="img">
<label for="male"><img src="%s/sex_male.gif" alt="男性"><input type="radio" id="male" name="sex" value="male"%s></label>
<label for="female"><img src="%s/sex_female.gif" alt="女性"><input type="radio" id="female" name="sex" value="female"%s></label>
</td>
<td class="explain">特に意味は無いかも……</td>
</tr>
<tr>
<td class="img"><label for="profile"><img src="%s/profile.gif" alt="プロフィール"></label></td>
<td colspan="2"><textarea id="profile" name="profile" cols="30" rows="2">%s</textarea></td>
</tr>
<tr>

EOF;

    $male   = '';
    $female = '';
    switch (RQ::$get->sex) {
    case 'male':
      $male = ' checked';
      break;

    case 'female':
      $female = ' checked';
      break;
    }

    printf($format,
	   self::GenerateUname(), self::PATH, RQ::$get->handle_name, self::GeneratePassword(),
	   self::PATH, self::PATH, $male, self::PATH, $female, self::PATH, RQ::$get->profile);
  }

  //希望役職選択フォーム出力
  private static function OutputWishRole() {
    if (! DB::$ROOM->IsOption('wish_role')) {
      echo '<td><input type="hidden" name="role" value="none">';
      return;
    }

    $format = <<<EOF
<td class="role"><img src="%s/role.gif" alt="役割希望"></td>
<td colspan="2">

EOF;
    printf($format, self::PATH);

    $stack = array('none');
    if (DB::$ROOM->IsChaosWish()) {
      array_push($stack,
		 'human', 'mage', 'necromancer', 'medium', 'priest', 'guard', 'common',
		 'poison', 'poison_cat', 'pharmacist', 'assassin', 'mind_scanner', 'jealousy',
		 'brownie', 'wizard', 'doll', 'escaper', 'wolf', 'mad', 'fox', 'child_fox',
		 'cupid', 'angel', 'quiz', 'vampire', 'chiroptera', 'fairy', 'ogre', 'yaksa',
		 'duelist', 'avenger', 'patron', 'mania', 'unknown_mania');
    }
    elseif (DB::$ROOM->IsOption('gray_random')) {
      array_push($stack, 'human', 'wolf', 'mad', 'fox');
    }
    else {
      array_push($stack, 'human', 'wolf');
      if (DB::$ROOM->IsQuiz()) {
	array_push($stack, 'mad', 'common', 'fox');
      }
      else {
	array_push($stack, 'mage', 'necromancer', 'mad', 'guard', 'common');
	if (DB::$ROOM->IsOption('detective')) $stack[] = 'detective_common';
	$stack[] = 'fox';
      }
      foreach (array('poison', 'assassin', 'boss_wolf') as $role) {
	if (DB::$ROOM->IsOption($role)) $stack[] = $role;
      }
      if (DB::$ROOM->IsOption('poison_wolf')) array_push($stack, 'poison_wolf', 'pharmacist');
      foreach (array('possessed_wolf', 'sirius_wolf', 'child_fox', 'cupid') as $role) {
	if (DB::$ROOM->IsOption($role)) $stack[] = $role;
      }
      if (DB::$ROOM->IsOption('medium')) array_push($stack, 'medium', 'mind_cupid');
      if (DB::$ROOM->IsOptionGroup('mania') && ! in_array('mania', $stack)) $stack[] = 'mania';
    }

    echo "<table>\n<tr>";
    $format = <<<EOF
<td><label for="%s"><input type="radio" id="%s" name="role" value="%s"%s><img src="%s/role_%s.gif" alt="%s"></label></td>

EOF;
    $count = 0;
    foreach ($stack as $role) {
      if ($count > 0 && $count % 4 == 0) echo "</tr>\n<tr>"; //4個ごとに改行
      $count++;
      $alt = '←' . ($role == 'none' ? '無し' : RoleData::$main_role_list[$role]);
      $checked = RQ::$get->role == $role ? ' checked' : '';
      printf($format, $role, $role, $role, $checked, self::PATH, $role, $alt);
    }
    echo "</tr>\n</table>";
  }

  //登録ボタン出力
  private static function OutputSubmit() {
    echo <<<EOF
</td></tr>
<tr>
<td class="submit" colspan="3">
<span class="explain">
ユーザ名、村人の名前、パスワードの前後の空白および改行コードは自動で削除されます
</span>
<input type="submit" id="entry" name="entry" value="村人登録申請"></td>
</tr>
</table>
</td></tr>
EOF;
  }

  //アイコン選択フォーム出力
  private static function OutputIcon() {
    $format = <<<EOF
<tr><td>
<fieldset><legend><img src="%s/icon.gif" alt="アイコン"></legend>
<table class="icon">
<tr><td colspan="5">
<input id="fix_number" type="radio" name="icon_no"%s><label for="fix_number">手入力</label>
<input type="text" name="icon_no" size="10px" value="%s">(半角英数で入力してください)
</td></tr>
<tr><td colspan="5">

EOF;
    if (isset(RQ::$get->icon_no) && RQ::$get->icon_no > (RQ::$get->user_no > 0 ? -1 : 0)) {
      $checked = ' checked';
      $icon_no = RQ::$get->icon_no;
    } else {
      $checked = '';
      $icon_no = '';
    }

    printf($format, self::PATH, $checked, $icon_no);
    IconHTML::Output('user_manager');
    echo <<<EOF
</tr></table>
</fieldset>
</td></tr>
EOF;
  }

  //ユーザ名フォーム生成
  private static function GenerateUname() {
    $format = <<<EOF
<tr>
<td class="img"><label for="uname"><img src="%s/uname.gif" alt="ユーザ名"></label></td>

EOF;

    if (RQ::$get->user_no > 0) { //登録情報変更モード時はユーザ名は変更不可
      $format .= <<<EOF
<td>%s</td>
<td class="explain">%s</td>
</tr>
EOF;
      $str = self::UNAME_EXPLAIN_HEADER . '<br>' . self::UNAME_EXPLAIN_FOOTER;
      return sprintf($format, self::PATH, RQ::$get->uname, $str);
    }
    elseif (GameConfig::TRIP) { //トリップ対応
      $format .= self::UNAME_INPUT . "\n" . <<<EOF
<td><label for="trip">＃</label> <input type="text" id="trip" name="trip" size="15" maxlength="15" value="%s"></td>
</tr>
<tr>
<td></td>
<td colspan="2" class="explain">%s</td>
</tr>
EOF;
      $str = self::UNAME_EXPLAIN_HEADER . self::UNAME_EXPLAIN_FOOTER . self::UNAME_TRIP;
      if (DB::$ROOM->IsOption('necessary_name') && DB::$ROOM->IsOption('necessary_trip')) {
	$str .= '<br><span>必ずユーザ名・トリップの両方を入力してください</span>';
      } elseif (DB::$ROOM->IsOption('necessary_name')) {
	$str .= '<br><span>必ずユーザ名を入力してください</span>';
      } elseif (DB::$ROOM->IsOption('necessary_trip')) {
	$str .= '<br><span>必ずトリップを入力してください</span>';
      }
      return sprintf($format, self::PATH, RQ::$get->uname, RQ::$get->trip, $str);
    }
    else {
      $format .= self::UNAME_INPUT . "\n" . <<<EOF
<td class="explain">%s(<span>トリップ使用不可</span>)</td>
</tr>
EOF;
      $str = self::UNAME_EXPLAIN_HEADER . '<br>' . self::UNAME_EXPLAIN_FOOTER;
      return sprintf($format, self::PATH, RQ::$get->uname, $str);
    }
  }

  //パスワードフォーム生成
  private static function GeneratePassword() {
    if (RQ::$get->user_no > 0) return '';
    $format = <<<EOF
<tr>
<td class="img"><label for="password"><img src="%s/password.gif" alt="パスワード"></label></td>
<td><input type="password" id="password" name="password" size="30" maxlength="30" value=""></td>
<td class="explain">セッションが切れた場合のログイン時に使います<br> (<span>暗号化されていないので要注意</span>)</td>
</tr>
EOF;
    return sprintf($format, self::PATH);
  }
}
