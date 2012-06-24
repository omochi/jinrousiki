<?php
//-- 開発支援クラス --//
class Dev {
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
  static function InitializeUser($count, $role_list = array()) {
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
  static function ComplementUser($scene = 'beforegame') {
    foreach (RQ::GetTest()->test_users as $id => $user) {
      $user->room_no = RQ::$get->room_no;
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
}