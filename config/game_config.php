<?php
//-- ¼���ƥʥ󥹡��������� --//
class RoomConfig{
  //¼��κǸ��ȯ��������¼�ˤʤ�ޤǤλ��� (��)
  //(���ޤ�û��������������ȶ��礹���ǽ������)
  var $die_room = 1200;
  #var $die_room = 12000; //�ƥ�����

  //��������ץ쥤��ǽ¼��
  var $max_active_room = 4;

  //����¼��Ω�Ƥ���ޤǤ��Ԥ����� (��)
  var $establish_wait = 120;

  //��λ����¼�Υ桼���Υ��å���� ID �ǡ����򥯥ꥢ����ޤǤλ��� (��)
  //���λ�����Ǥ���С������ڡ����˺���¼�Υ�󥯤��и����ޤ�
  var $clear_session_id = 86400; //24����

  //����Ϳ��Υꥹ�� (RoomImage->max_user_list ��Ϣư������ �� ���ߤ�����)
  var $max_user_list = array(8, 16, 22, 32, 50);
  var $default_max_user = 22; //�ǥե���Ȥκ���Ϳ� ($max_user_list �ˤ����ͤ�����뤳��)

  //-- OutputCreateRoom() --//
  var $room_name = 60; //¼̾�κ���ʸ����
  var $room_comment = 60; //¼�������κ���ʸ����
  var $ng_word = '/http:\/\//i'; //���϶ػ�ʸ���� (����ɽ��)

  //�ƥ��ץ�����ͭ���� [true:���� / false:���ʤ�]
  //�ǥե���Ȥǥ����å��� [true:�Ĥ��� / false:�Ĥ��ʤ�]
  var $wish_role = true; //����˾��
  var $default_wish_role = false;

  var $real_time = true; //�ꥢ�륿������ (�������� TimeConfig->default_day/night ����)
  var $default_real_time = true;

  var $open_vote = true; //��ɼ����ɼ�����ɽ����
  var $default_open_vote = false;

  var $open_day = true; //�����ץ˥󥰤���
  var $default_open_day = false;

  var $dummy_boy = true; //��������Ͽ����귯
  var $default_dummy_boy = true;

  var $gerd = true; //����ȷ��⡼��
  var $default_gerd = false;

  var $not_open_cast = true; //��������������ʤ�
  var $auto_open_cast = true; //��������ư�Ǹ�������

  //����ե⡼�ɤΥǥե���� [NULL:̵�� / 'auto':��ư���� / 'full': �������� ]
  var $default_not_open_cast = 'auto';

  var $poison = true; //���ǼԽи� (ɬ�׿Ϳ��� CastConfig->poison ����)
  var $default_poison = true;

  var $assassin = true; //�Ż��Խи� (ɬ�׿Ϳ��� CastConfig->assassin ����)
  var $default_assassin = false;

  var $boss_wolf = true; //��ϵ�и� (ɬ�׿Ϳ��� CastConfig->boss_wolf ����)
  var $default_boss_wolf = false;

  var $poison_wolf = true; //��ϵ�и� (ɬ�׿Ϳ��� CastConfig->poison_wolf ����)
  var $default_poison_wolf = false;

  var $possessed_wolf = true; //��ϵ�и� (ɬ�׿Ϳ��� CastConfig->possessed_wolf ����)
  var $default_possessed_wolf = false;

  var $sirius_wolf = true; //ŷϵ�и� (ɬ�׿Ϳ��� CastConfig->sirius_wolf ����)
  var $default_sirius_wolf = false;

  var $cupid = true; //���塼�ԥåɽи� (ɬ�׿Ϳ��� CastConfig->cupid ����)
  var $default_cupid = false;

  var $medium = true; //����и� (ɬ�׿Ϳ��� CastConfig->medium ����)
  var $default_medium = false;

  var $mania = true; //���åޥ˥��и� (ɬ�׿Ϳ��� CastConfig->mania ����)
  var $default_mania = false;

