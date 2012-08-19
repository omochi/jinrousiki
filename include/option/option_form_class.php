<?php
//-- オプション入力画面表示クラス --//
class OptionForm {
  const SEPARATOR = "  <tr><td colspan=\"2\"><hr></td></tr>\n";
  const TEXTBOX = '<input type="%s" name="%s" id="%s" size="%d" value="">%s';
  const TEXTBOX_EXPLAIN = ' <span class="explain">%s</span>';
  const CHECKBOX = '<input type="%s" id="%s" name="%s" value="%s"%s> <span class="explain">%s</span>';
  const REALTIME = '(%s　昼：<input type="text" name="%s_day" value="%d" size="2" maxlength="2">分 夜：<input type="text" name="%s_night" value="%d" size="2" maxlength="2">分)';
  const SELECTOR = "  <option value=\"%s\"%s>%s</option>\n";

  private static $order = array(
    'room_name', 'room_comment', 'max_user', null,
    'wish_role', 'real_time', 'wait_morning', 'open_vote', 'seal_message', 'open_day',
    'necessary_name', 'necessary_trip', null,
    'dummy_boy_selector', 'gm_password', 'gerd', null,
    'not_open_cast_selector', null,
    'poison', 'assassin', 'wolf', 'boss_wolf', 'poison_wolf', 'possessed_wolf', 'sirius_wolf',
    'fox', 'child_fox', 'cupid', 'medium', 'mania', 'decide', 'authority', null,
    'liar', 'gentleman', 'sudden_death', 'perverseness', 'deep_sleep', 'mind_open', 'blinder',
    'critical', 'joker', 'death_note', 'detective', 'weather', 'festival', 'replace_human_selector',
    'change_common_selector', 'change_mad_selector', 'change_cupid_selector', null,
    'special_role', null,
    'topping', 'boost_rate', 'chaos_open_cast', 'sub_role_limit', 'secret_sub_role'
  );

  //出力
  static function Output() {
    foreach (self::$order as $name) {
      is_null($name) ? print(self::SEPARATOR) : self::Generate($name);
    }
  }

  //生成 (振り分け処理用)
  private function Generate($name) {
    $item = OptionManager::GetClass($name);
    if (! $item->enable || ! isset($item->type)) return;
    switch ($item->type) {
    case 'textbox':
    case 'password':
      $str = self::GenerateTextbox($item);
      break;

    case 'checkbox':
    case 'radio':
      $str = self::GenerateCheckbox($item);
      break;

    case 'realtime':
      $str = self::GenerateRealtime($item);
      break;

    case 'selector':
      $str = self::GenerateSelector($item);
      break;

    case 'group':
      $str = self::GenerateGroup($item);
      break;
    }
    $format = <<<EOF
  <tr>
    <td><label for="%s">%s：</label></td>
    <td>%s</td>
  </tr>%s
EOF;
    printf($format, $item->name, $item->GetCaption(), $str, "\n");
  }

  //テキストボックス生成
  private function GenerateTextbox(TextRoomOptionItem $item) {
    $size = sprintf('%s_input', $item->name);
    $str  = $item->GetExplain();
    return sprintf(self::TEXTBOX, $item->type, $item->name, $item->name, RoomConfig::$$size,
		   isset($str) ? sprintf(self::TEXTBOX_EXPLAIN, $str) : '');
  }

  //チェックボックス生成
  private function GenerateCheckbox(CheckRoomOptionItem $item) {
    $footer = isset($item->footer) ? $item->footer : sprintf('(%s)', $item->GetExplain());
    return sprintf(self::CHECKBOX, $item->type, $item->name, $item->form_name, $item->form_value,
		   $item->value ? ' checked' : '', Text::ConvertLine($footer));
  }

  //チェックボックス生成 (リアルタイム制専用)
  private function GenerateRealtime(Option_real_time $item) {
    $footer = sprintf(self::REALTIME, Text::ConvertLine($item->GetExplain()),
		      $item->name, TimeConfig::DEFAULT_DAY,
		      $item->name, TimeConfig::DEFAULT_NIGHT);
    return sprintf(self::CHECKBOX, 'checkbox', $item->name, $item->name, $item->form_value,
		   $item->value ? ' checked' : '', $footer);
  }

  //セレクタ生成
  private function GenerateSelector(SelectorRoomOptionItem $item) {
    $str = '';
    foreach ($item->GetItem() as $code => $child) {
      $label = $child instanceof RoomOptionItem ? $child->GetCaption() : $child;
      if (! is_string($code)) $code = $label;
      $str .= sprintf(self::SELECTOR, $code, $code == $item->value ? ' selected' : '', $label);
    }
    $explain = Text::ConvertLine($item->GetExplain());
    $format = <<<EOF
<select id="%s" name="%s">
<optgroup label="%s">
%s</optgroup>
</select>
<span class="explain">(%s)</span>
EOF;
    return sprintf($format, $item->name, $item->form_name, $item->label, $str, $explain);
  }

  //グループ生成
  private function GenerateGroup(RoomOptionItem $item) {
    $str  = '';
    foreach ($item->GetItem() as $child) {
      $type = $child->type;
      if (! empty($type)) {
	switch ($type) {
	case 'radio':
	  $str .= self::GenerateCheckbox($child);
	  break;
	}
	$str .= "<br>\n";
      }
    }
    return $str;
  }
}
