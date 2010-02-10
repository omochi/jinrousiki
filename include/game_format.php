<?php
//-- 発言処理の基底クラス --//
class DocumentBuilder{
  var $extension_list = array();

  function DocumentBuilder(){ $this->__construct(); }

  function __construct(){
    global $ROLES, $USERS, $SELF;

    //フィルタ対象役職の情報をロード
    $ROLES->actor = $USERS->ByVirtual($SELF->user_no); //仮想ユーザを取得
    $ROLES->Load('talk');
    $this->extension_list = $ROLES->loaded->class;
  }

  //発言テーブルヘッダ作成
  function BeginTalk($class){
    $this->cache = '<table class="' . $class . '">' . "\n";
  }

  //基礎発言処理
  function RawAddTalk($symbol, $user_info, $sentence, $volume, $row_class = '',
		      $user_class = '', $say_class = ''){
    global $RQ_ARGS;

    if($row_class  != '') $row_class  = ' ' . $row_class;
    if($user_class != '') $user_class = ' ' . $user_class;
    if($say_class  != '') $say_class  = ' ' . $say_class;
    LineToBR($sentence);
    if($GAME_CONF->quote_words) $sentence = '「' . $sentence . '」';

    $this->cache .= <<<WORDS
<tr class="user-talk{$row_class}">
<td class="user-name{$user_class}">{$symbol}{$user_info}</td>
<td class="say{$say_class} {$volume}">{$sentence}</td>
</tr>

WORDS;
  }

  //標準的な発言処理
  function AddTalk($user, $talk){
    global $GAME_CONF, $RQ_ARGS, $USERS;

    //表示情報を抽出
    $handle_name = $user->handle_name;
    if($RQ_ARGS->add_role) $handle_name .= $user->MakeShortRoleName(); //役職表示モード対応

    $user_info = '<font style="color:'.$user->color.'">◆</font>'.$handle_name;
    if(strpos($talk->location, 'self_talk') !== false && ! $user->IsRole('dummy_common')){
      $user_info .= '<span>の独り言</span>';
    }
    $volume = $talk->font_type;
    $sentence = $talk->sentence;
    foreach($this->extension_list as $extension){ //フィルタリング処理
      $extension->OnAddTalk($user, $talk, $user_info, $volume, $sentence);
    }
    $this->RawAddTalk('', $user_info, $sentence, $volume);
  }

  //囁き処理
  function AddWhisper($role, $talk){
    global $GAME_CONF, $ROLES;

    if(($user_info = $ROLES->GetWhisperingUserInfo($role, $user_class)) === false) return;
    $volume = $talk->font_type;
    $sentence = $ROLES->GetWhisperingSound($role, $talk, $say_class);
    foreach($this->extension_list as $extension){ //フィルタリング処理
      $extension->OnAddWhisper($role, $talk, $user_info, $volume, $sentence);
    }
    $this->RawAddTalk('', $user_info, $sentence, $volume, '', $user_class, $say_class);
  }

  function AddSystemTalk($sentence, $class = 'system-user'){
    $this->cache .= <<<WORDS
<tr>
<td class="{$class}" colspan="2">{$sentence}</td>
</tr>

WORDS;
  }

  function AddSystemMessage($class, $sentence, $add_class = ''){
    if($add_class != '') $add_class = ' ' . $add_class;
    $this->cache .= <<<WORDS
<tr class="system-message{$add_class}">
<td class="{$class}" colspan="2">{$sentence}</td>
</tr>

WORDS;
  }

  function EndTalk(){
    echo $this->cache.'</table>'."\n";
    $this->cache = '';
  }
}

//-- 発言フィルタリング用拡張クラス --//
class DocumentBuilderExtension{
  function OnAddTalk($user, $talk, &$user_info, &$volume, &$sentence){}

  function OnAddWhisper($role, $talk, &$user_info, &$volume, &$sentence){}
}