  var $decide = true; //����Խи� (ɬ�׿Ϳ��� CastConfig->decide ����)
  var $default_decide = true;

  var $authority = true; //���ϼԽи� (ɬ�׿Ϳ��� CastConfig->authority ����)
  var $default_authority = true;

  var $liar = true; //ϵ��ǯ¼
  var $default_liar = false;

  var $gentleman = true; //�»Ρ��ʽ�¼
  var $default_gentleman = false;

  var $sudden_death = true; //�����μ�¼
  var $default_sudden_death = false;

  var $perverseness = true; //ŷ�ٵ�¼
  var $default_perverseness = false;

  var $critical = true; //�޽�¼
  var $default_critical = false;

  var $detective = true; //õ��¼
  var $default_detective = false;

  var $festival = true; //���פ�¼
  var $default_festival = false;

  var $replace_human = true; //¼���ִ�¼
  var $full_mania = true; //���åޥ˥�¼
  var $full_chiroptera = true; //����¼
  var $full_cupid = true; //���塼�ԥå�¼
  //�ִ��⡼�ɤ����� (replace_human�������ͥ�������⡼��)
  var $replace_human_list = array('full_mania', 'full_chiroptera', 'full_cupid', 'replace_human');

  var $chaos = true; //����⡼��
  var $chaosfull = true; //��������⡼��
  var $chaos_hyper = true; //Ķ������⡼��

  //����⡼�ɤΥǥե����
  //[NULL:�̾��ϵ / 'chaos':�̾���� / 'chaosfull':�������� / 'chaos_hyper':Ķ������]
  var $default_chaos = NULL; //�̾��ϵ

  var $chaos_open_cast = true; //����������ɽ������ (����⡼�����ѥ��ץ����)
  var $chaos_open_cast_camp = true; //�ر���������ɽ������ (����⡼�����ѥ��ץ����)
  var $chaos_open_cast_role = true; //�򿦤μ�����������ɽ������ (����⡼�����ѥ��ץ����)

  //���Υ⡼�ɤΥǥե���� [NULL:̵�� / 'camp':�ر� / 'role':�� / 'full':����]
  var $default_chaos_open_cast = 'camp'; //�ر�����

  var $secret_sub_role = true; //�����򿦤��ܿͤ����Τ��ʤ� (����⡼�����ѥ��ץ����)
  var $default_secret_sub_role = false;

  var $sub_role_limit = true; //���������� (����⡼�����ѥ��ץ����)
  var $sub_role_limit_easy   = true; //���������¡�EASY�⡼��
  var $sub_role_limit_normal = true; //���������¡�NORMAL�⡼��
  var $no_sub_role = true; //�����򿦤�Ĥ��ʤ�
  //���������¤Υǥե���� [NULL:����̵�� / no:�Ĥ��ʤ� / easy:EASY�⡼�� / normal:NORMAL�⡼��]
  var $default_sub_role_limit = 'no'; //�Ĥ��ʤ� (no_sub_role)

  var $quiz = true; //������¼
  var $default_quiz = false;

  var $duel = true; //��Ʈ¼
  var $default_duel = false;
}

//-- ���������� --//
class GameConfig{
  //-- ������Ͽ --//
  //��¼���� (Ʊ��������Ʊ�� IP ��ʣ����Ͽ) (true�����Ĥ��ʤ� / false�����Ĥ���)
  var $entry_one_ip_address = true;

  //�ȥ�å��б� (true���Ѵ����� / false�� "#" ���ޤޤ�Ƥ����饨�顼���֤�)
  var $trip = true;
  var $trip_2ch = true; //2ch �ߴ� (12���б�) �⡼�� (true��ͭ�� / false��̵��)

  //ʸ��������
  var $entry_uname_limit = 50; //�桼��̾��¼�ͤ�̾��
  var $entry_profile_limit = 300; //�ץ�ե�����

