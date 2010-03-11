<?php
//-- ȯ�������δ��쥯�饹 --//
class DocumentBuilder{
  var $actor;
  var $flag;
  var $extension_list = array();

  function DocumentBuilder(){ $this->__construct(); }
  function __construct(){
    global $USERS, $SELF;

    $this->actor = $USERS->ByVirtual($SELF->user_no); //���ۥ桼�������
    $this->LoadExtension();
    $this->SetFlag();
  }

  //�ե��륿�о��򿦤ξ�������
  function LoadExtension(){
    global $ROLES;

    $ROLES->actor = $this->actor;
    $this->extension_list = $ROLES->Load('talk');
  }

  //�ե��륿�ѥե饰�򥻥å�
  function SetFlag(){
    global $ROOM, $SELF;

    //Ƚ�����ѿ��򥻥å�
    $actor = $this->actor;
    $dummy_boy = $SELF->IsDummyBoy();

    //�ե饰�򥻥å�
    $this->flag->dummy_boy = $dummy_boy;
    $this->flag->common    = ($dummy_boy || $actor->IsCommon(true));
    $this->flag->wolf      = ($dummy_boy || $SELF->IsWolf(true) || $actor->IsRole('whisper_mad'));
    $this->flag->fox       = ($dummy_boy || $actor->IsFox(true));
    $this->flag->mind_read = ($ROOM->date > 1 && $SELF->IsLive());

    //ȯ�����������ե饰
    /*
      + �����ཪλ�������ɽ��
      + �����귯�ˤ�����ɽ��
      + �ɽ��������֤λ�Ԥˤ�����ɽ��
      + �ɽ�����վ��֤ϴ���Ԥ�Ʊ�� (��ɼ�����ɽ�����ʤ�)
    */
    $this->flag->open_talk = ($dummy_boy || $ROOM->IsFinished() ||
			      ($SELF->IsDead() && $ROOM->IsOpenCast()));
  }

  //ȯ���ơ��֥�إå�����
  function BeginTalk($class){
    $this->cache = '<table class="' . $class . '">' . "\n";
  }

  //����ȯ������
  function RawAddTalk($symbol, $user_info, $sentence, $volume, $row_class = '',
		      $user_class = '', $say_class = ''){
    global $GAME_CONF;

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
    if($RQ_ARGS->add_role){ //��ɽ���⡼���б�
      $real_user = $talk->scene == 'heaven' ? $user : $USERS->ByReal($user->user_no);
      $handle_name .= $real_user->GenerateShortRoleName();
    }

    $user_info = '<font style="color:'.$user->color.'">��</font>'.$handle_name;
    if($talk->type == 'self_talk' && ! $user->IsRole('dummy_common')){
      $user_info .= '<span>���Ȥ��</span>';
    }
    $volume = $talk->font_type;
    $sentence = $talk->sentence;
    foreach($this->extension_list as $extension){ //�ե��륿��󥰽���
      $extension->AddTalk($user, $talk, $user_info, $volume, $sentence);
    }
    $this->RawAddTalk('', $user_info, $sentence, $volume);
  }

  //�񤭽���
  function AddWhisper($role, $talk){
    global $ROLES;

    if(($user_info = $ROLES->GetWhisperingUserInfo($role, $user_class)) === false) return;
    $volume = $talk->font_type;
    $sentence = $ROLES->GetWhisperingSound($role, $talk, $say_class);
    foreach($this->extension_list as $extension){ //�ե��륿��󥰽���
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
