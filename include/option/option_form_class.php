<?php
/**
 * オプション入力画面を表示するためのツールを提供します。
 * @author enogu
 */
class OptionForm {
  static function Output() {
    self::GenerateRow('room_name');
    self::GenerateRow('room_comment');
    self::GenerateRow('max_user');

    self::HorizontalRule();

    self::GenerateRow('wish_role');
    self::GenerateRow('real_time');
    self::GenerateRow('wait_morning');
    self::GenerateRow('open_vote');
    self::GenerateRow('seal_message');
    self::GenerateRow('open_day');

    self::HorizontalRule();

    self::GenerateRow('dummy_boy_selector');
    self::GenerateRow('gm_password');
    self::GenerateRow('gerd');

    self::HorizontalRule();

    self::GenerateRow('not_open_cast_selector');

    self::HorizontalRule();

    self::GenerateRow('poison');
    self::GenerateRow('assassin');
    self::GenerateRow('wolf');
    self::GenerateRow('boss_wolf');
    self::GenerateRow('poison_wolf');
    self::GenerateRow('possessed_wolf');
    self::GenerateRow('sirius_wolf');
    self::GenerateRow('fox');
    self::GenerateRow('child_fox');
    self::GenerateRow('cupid');
    self::GenerateRow('medium');
    self::GenerateRow('mania');
    self::GenerateRow('decide');
    self::GenerateRow('authority');

    self::HorizontalRule();

    self::GenerateRow('liar');
    self::GenerateRow('gentleman');
    self::GenerateRow('sudden_death');
    self::GenerateRow('perverseness');
    self::GenerateRow('deep_sleep');
    self::GenerateRow('mind_open');
    self::GenerateRow('blinder');
    self::GenerateRow('critical');
    self::GenerateRow('joker');
    self::GenerateRow('death_note');
    self::GenerateRow('detective');
    self::GenerateRow('weather');
    self::GenerateRow('festival');
    self::GenerateRow('replace_human_selector');
    self::GenerateRow('change_common_selector');
    self::GenerateRow('change_mad_selector');
    self::GenerateRow('change_cupid_selector');

    self::HorizontalRule();

    self::GenerateRow('special_role');

    self::HorizontalRule();

    self::GenerateRow('topping');
    self::GenerateRow('boost_rate');

    self::GenerateRow('chaos_open_cast');
    self::GenerateRow('sub_role_limit');
    self::GenerateRow('secret_sub_role');
  }

  function GenerateRow($name) {
    $item = OptionManager::GetClass($name);
    if (! $item->enable || ! isset($item->formtype)) return;

    $type = $item->formtype;
    $item->LoadMessages();
    echo <<<HTML
  <tr>
  <td><label for="{$item->name}">{$item->caption}：</label></td>
  <td>
HTML;
    self::$type($item);
    echo <<<HTML
  </td>
  </tr>
HTML;
  }

  function HorizontalRule() {
    echo '<tr><td colspan="2"><hr></td></tr>';
  }

  function textbox(RoomOptionItem $item, $type = 'textbox') {
    $footer = isset($item->footer) ? $item->footer : '('.$item->explain.')';
    $footer = Text::LineToBR($footer);
    $size = isset($item->size) ? 'size="'.$item->size.'"' : '';
    echo <<<HTML
<input type="{$type}" id="{$item->name}" name="{$item->formname}" {$size} value="{$item->value}">
<span class="explain">$footer</span>
HTML;
  }

  function password(RoomOptionItem $item) {
    self::textbox($item, 'password');
  }

  function checkbox(RoomOptionItem $item, $type = 'checkbox') {
    $footer = isset($item->footer) ? $item->footer : '('.$item->explain.')';
    $footer = Text::LineToBR($footer);
    $checked = $item->value ? ' checked' : '';
    echo <<<HTML
<input type="{$type}" id="{$item->name}" name="{$item->formname}" value="{$item->formvalue}"{$checked}>
<span class="explain">{$footer}</span>

HTML;
  }

  function radio(RoomOptionItem $item) {
    self::checkbox($item, 'radio');
  }

  function select(RoomOptionItem $item) {
    $options = '';
    foreach ($item->GetItems() as $code => $child) {
      if ($child instanceof RoomOptionItem) {
	$child->LoadMessages();
	$label = $child->caption;
      }
      else {
	$label = $child;
      }
      if (!is_string($code)) {
	$code = $label;
      }
      $selected = $code == $item->value ? ' selected' : '';
      $options .= "<option value=\"{$code}\" {$selected}>{$label}</option>\n";
    }
    $explain = Text::LineToBR($item->explain);
    echo <<<HTML
<select id="{$item->name}" name="{$item->formname}">
<optgroup label="{$item->label}">
{$options}</optgroup>
</select>
<span class="explain">({$explain})</span>
HTML;
  }

  function realtime(Option_real_time $item) {
    $checked = $item->value ? ' checked' : '';
    $explain = Text::LineToBR($item->explain);
    echo <<<HTML
<input type="checkbox" id="{$item->name}" name="{$item->formname}" value="on"{$checked}>
<span class='explain'>({$explain}　昼：<input type="text" name="{$item->formname}_day" value="{$item->defaultDayTime}" size="2" maxlength="2">分 夜：<input type="text" name="{$item->formname}_night" value="{$item->defaultNightTime}" size="2" maxlength="2">分)</span>
</td>

HTML;
  }

  function group(RoomOptionItem $item) {
    foreach ($item->GetItems() as $key => $child) {
      $type = $child->formtype;
      if (! empty($type)) {
	$child->LoadMessages();
	if ($type == 'radio') {
	  $child->formname = $item->formname;
	  $child->formvalue = $key;
	}
	self::$type($child);
	echo "<br>\n";
      }
    }
  }
}