  //-- ɽ������ --//
  var $quote_words = false; //ȯ����֡פǳ��
  var $display_talk_limit = 500; //�����೫�������ȯ��ɽ�����θ³���

  //-- ��ɼ --//
  var $self_kick = true; //��ʬ�ؤ� KICK (true��ͭ�� / false��̵��)
  var $kick = 3; //��ɼ�� KICK ������Ԥ���
  var $draw = 5; //����ɼ�����ܤǰ���ʬ���Ȥ��뤫

  //-- �򿦤�ǽ������ --//
  //��ǽ�ϼԤ��ߤä��ݤ˴������ޤ���о� (true:��ɼ�ԥ����� / false:����������)
  var $poison_only_voter = false; //1.3 �ϤΥǥե���Ȥ� false

  //ϵ����ǽ�ϼԤ������ݤ˴������ޤ���о� (true:��ɼ�Ը��� / false:������)
  var $poison_only_eater = true; //1.3 �ϤΥǥե���Ȥ� false

  var $cupid_self_shoot = 18; //���塼�ԥåɤ�¾�ͷ����ǽ�Ȥʤ����¼�Ϳ�
  var $cute_wolf_rate = 1; //˨ϵ��ȯưΨ (%)
  var $gentleman_rate = 13; //�»Ρ��ʽ���ȯưΨ (%)
  var $liar_rate = 95; //ϵ��ǯ��ȯưΨ (%)

  //ϵ��ǯ���Ѵ��ơ��֥�
  var $liar_replace_list = array('¼��' => '��ϵ', '��ϵ' => '¼��',
				 '���Ӥ�' => '��������', '��������' => '���Ӥ�',
				 '���ӥ�' => '��������', '��������' => '���ӥ�',
				 '����' => '��', '��' => '����',
				 '��' => '��', '��' => '��',
				 '��' => 'ϵ', 'ϵ' => '��',
				 '��' => '��', '��' => '��',
				 '��' => '��', '��' => '��',
				 'CO' => '����', '�ã�' => '����', '����' => 'CO',
				 '�ߤ�' => '����', '����' => '�ߤ�',
				 '������' => '���顼', '���顼'  => '������',
				 '��ǯ' => '����', '����' => '��ǯ',
				 '���礦�ͤ�' => '���礦����', '���礦����' => '���礦�ͤ�',
				 '���Ϥ褦' => '���䤹��', '���䤹��' => '���Ϥ褦'
				 );

  //�����º̤��Ѵ��ơ��֥�
  var $rainbow_replace_list = array('��' => '��', '��' => '��', '��' => '��', '��' => '��',
				    '��' => '��', '��' => '��', '��' => '��');

  //�����º̤��Ѵ��ơ��֥�
  var $weekly_replace_list = array('��' => '��', '��' => '��', '��' => '��', '��' => '��',
				   '��' => '��', '��' => '��', '��' => '��');

  //��Ԥ��Ѵ��ơ��֥�
  var $actor_replace_list = array('�Ǥ�' => '�ߤ��');

  var $invisible_rate = 10; //�����º̤�ȯ��������������ؤ���Ψ (%)
  var $silent_length  = 25; //̵����ȯ���Ǥ������ʸ����

  //-- �ְ۵ġפ��� --//
  var $objection = 5; //������
  var $objection_image = 'img/objection.gif'; //�ְ۵ġפ���ܥ���β����ѥ�

  //-- ��ư���� --//
  var $auto_reload = true; //game_view.php �Ǽ�ư������ͭ���ˤ��� / ���ʤ� (��������٤����)
  var $auto_reload_list = array(15, 30, 45, 60, 90, 120); //��ư�����⡼�ɤι����ֳ�(��)�Υꥹ��

  //-- ����¾ --//
  var $power_gm = false; //���� GM �⡼�� (ON��true / OFF��false)
  var $random_message = false; //�������å����������� (���롧true / ���ʤ���false)
}

