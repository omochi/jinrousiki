<?php
//-- 配役テスト --//
class CastTest {
  //出力
  static function Output() {
    HTML::OutputHeader('配役テスト', 'game_play', true);
    GameHTML::OutputPlayer();
    Vote::AggregateGameStart();
    DB::$ROOM->date++;
    DB::$ROOM->scene = 'night';
    foreach (DB::$USER->rows as $user) $user->Reparse();
    GameHTML::OutputPlayer();
    HTML::OutputFooter();
  }
}

//-- 裏・闇鍋モードテスト --//
class ChaosVersoTest {
  //出力
  static function Output() {
    HTML::OutputHeader('裏・闇鍋モード配役テスト', 'test/chaos_verso', true);

    foreach (array('user_count' => 20, 'try_count' => 100) as $key => $value) {
      $$key = isset($_POST[$key]) && $_POST[$key] > 0 ? $_POST[$key] : $value;
    }
    $id_u = 'user_count';
    $id_t = 'try_count';

    echo <<<EOF
<form method="POST" action="chaos_verso.php">
<input type="hidden" name="command" value="role_test">
<label for="{$id_u}">人数</label><input type="text" id="{$id_u}" name="{$id_u}" size="2" value="{$$id_u}">
<label for="{$id_t}">試行回数</label><input type="text" id="{$id_t}" name="{$id_t}" size="2" value="{$$id_t}">
<input type="submit" value=" 実 行 "><br>
</form>

EOF;

    if (@$_POST['command'] == 'role_test') {
      Loader::LoadRequest('RequestBase'); //専用 Request を作るべき
      RQ::$get->TestItems = new StdClass();
      RQ::GetTest()->is_virtual_room = true;

      $stack = new StdClass();
      $stack->game_option = array('chaos_verso');
      $stack->option_role = array();

      RQ::SetTestRoom('game_option', implode(' ', $stack->game_option));
      RQ::SetTestRoom('option_role', implode(' ', $stack->option_role));
      DB::$ROOM = new Room(RQ::$get);
      DB::$ROOM->LoadOption();

      $user_count = @(int)$_POST['user_count'];
      $try_count  = @(int)$_POST['try_count'];
      $str = '%0' . strlen($try_count) . 'd回目: ';
      for ($i = 1; $i <= $try_count; $i++) {
	printf($str, $i);
	$role_list = Cast::GetRoleList($user_count);
	if ($role_list == '') break;
	Text::p(Vote::GenerateRoleNameList(array_count_values($role_list), true));
      }
    }
    HTML::OutputFooter(true);
  }
}

//-- 役職名表示 --//
class NameTest {
  const LABEL = '<input type="radio" name="type" id="%s" value="%s"><label for="%s">%s</label>';

  //出力
  static function Output() {
    HTML::OutputHeader('役職名表示', 'test/name', true);
    echo <<<EOF
<form method="POST" action="name_test.php">
<input type="hidden" name="command" value="name_test">
<input type="submit" value=" 実 行 "><br>
<input type="radio" name="type" value="all-all" checked>全て

EOF;

    $stack = new StdClass();
    foreach (array_keys(RoleData::$main_role_list) as $role) { //役職データ収集
      $stack->group[RoleData::DistinguishRoleGroup($role)][] = $role;
      $stack->camp[RoleData::DistinguishCamp($role, true)][] = $role;
    }
    $count = 0;
    foreach (array('camp' => '陣営', 'group' => '系') as $type => $name) {
      foreach (array_keys($stack->$type) as $role) {
	$count++;
	if ($count > 0 && $count % 9 == 0) Text::d();
	$value = $role . '-' . $type;
	$label = RoleData::$main_role_list[$role] . $name;
	Text::Output(sprintf(self::LABEL, $value, $value, $value, $label));
      }
    }
    Text::Output('</form>');
    self::Execute($stack);
    HTML::OutputFooter();
  }

  //実行処理
  private function Execute(StdClass $role_data) {
    if (@$_POST['command'] != 'name_test') return; //実行判定
    list($role, $type) = explode('-', @$_POST['type']);
    switch ($type) {
    case 'all':
      $stack = array_keys(RoleData::$main_role_list);
      break;

    case 'camp':
    case 'group':
      $stack = $role_data->{$type}[$role];
      break;

    default:
      return;
    }
    foreach ($stack as $role) Text::d(RoleData::GenerateMainRoleTag($role));
  }
}

//-- 異議ありテスト --//
class ObjectionTest {
  const URL   = 'objection_test.php';
  const RESET = '<p><a href="%s">リセット</a></p>%s<table>%s';

  //出力
  static function Output() {
    HTML::OutputHeader('異議ありテスト', null, true);
    printf(self::RESET, self::URL, "\n", "\n");
    $form = <<<EOF
<tr><td class="objection"><form method="POST" action="%s">
<input type="hidden" name="command" value="on">
<input type="hidden" name="set_objection" value="%s">
<input type="image" name="objimage" src="%s" border="0"> (%s)
</form></td></tr>%s
EOF;
    $image = JINRO_ROOT . '/' . GameConfig::OBJECTION_IMAGE;
    $stack = array(
      'entry'            => '入村',
      'full'             => '定員',
      'morning'          => '夜明け',
      'revote'           => '再投票',
      'novote'           => '未投票告知',
      'alert'            => '未投票警告',
      'objection_male'   => '異議あり(男)',
      'objection_female' => '異議あり(女)');
    foreach ($stack as $key => $value) printf($form, self::URL, $key, $image, $value, "\n");
    Text::Output('</table>');

    if ($_POST['command'] == 'on' && array_key_exists($_POST['set_objection'], $stack)) {
      Sound::Output($_POST['set_objection']);
    }
    HTML::OutputFooter();
  }
}

