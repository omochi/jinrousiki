<?php
//-- ȯ�������δ��쥯�饹 --//
class DocumentBuilder{
  var $extension_list = array();

  function DocumentBuilder(){ $this->__construct(); }

  function __construct(){
    global $ROLES, $USERS, $SELF;

    //�ե��륿�о��򿦤ξ�������
    $ROLES->actor = $USERS->ByVirtual($SELF->user_no); //���ۥ桼�������
    $ROLES->Load('talk');
    $this->extension_list = $ROLES->loaded->class;
  }

  //ȯ���ơ��֥�إå�����
  function BeginTalk($class){
    $this->cache = '<table class="' . $class . '">' . "\n";
  }

  //����ȯ������
  function RawAddTalk($symbol, $user_info, $sentence, $volume, $row_class = '',
		      $user_class = '', $say_class = ''){
    global $RQ_ARGS;

    if($row_class  != '') $row_class  = ' ' . $row_class;
    if($user_class != '') $user_class = ' ' . $user_class;
    if($say_class  != '') $say_class  = ' ' . $say_class;
    LineToBR($sentence);
    if($GAME_CONF->quote_words) $sentence = '��' . $sentence . '��';

    $this->cache .= <<<WORDS
<tr class="user-talk{$row_class}">
<td class="user-name{$user_class}">{$symbol}{$user_info}</td>
<td class="say{$say_class} {$volume}">{$sentence}</td>
</tr>

WORDS;
  }

  //ɸ��Ū��ȯ������
  function AddTalk($user, $talk){
    global $GAME_CONF, $RQ_ARGS, $USERS;

    //ɽ����������
    $handle_name = $user->handle_name;
    if($RQ_ARGS->add_role) $handle_name .= $user->MakeShortRoleName(); //��ɽ���⡼���б�

    $user_info = '<font style="color:'.$user->color.'">��</font>'.$handle_name;
    if(strpos($talk->location, 'self_talk') !== false && ! $user->IsRole('dummy_common')){
      $user_info .= '<span>���Ȥ��</span>';
    }
    $volume = $talk->font_type;
    $sentence = $talk->sentence;
    foreach($this->extension_list as $extension){ //�ե��륿��󥰽���
      $extension->OnAddTalk($user, $talk, $user_info, $volume, $sentence);
    }
    $this->RawAddTalk('', $user_info, $sentence, $volume);
  }

  //�񤭽���
  function AddWhisper($role, $talk){
    global $GAME_CONF, $ROLES;

    if(($user_info = $ROLES->GetWhisperingUserInfo($role, $user_class)) === false) return;
    $volume = $talk->font_type;
    $sentence = $ROLES->GetWhisperingSound($role, $talk, $say_class);
    foreach($this->extension_list as $extension){ //�ե��륿��󥰽���
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

//-- ȯ���ե��륿����ѳ�ĥ���饹 --//
class DocumentBuilderExtension{
  function OnAddTalk($user, $talk, &$user_info, &$volume, &$sentence){}

  function OnAddWhisper($role, $talk, &$user_info, &$volume, &$sentence){}
}
