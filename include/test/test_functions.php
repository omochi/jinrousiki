<?php
//-- テスト村生成クラス --//
class DevRoom {
  //テスト村データ初期化
  static function Initialize($list = array()) {
    //初期村データを生成
    $base_list = array(
     'id' => RQ::Get()->room_no, 'comment' => '',
     'date' => 0, 'scene' => 'beforegame', 'status' => 'waiting',
     'game_option' => 'dummy_boy real_time:6:4 wish_role',
     'option_role' => '', 'vote_count' => 1
    );

    RQ::Set('room_no', 1);
    RQ::Set('vote_times', 1);
    RQ::Set('reverse_log', null);
    RQ::InitTestRoom();
    RQ::GetTest()->test_room = array_merge($base_list, $list);
    RQ::GetTest()->event           = array();
    RQ::GetTest()->result_ability  = array();
    RQ::GetTest()->result_dead     = array();
    RQ::GetTest()->system_message  = array();
  }

  //村データロード
  static function Load() {
    DB::$ROOM = new Room(RQ::Get());
    DB::$ROOM->test_mode    = true;
    DB::$ROOM->log_mode     = true;
    DB::$ROOM->scene        = 'beforegame';
    DB::$ROOM->revote_count = 0;
    if (! isset(DB::$ROOM->vote)) DB::$ROOM->vote = array();
  }

  //イベント情報取得
  static function GetEvent() {
    $stack = array();
    foreach (RQ::GetTest()->system_message as $date => $date_list) {
      //Text::p($date_list, $date);
      if ($date != DB::$ROOM->date) continue;
      foreach ($date_list as $type => $type_list) {
	switch ($type) {
	case 'WEATHER':
	case 'EVENT':
	case 'SAME_FACE':
	case 'VOTE_DUEL':
	case 'BLIND_VOTE':
	  foreach ($type_list as $event) {
	    $stack[] = array('type' => $type, 'message' => $event);
	  }
	  break;
	}
      }
    }
    return $stack;
  }

  //配役テスト
  static function Cast(StdClass $stack) {
    RQ::SetTestRoom('game_option', implode(' ', $stack->game_option));
    RQ::SetTestRoom('option_role', implode(' ', $stack->option_role));

    DB::$ROOM = new Room(RQ::Get());
    DB::$ROOM->LoadOption();
    //Text::p(DB::$ROOM);

    $user_count = RQ::Get()->user_count;
    $try_count  = RQ::Get()->try_count;
    $str = '%0' . strlen($try_count) . 'd回目: ';
    for ($i = 1; $i <= $try_count; $i++) {
      printf($str, $i);
      $role_list = Cast::GetRoleList($user_count);
      if ($role_list == '') break;
      Text::p(Vote::GenerateRoleNameList(array_count_values($role_list), true));
    }
  }
}

//-- テストユーザ生成クラス --//
class DevUser {
  // ユーザのアイコンカラーリスト
  static $icon_color_list = array('#DDDDDD', '#999999', '#FFD700', '#FF9900', '#FF0000',
				  '#99CCFF', '#0066FF', '#00EE00', '#CC00CC', '#FF9999');

