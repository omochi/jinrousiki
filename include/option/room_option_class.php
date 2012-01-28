<?php
require_once(dirname(__FILE__).'/room_option_item_class.php');
require_once(dirname(__FILE__).'/option_class.php');

class RoomOption extends OptionParser {
  static function Options() {
    global $GAME_OPT_CONF, $ROOM_CONF, $TIME_CONF;

    self::Category('BUILD');
    self::Add(self::NOT_OPTION, RoomOptionItem::Text('room_name', '村の名前', '')
            ->Size($ROOM_CONF->room_name_input)
            ->Footer('村')
            ->CollectOverride('NotOption')
            );
    self::Add(self::NOT_OPTION, RoomOptionItem::Text('room_comment', '村についての説明', '')
            ->Size($ROOM_CONF->room_comment_input)
            ->Footer('')
            ->CollectOverride('NotOption')
            );

    self::Add(self::NOT_OPTION, RoomOptionItem::Selector('max_user', '最大人数', '最大人数', '配役は<a href="info/rule.php">ルール</a>を確認して下さい')
            ->ItemSource($ROOM_CONF->max_user_list)
            ->Value($ROOM_CONF->default_max_user)
            ->CollectOverride('NotOption')
            );

    self::Category('GENERAL');
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('wish_role', '役割希望制', '希望の役割を指定できますが、なれるかは運です')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::RealTime('real_time', 'リアルタイム制', '制限時間が実時間で消費されます')
            ->Day($TIME_CONF->default_day)
            ->Night($TIME_CONF->default_night)
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('wait_morning', '早朝待機制', '夜が明けてから一定時間の間発言ができません')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('open_vote', '投票した票数を公表する', '「権力者」などのサブ役職が分かりやすくなります')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('seal_message', '天啓封印', '一部の個人通知メッセージが表示されなくなります')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('open_day', 'オープニングあり', 'ゲームが1日目「昼」からスタートします')
            );

    self::Category('DUMMY');
    self::Add(self::GAME_OPTION, RoomOptionItem::Group('dummy_boy', '初日の夜は身代わり君')
            ->Item(RoomOptionItem::Radio('dummy_boy', '', '', '身代わり君なし'))
            ->Item(RoomOptionItem::Radio('dummy_boy', 'on', '', '身代わり君あり (初日の夜、身代わり君が狼に食べられます)'))
            ->Item(RoomOptionItem::Radio('dummy_boy', 'gm_login', '', '仮想 GM が身代わり君としてログインします')->CollectOverride('CollectValue'))
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Password('gm_password', 'GM ログインパスワード', '(仮想 GM モード・クイズ村モード時の GM のパスワードです)<br>※ ログインユーザ名は「dummy_boy」です。GM は入村直後に必ず名乗ってください。')
            ->Size($ROOM_CONF->gm_password_input)
            ->CollectOverride('NotOption')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('gerd', 'ゲルト君モード', '役職が村人固定になります [村人が出現している場合のみ有効]')
            );

    self::Category('OPEN_CASTING');
    self::Add(self::GAME_OPTION, RoomOptionItem::Group('not_open_cast', '霊界で配役を公開しない')
            ->Item(RoomOptionItem::Radio('not_open_cast', '', '', '常時公開 (蘇生能力は無効です)')->CollectOverride('NotOption'))
            ->Item(RoomOptionItem::Radio('not_open_cast', 'not_open_cast', '', '常時非公開 (誰がどの役職なのか公開されません。蘇生能力は有効です)')->CollectOverride('CollectValue'))
            ->Item(RoomOptionItem::Radio('not_open_cast', 'auto_open_cast', '', '自動公開 (蘇生能力者などが能力を持っている間だけ霊界が非公開になります)')->CollectOverride('CollectValue'))
            );

    self::Category('CASTING');
    self::Add(self::ROLE_OPTION, 'poison');
    self::Add(self::ROLE_OPTION, 'assassin');
    self::Add(self::ROLE_OPTION, 'wolf');
    self::Add(self::ROLE_OPTION, 'boss_wolf');
    self::Add(self::ROLE_OPTION, 'poison_wolf');
    self::Add(self::ROLE_OPTION, 'possessed_wolf');
    self::Add(self::ROLE_OPTION, 'sirius_wolf');
    self::Add(self::ROLE_OPTION, 'fox');
    self::Add(self::ROLE_OPTION, 'child_fox');
    self::Add(self::ROLE_OPTION, 'cupid');
    self::Add(self::ROLE_OPTION, 'medium');
    self::Add(self::ROLE_OPTION, 'mania');
    self::Add(self::ROLE_OPTION, 'decide');
    self::Add(self::ROLE_OPTION, 'authority');