//������λ�������
class TimeConfig{
  //���ס��������Ĥ���֥���Ǥ������ͤ�᤮�����ɼ���Ƥ��ʤ��ͤ������ष�ޤ�(��)
  var $sudden_death = 120; //180;

  //Ķ��Υޥ��ʥ����֤��������ͤ�ۤ������ϥ����Ф����Ū�˥����󤷤Ƥ�����Ƚ�ꤷ�ơ�
  //Ķ����֤�ꥻ�åȤ��ޤ� (��)
  var $server_disconnect = 90;

  //-- �ꥢ�륿������ --//
  var $default_day   = 5; //�ǥե���Ȥ�������»���(ʬ)
  var $default_night = 3; //�ǥե���Ȥ�������»���(ʬ)

  //-- ���ä��Ѥ������ۻ����� --//
  //������»���(���12���֡�spend_time=1(Ⱦ��100ʸ������) �� 12���� �� $day �ʤߤޤ�)
  var $day = 96;

  //������»���(��� 6���֡�spend_time=1(Ⱦ��100ʸ������) ��  6���� �� $night �ʤߤޤ�)
  var $night = 24;

  //��ꥢ�륿�������Ǥ������ͤ�᤮������ۤȤʤꡢ���ꤷ�����֤��ʤߤޤ�(��)
  var $silence = 60;

  //���۷в���� (12���� �� $day(��) or 6���� �� $night (��) �� $silence_pass �ܤλ��֤��ʤߤޤ�)
  var $silence_pass = 8;
}

//-- ¼�Υ��ץ������� --//
class RoomImage extends ImageManager{
  var $path      = 'room_option';
  var $extension = 'gif';
  var $class     = 'option';
  /*
  //¼�κ���Ϳ��ꥹ�� (RoomConfig->max_user_list ��Ϣư������)
  //���ߤ��Ի���
  var $max_user_list = array(
			      8 => 'img/room_option/max8.gif',   // 8��
			     16 => 'img/room_option/max16.gif',  //16��
			     22 => 'img/room_option/max22.gif'   //22��
			     );
  */
}

//-- �򿦤β��� --//
class RoleImage extends ImageManager{
  var $path      = 'role';
  var $extension = 'gif';
  var $class     = '';
}

//-- �����رĤβ��� --//
class VictoryImage extends VictoryImageBase{
  var $path      = 'victory_role';
  var $extension = 'gif';
  var $class     = 'winner';
}

//������ץ쥤���Υ�������ɽ������
class IconConfig{
  var $path   = 'user_icon'; //�桼����������Υѥ�
  var $dead   = 'grave.gif'; //���
  var $wolf   = 'wolf.gif';  //ϵ
  var $width  = 45; //ɽ��������(��)
  var $height = 45; //ɽ��������(�⤵)
  var $view   = 100; //����̤�ɽ�����륢������ο�
  var $page   = 10; //����̤�ɽ������ڡ������ο�

  function IconConfig(){ $this->__construct(); }
  function __construct(){
    $this->path = JINRO_ROOT . '/' . $this->path;
    $this->dead = JINRO_IMG  . '/' . $this->dead;
    $this->wolf = JINRO_IMG  . '/' . $this->wolf;
  }
}

//-- �������� --//
class Sound extends SoundBase{
  var $path      = 'swf'; //�����Υѥ�
  var $extension = 'swf'; //��ĥ��

  var $morning          = 'sound_morning';          //������
  var $revote           = 'sound_revote';           //����ɼ
  var $objection_male   = 'sound_objection_male';   //�۵Ĥ���(��)
  var $objection_female = 'sound_objection_female'; //�۵Ĥ���(��)
}

//����ɽ������
class OldLogConfig{
  var $view = 20; //����̤�ɽ������¼�ο�
  var $page =  5; //����̤�ɽ������ڡ������ο�
  var $reverse = true; //�ǥե���Ȥ�¼�ֹ��ɽ���� (true:�դˤ��� / false:���ʤ�)
}