  // ユーザの初期データ
  static $user_list = array(
     1 => array('uname'        => 'dummy_boy',
	       'handle_name'   => '身代わり君',
	       'icon_filename' => '../img/dummy_boy_user_icon.jpg',
	       'color'         => '#000000'),
     2 => array('uname'        => 'light_gray',
		'handle_name'  => '明灰'),
     3 => array('uname'        => 'dark_gray',
		'handle_name'  => '暗灰'),
     4 => array('uname'        => 'yellow',
		'handle_name'  => '黄色'),
     5 => array('uname'        => 'orange',
		'handle_name'  => 'オレンジ'),
     6 => array('uname'        => 'red',
		'handle_name'  => '赤'),
     7 => array('uname'        => 'light_blue',
		'handle_name'  => '水色'),
     8 => array('uname'        => 'blue',
		'handle_name'  => '青'),
     9 => array('uname'        => 'green',
		'handle_name'  => '緑'),
    10 => array('uname'        => 'purple',
		'handle_name'  => '紫'),
    11 => array('uname'        => 'cherry',
		'handle_name'  => 'さくら'),
    12 => array('uname'        => 'white',
		'handle_name'  => '白'),
    13 => array('uname'        => 'black',
		'handle_name'  => '黒'),
    14 => array('uname'        => 'gold',
		'handle_name'  => '金'),
    15 => array('uname'        => 'frame',
		'handle_name'  => '炎'),
    16 => array('uname'        => 'scarlet',
		'handle_name'  => '紅'),
    17 => array('uname'        => 'ice',
		'handle_name'  => '氷'),
    18 => array('uname'        => 'deep_blue',
		'handle_name'  => '蒼'),
    19 => array('uname'        => 'emerald',
		'handle_name'  => '翠'),
    20 => array('uname'        => 'rose',
		'handle_name'  => '薔薇'),
    21 => array('uname'        => 'peach',
		'handle_name'  => '桃'),
    22 => array('uname'        => 'gust',
		'handle_name'  => '霧'),
    23 => array('uname'        => 'cloud',
		'handle_name'  => '雲'),
    24 => array('uname'        => 'moon',
		'handle_name'  => '月'),
    25 => array('uname'        => 'sun',
		'handle_name'  => '太陽'),
			    );

  //ユーザデータ初期化
  static function Initialize($count, $role_list = array()) {
    RQ::GetTest()->test_users = array();
    for ($id = 1; $id <= $count; $id++) {
      RQ::GetTest()->test_users[$id] = new User(isset($role_list[$id]) ? $role_list[$id] : null);
    }

    foreach (self::$user_list as $id => $list) {
      if ($id > $count) break;
      foreach ($list as $key => $value) RQ::GetTest()->test_users[$id]->$key = $value;
    }
  }

  //ユーザデータ補完
  static function Complement($scene = 'beforegame') {
    foreach (RQ::GetTest()->test_users as $id => $user) {
      $user->room_no = RQ::Get()->room_no;
      $user->user_no = $id;
      if (! isset($user->sex)) $user->sex = $id % 2 == 0 ? 'female' : 'male';
      $user->role_id = $id;
      if (! isset($user->profile)) $user->profile = $id;
      if (! isset($user->live)) $user->live = 'live';
      $user->last_load_scene = $scene;
      if ($id > 1) {
	$user->color = self::$icon_color_list[($id - 2) % 10];
	$user->icon_filename = sprintf('%03d.gif', ($id - 2) % 10 + 1);
      }
    }
  }

  //ユーザ情報をロード
  static function Load() {
    DB::$USER = new UserData(RQ::Get());
    DB::$SELF = DB::$USER->ByID(1);
    if (DB::$ROOM->IsBeforeGame()) {
      foreach (DB::$USER->rows as $user) {
	if (! isset($user->vote_type)) $user->vote_type = 'GAME_START';
      }
    }
  }
}

//-- HTML 生成クラス (テスト拡張) --//
class DevHTML {
  //共通リクエストロード
  static function LoadRequest() {
    Loader::LoadRequest();
    RQ::Get()->ParsePostOn('execute');
  }

  static function IsExecute() {
    return RQ::Get()->execute;
  }

  // フォームヘッダ出力
  static function OutputFormHeader($title, $url) {
    self::LoadRequest();
    HTML::OutputHeader($title, 'test/role', true);
    foreach (array('user_count' => 20, 'try_count' => 100) as $key => $value) {
      RQ::Get()->ParsePostInt($key);
      $$key = RQ::Get()->$key > 0 ? RQ::Get()->$key : $value;
    }
    $id_u = 'user_count';
    $id_t = 'try_count';
    echo <<<EOF
<form method="post" action="{$url}">
<input type="hidden" name="execute" value="on">
<label for="{$id_u}">人数</label><input type="text" id="{$id_u}" name="{$id_u}" size="2" value="{$$id_u}">
<label for="{$id_t}">試行回数</label><input type="text" id="{$id_t}" name="{$id_t}" size="2" value="{$$id_t}">
<input type="submit" value=" 実 行 "><br>

EOF;
  }
}