//-- トリップテスト --//
class TripTest {
  //出力
  static function Output() {
    HTML::OutputHeader('トリップテスト', null, true);
    echo <<<EOF
<form method="POST" action="trip_test.php">
<input type="hidden" name="command" value="on">
<label>トリップキー</label><input type="text" name="key" size="20" value="">
</form>

EOF;
    if ($_POST['command'] == 'on') Text::p(Text::ConvertTrip($_POST['key']), '変換結果');
    HTML::OutputFooter();
  }
}

//-- Twitter 投稿テスト --//
class TwitterTest {
  //出力
  static function Output() {
    HTML::OutputHeader('Twitter 投稿テスト', null, true);
    echo <<<EOF
<form method="POST" action="twitter_test.php">
<input type="hidden" name="command" value="on">
<table border="0">
<tr><td><label>番地</label></td><td><input type="text" name="number" size="5" value="1"></td></tr>
<tr><td><label>名前</label></td><td><input type="text" name="name" size="30" value=""></td></tr>
<tr><td><label>コメント</label></td><td><input type="text" name="comment" size="30" value=""></td></tr>
<tr><td colspan="2"><input type="submit" value=" 実 行 "></td></tr>
</table>
</form>

EOF;
    if ($_POST['command'] == 'on') {
      $number  = intval($_POST['number']);
      $name    = $_POST['name'];
      $comment = $_POST['comment'];
      if (JinroTwitter::Send($number, $name, $comment)) Text::d('Twitter 投稿成功');
    }
    HTML::OutputFooter();
  }
}

//投票テスト
class VoteTest {
  //投票画面出力
  static function OutputVote() {
    Loader::LoadFile('vote_message');

    $stack = new RequestGameVote();
    RQ::$get->vote      = $stack->vote;
    RQ::$get->target_no = $stack->target_no;
    RQ::$get->situation = $stack->situation;
    RQ::$get->back_url  = '<a href="vote_test.php">戻る</a>';

    if (RQ::$get->vote) { //投票処理
      HTML::OutputHeader('投票テスト', 'game_play', true); //HTMLヘッダ
      if (RQ::$get->target_no == 0) { //空投票検出
	HTML::OutputResult('空投票', '投票先を指定してください');
      }
      elseif (DB::$ROOM->IsDay()) { //昼の処刑投票処理
	//Vote::VoteDay();
      }
      elseif (DB::$ROOM->IsNight()) { //夜の投票処理
	Vote::VoteNight();
      }
      else { //ここに来たらロジックエラー
	VoteHTML::OutputError('投票コマンドエラー', '投票先を指定してください');
      }
    }
    else {
      RQ::$get->post_url = 'vote_test.php';
      DB::$SELF->last_load_scene = DB::$ROOM->scene;

      if (DB::$SELF->IsDead()) {
	DB::$SELF->IsDummyBoy() ? VoteHTML::OutputDummyBoy() : VoteHTML::OutputHeaven();
      }
      else {
	switch(DB::$ROOM->scene) {
	case 'beforegame':
	  VoteHTML::OutputBeforeGame();
	  break;

	case 'day':
	  VoteHTML::OutputDay();
	  break;

	case 'night':
	  VoteHTML::OutputNight();
	  break;

	default: //ここに来たらロジックエラー
	  VoteHTML::OutputError('投票シーンエラー');
	  break;
	}
      }
    }
    DB::$SELF = DB::$USER->ByID(1);
    GameHTML::OutputPlayer();
    HTML::OutputFooter(true);
  }

  //配役情報出力
  static function OutputCast() {
    Loader::LoadFile('chaos_config');

    HTML::OutputHeader('投票テスト', 'game_play', true);
    //Text::p(Lottery::ToProbability(ChaosConfig::$chaos_hyper_random_role_list));
    //Text::p(array_sum(ChaosConfig::$chaos_hyper_random_role_list));
    //Text::p(ChaosConfig::$role_group_rate_list);
    Text::Output('<table border="1" cellspacing="0">');
    echo '<tr><th>人口</th>';
    foreach (ChaosConfig::$role_group_rate_list as $group => $rate) {
      $role  = RoleData::DistinguishRoleGroup($group);
      $class = RoleData::DistinguishRoleClass($role);
      printf('<th class="%s">%s</th>', $class, RoleData::$short_role_list[$role]);
    }
    Text::Output('</tr>');
    for ($i = 8; $i <= 40; $i++) {
      printf('<tr align="right"><td><strong>%d</strong></td>', $i);
      foreach (ChaosConfig::$role_group_rate_list as $rate) {
	printf('<td>%d</td>', round($i / $rate));
      }
      Text::Output('</tr>');
    }
    Text::Output('</table>');
    HTML::OutputFooter(true);
  }
}
