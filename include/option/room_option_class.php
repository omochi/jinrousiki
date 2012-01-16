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
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('poison', '埋毒者登場', '処刑されたり狼に食べられた場合、道連れにします [村人2→埋毒1・人狼1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('assassin', '暗殺者登場', '夜に村人一人を暗殺することができます [村人2→暗殺者1・人狼1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('wolf', '人狼追加', '人狼をもう一人追加します [村人1→人狼1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('boss_wolf', '白狼登場', '占い結果が「村人」・霊能結果が「白狼」と表示される狼です [人狼1→白狼1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('poison_wolf', '毒狼登場', '処刑時にランダムで村人一人を巻き添えにする狼です<br>　　　[人狼1→毒狼1 / 村人1→薬師1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('possessed_wolf', '憑狼登場', '襲撃した人に憑依して乗っ取ってしまう狼です [人狼1→憑狼1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('sirius_wolf', '天狼登場', '仲間が減ると特殊能力が発現する狼です [人狼1→天狼1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('fox', '妖狐追加', '妖狐をもう一人追加します [村人1→妖狐1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('child_fox', '子狐登場', '限定的な占い能力を持ち、占い結果が「村人」・霊能結果が「子狐」となる妖狐です <br>　　　[妖狐1→子狐1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('cupid', 'キューピッド登場', '初日夜に選んだ相手を恋人にします。恋人となった二人は勝利条件が変化します<br>　　　[村人1→キューピッド1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('medium', '巫女登場', '突然死した人の所属陣営が分かります [村人2→巫女1・女神1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('mania', '神話マニア登場', '初日夜に他の村人の役職をコピーします [村人1→神話マニア1]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('decide', '決定者登場', '投票が同数の時、決定者の投票先が優先されます [兼任]')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('authority', '権力者登場', '投票の票数が二票になります [兼任]')
            );

    self::Category('SPECIAL');
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('liar', '狼少年村', 'ランダムで「狼少年」がつきます')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('gentleman', '紳士・淑女村', '全員に性別に応じた「紳士」「淑女」がつきます')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('sudden_death', '虚弱体質村', '全員に投票でショック死するサブ役職のどれかがつきます')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('perverseness', '天邪鬼村', '全員に「天邪鬼」がつきます。一部のサブ役職系オプションが強制オフになります')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('deep_sleep', '静寂村', '全員に「爆睡者」がつきます')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('mind_open', '白夜村', '全員に「公開者」がつきます')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('blinder', '宵闇村', '全員に「目隠し」がつきます')
            );
    self::Add(self::ROLE_OPTION, RoomOptionItem::Check('critical', '急所村', '全員に「会心」「痛恨」がつきます。')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('joker', 'ババ抜き村', '誰か一人に「ジョーカー」がつきます')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('death_note', 'デスノート村', '毎日、誰か一人に「デスノート」が与えられます')
            );
    self::Add(self::GAME_OPTION, RoomOptionItem::Check('detective', '探偵村', '「探偵」が登場し、初日の夜に全員に公表されます')
            );
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
    if ($item->enable && isset(self::$categories[self::$currentCategory])) {
      self::SetGroup($group, $item);
      self::$definitions[$item->name] = $item;
      self::$categories[self::$currentCategory][] = $item->name;
    }
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
    if (isset($this->groups[$type])) {
      return $this->ToString(array_keys($this->groups[$type]));
    }
    else {
      return $this->ToString();
    }
  }
}