    self::Category('SPECIAL');
    self::Add(self::ROLE_OPTION, 'liar');
    self::Add(self::ROLE_OPTION, 'gentleman');
    self::Add(self::ROLE_OPTION, 'sudden_death');
    self::Add(self::ROLE_OPTION, 'perverseness');
    self::Add(self::GAME_OPTION, 'deep_sleep');
    self::Add(self::GAME_OPTION, 'mind_open');
    self::Add(self::GAME_OPTION, 'blinder');
    self::Add(self::ROLE_OPTION, 'critical');
    self::Add(self::GAME_OPTION, 'joker');
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('death_note', 'デスノート村', '毎日、誰か一人に「デスノート」が与えられます')
            );
    self::Add(self::GAME_OPTION, 'detective');
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('weather', '天候あり', '「天候」と呼ばれる特殊イベントが発生します')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('festival', 'お祭り村', '管理人がカスタムする特殊設定です')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Selector('replace_human', 'モード名', '村人置換村', '「村人」が全員特定の役職に入れ替わります')
            ->Item('', 'なし')
            ->Item('replace_human', '村人置換村')
            ->Item('full_mad', '狂人村')
            ->Item('full_cupid', 'キューピッド村')
            ->Item('full_quiz', '出題者村')
            ->Item('full_vampire', '吸血鬼村')
            ->Item('full_chiropetra', '蝙蝠村')
            ->Item('full_mania', '神話マニア村')
            ->Item('full_unknown_mania', '鵺村')
            ->CollectOverride('CollectValue')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Selector('change_common', 'モード名', '共有者置換村', '「共有者」が全員特定の役職に入れ替わります')
            ->Item('', 'なし')
            ->Item('change_common', '共有者置換村')
            ->Item('change_hermit_common', '隠者村')
            ->CollectOverride('CollectValue')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Selector('change_mad', 'モード名', '狂人置換村', '「狂人」が全員特定の役職に入れ替わります')
            ->Item('', 'なし')
            ->Item('change_mad', '狂人置換村')
            ->Item('change_fanatic_mad', '狂信者村')
            ->Item('change_whisper_mad', '囁き狂人村')
            ->Item('change_immolate_mad', '殉教者村')
            ->CollectOverride('CollectValue')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Selector('change_cupid', 'モード名', 'キューピッド置換村', '「キューピッド」が全員特定の役職に入れ替わります')
            ->Item('', 'なし')
            ->Item('change_cupid', 'キューピッド置換村')
            ->Item('change_mind_cupid', '女神村')
            ->Item('change_triangle_cupid', '小悪魔村')
            ->Item('change_angel', '天使村')
            ->CollectOverride('CollectValue')
            );

    self::Category('CHAOS');
    self::Add(self::GAME_OPTION, RoomOptionItem::Selector('special_role', 'モード名', '特殊配役モード', '詳細は<a href="info/game_option.php">ゲームオプション</a>を参照してください')
            ->Item('', 'なし')
            ->Item('chaos', '闇鍋モード')
            ->Item('chaosfull', '真・闇鍋モード')
            ->Item('chaos_hyper', '超・闇鍋モード')
            ->Item('chaos_verso', '裏・闇鍋モード')
            ->Item('duel', '決闘村')
            ->Item('gray_random', 'グレラン村')
            ->Item('quiz', 'クイズ村')
            ->CollectOverride('CollectValue')
            );

    self::Category('CHAOS_CASTING');
    self::Add(self::ROLE_OPTION, RoomOptionItem::Selector('topping', 'モード名', '固定配役追加モード', '固定配役に追加する役職セットです')
            ->ItemSource($GAME_OPT_CONF->topping_items)
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Selector('boost_rate', 'モード名', '出現率変動モード', '役職の出現率に補正がかかります')
            ->ItemSource($GAME_OPT_CONF->boost_rate_items)
            );

    self::Add(self::ROLE_OPTION, RoomOptionItem::Group('chaos_open_cast', '配役を通知する')
            ->Item(RoomOptionItem::Radio('chaos_open_cast', '', '', '通知無し')->CollectOverride('NotOption'))
            ->Item(RoomOptionItem::Radio('chaos_open_cast', 'camp', '', '陣営通知 (陣営毎の合計を通知)')->CollectOverride('CollectId'))
            ->Item(RoomOptionItem::Radio('chaos_open_cast', 'role', '', '役職通知 (役職の種類別に合計を通知)')->CollectOverride('CollectId'))
            ->Item(RoomOptionItem::Radio('chaos_open_cast', 'full', '', '完全通知 (通常村相当)'))
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Group('sub_role_limit', 'サブ役職制限')
            ->Item(RoomOptionItem::Radio('sub_role_limit', 'no_sub_role', '', 'サブ役職をつけない')->CollectOverride('CollectValue'))
            ->Item(RoomOptionItem::Radio('sub_role_limit', 'easy', '', 'サブ役職制限：EASYモード')->CollectOverride('CollectId'))
            ->Item(RoomOptionItem::Radio('sub_role_limit', 'normal', '', 'サブ役職制限：NORMALモード')->CollectOverride('CollectId'))
            ->Item(RoomOptionItem::Radio('sub_role_limit', 'hard', '', 'サブ役職制限：HARDモード')->CollectOverride('CollectId'))
            ->Item(RoomOptionItem::Radio('sub_role_limit', '', '', 'サブ役職制限なし')->CollectOverride('NotOption'))
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('secret_sub_role', 'サブ役職を表示しない', 'サブ役職が分からなくなります：闇鍋モード専用オプション')
            );

    self::End();
  }

  static $icon_order = array(
    'wish_role', 'real_time', 'dummy_boy', 'gm_login', 'gerd', 'wait_morning', 'open_vote',
    'seal_message', 'open_day', 'not_open_cast', 'auto_open_cast', 'poison', 'assassin', 'wolf',
    'boss_wolf', 'poison_wolf', 'possessed_wolf', 'sirius_wolf', 'fox', 'child_fox', 'cupid',
    'medium', 'mania', 'decide', 'authority', 'detective', 'liar', 'gentleman', 'deep_sleep',
    'blinder', 'mind_open', 'sudden_death', 'perverseness', 'critical', 'joker', 'death_note',
    'weather', 'festival', 'replace_human', 'full_mad', 'full_cupid', 'full_quiz', 'full_vampire',
    'full_chiroptera', 'full_mania', 'full_unknown_mania', 'change_common', 'change_hermit_common',
    'change_mad', 'change_fanatic_mad', 'change_whisper_mad', 'change_immolate_mad', 'change_cupid',
    'change_mind_cupid', 'change_triangle_cupid', 'change_angel', 'duel', 'gray_random', 'quiz',
    'chaos', 'chaosfull', 'chaos_hyper', 'chaos_verso', 'topping', 'boost_rate', 'chaos_open_cast',
    'chaos_open_cast_camp', 'chaos_open_cast_role', 'secret_sub_role', 'no_sub_role',
    'sub_role_limit_easy', 'sub_role_limit_normal', 'sub_role_limit_hard');

  static $definitions = array();
  static $categories = array();
  static $currentCategory = 'general';

  //これらのプロパティは設定されたオプションのゲーム用/役職用の分割に使用されている。詳しくはGetOptionStringメソッドを見よ。
  //異なるパラメータで同じクラスのグローバル変数を複数生成できるようになった場合、またはroomテーブルのオプション属性が統合された場合、
  //これらのプロパティを使用する必要はなくなると思われる。(2012-01-15 enogu)
  const NOT_OPTION = '';
  const GAME_OPTION = 'game_option';
  const ROLE_OPTION = 'role_option';
  var $groups = array();

  static function Category($category) {
    self::$categories[$category] = array();
    self::$currentCategory = $category;
  }

  static function End() {
    self::$currentCategory = null;
  }

  private static function SetGroup($group, $item) {
    $item->group = $group;
    if ($item instanceof RoomOptionItemGroup) {
      foreach ($item->items as $child) {
        self::SetGroup($group, $child);
      }
    }
  }

  static function Add($group, $item) {
		if (is_string($item)) {
			require_once(dirname(__FILE__)."/{$item}.php");
			$class = 'Option_'.$item;
			$item = new $class;
		}
    if ($item->enable && isset(self::$categories[self::$currentCategory])) {
      self::SetGroup($group, $item);
      self::$definitions[$item->name] = $item;
      self::$categories[self::$currentCategory][] = $item->name;
    }
  }

  static function Wrap($option) {
    $result = new RoomOption();
    foreach (func_get_args() as $opt) {
      if ($opt instanceof OptionParser) {
        array_merge($result->options, $opt->options);
      }
      else if (is_string($opt)) {
        $result->Option($opt);
      }
    }
    return $result;
  }

  function  __construct($value = '') {
    if (count(self::$definitions) == 0) {
      self::Options();
    }
    parent::__construct($value);
  }

  function OutputCategory($category, $border = false) {
    OutputView(self::$categories[$category], $border);
  }
  function OutputView($items = 'all', $border = false) {
    if ($items == 'all') {
      $items = array_keys(self::$definitions);
    }
    else if (!is_array($items)) {
      $items = array_flip(func_get_args());
    }
    foreach(self::$categories as $category) {
      $target = array_intersect($category, $items);
      if (!empty($target)) {
        foreach ($target as $item) {
          echo(self::$definitions[$item]->GenerateView());
        }
        if ($border) {
          echo '<tr><td colspan="2"><hr></td></tr>';
        }
      }
    }
  }

  function LoadPostParams($target = null) {
    $row = '';
    //$targetがNULLの場合、func_get_argsは要素数1の配列を返してしまうため、そのまま通過させる。
    if (isset($target) && !is_array($target)) {
      $target = func_get_args();
    }
    $this->_LoadPostParams(array_intersect_key(self::$definitions, $_POST), $target);
    $this->row = $row;
  }

  private function _LoadPostParams($items, $target) {
    $collectAll = empty($target);
    foreach ($items as $def) {
      if ($def instanceof RoomOptionItemGroup) {
        $this->_LoadPostParams($def->items, $target);
      }
      else if ($collectAll || in_array($def->name, $target)) {
        $this->currentGroup = $def->group;
        $def->CollectPostParam($this);
      }
    }
    $this->currentGroup = self::NOT_OPTION;
  }

  function Set($item, $name, $value) {
    if ($item instanceof RoomOptionItem) {
      $this->groups[$item->group][$name] = true;
    }
    else {
      $this->groups[$item][$name] = true;
    }
    parent::__set($name, $value);
  }

  function GetCaption($name) {
    if (isset(self::$definitions[$name])) {
      return self::$definitions[$name]->caption;
    }
    return false;
  }

  function GetMessage($name) {
    if (isset(self::$definitions[$name])) {
      return self::$definitions[$name]->description;
    }
    return false;
  }

  function GetOptionString($type = null) {
		if (!isset($type)) {
      return $this->ToString();
		}
    elseif (isset($this->groups[$type])) {
      return $this->ToString(array_keys($this->groups[$type]));
    }
  }

  /** ゲームオプションの画像タグを作成する */
  function GenerateImageList() {
    global $ROOM_IMG, $CAST_CONF, $GAME_OPT_MESS;

    $str = '';
    foreach(self::$icon_order as $option){
      if(!(isset($this->$option) && $GAME_OPT_MESS->$option)) {
	      continue;
			}
			$footer = '';
			$sentence = $GAME_OPT_MESS->$option;
			if(property_exists($CAST_CONF, $option) && is_int($CAST_CONF->$option)){
				$sentence .= '(' . $CAST_CONF->$option . '人～)';
			}
			switch($option){
			case 'real_time':
        list($day, $night) = $this->options[$option];
        $sentence .= "　昼： {$day} 分　夜： {$night} 分";
				$footer = '['. $day . '：' . $night . ']';
				break;

			case 'topping':
			case 'boost_rate':
				$type = $this->options[$option][0];
				$sentence .= '(Type' . $GAME_OPT_MESS->{$option . '_' . $type} . ')';
				$footer = '['. strtoupper($type) . ']';
				break;
			}
			$str .= $ROOM_IMG->Generate($option, $sentence) . $footer;
    }
    return $str;
  }
}
