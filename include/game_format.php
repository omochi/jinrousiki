<?php
//-- 発言処理の基底クラス --//
class DocumentBuilder{
  var $actor;
  var $flag;
  var $extension_list = array();

  function DocumentBuilder(){ $this->__construct(); }
  function __construct(){
    global $USERS, $SELF;

    $this->actor = $USERS->ByVirtual($SELF->user_no); //仮想ユーザを取得
    $this->LoadExtension();
    $this->SetFlag();
  }

  //フィルタ対象役職の情報をロード
  function LoadExtension(){
    global $ROLES;

    $ROLES->actor = $this->actor;
    $this->extension_list = $ROLES->Load('talk');
  }

  //フィルタ用フラグをセット
  function SetFlag(){
    global $ROOM, $SELF;

    //判定用変数をセット
    $actor = $this->actor;
    $dummy_boy = $SELF->IsDummyBoy();

    //フラグをセット
    $this->flag->dummy_boy = $dummy_boy;
    $this->flag->common    = ($dummy_boy || $actor->IsCommon(true));
    $this->flag->wolf      = ($dummy_boy || $SELF->IsWolf(true) || $actor->IsRole('whisper_mad'));
    $this->flag->fox       = ($dummy_boy || $actor->IsFox(true));
    $this->flag->mind_read = ($ROOM->date > 1 && $SELF->IsLive());

    //発言完全公開フラグ
    /*
      + ゲーム終了後は全て表示
      + 身代わり君には全て表示
      + 霊界表示オン状態の死者には全て表示
      + 霊界表示オフ状態は観戦者と同じ (投票情報は表示しない)
    */
    $this->flag->open_talk = ($dummy_boy || $ROOM->IsFinished() ||
			      ($SELF->IsDead() && $ROOM->IsOpenCast()));
  }

  //発言テーブルヘッダ作成
  function BeginTalk($class){
    $this->cache = '<table class="' . $class . '">' . "\n";
  }

  //基礎発言処理
  function RawAddTalk($symbol, $user_info, $sentence, $volume, $row_class = '',
		      $user_class = '', $say_class = ''){
    global $GAME_CONF;

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
    if($RQ_ARGS->add_role){ //役職表示モード対応
      $real_user = $talk->scene == 'heaven' ? $user : $USERS->ByReal($user->user_no);
      $handle_name .= $real_user->GenerateShortRoleName();
    }

    $user_info = '<font style="color:'.$user->color.'">◆</font>'.$handle_name;
    if($talk->type == 'self_talk' && ! $user->IsRole('dummy_common')){
      $user_info .= '<span>の独り言</span>';
    }
    $volume = $talk->font_type;
    $sentence = $talk->sentence;
    foreach($this->extension_list as $extension){ //フィルタリング処理
      $extension->AddTalk($user, $talk, $user_info, $volume, $sentence);
    }
    $this->RawAddTalk('', $user_info, $sentence, $volume);
  }

  //囁き処理
  function AddWhisper($role, $talk){
    global $ROLES;

    if(($user_info = $ROLES->GetWhisperingUserInfo($role, $user_class)) === false) return;
    $volume = $talk->font_type;
    $sentence = $ROLES->GetWhisperingSound($role, $talk, $say_class);
    foreach($this->extension_list as $extension){ //フィルタリング処理
      $extension->AddWhisper($role, $talk, $user_info, $volume, $sentence);
    }
    $this->RawAddTalk('', $user_info, $sentence, $volume, '', $user_class, $say_class);
  }

  function AddSystemTalk($sentence, $class = 'system-user'){
    LineToBR($sentence);
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
