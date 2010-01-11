<?php
//�������ƥʥ󥹡���������
class RoomConfig{
  //�����Ǹ�β��ä�����¼�ˤʤ�ޤǤλ��� (��)
  //(���ޤ�û��������������ȶ��礹���ǽ������)
  #var $die_room = 1200;
  var $die_room = 12000; //�ǥХå��Ѥ�Ĺ�����Ƥ���

  //��������ץ쥤��ǽ¼��
  var $max_active_room = 4;

  //��λ���������Υ桼���Υ��å���� ID �ǡ����򥯥ꥢ����ޤǤλ��� (��)
  var $clear_session_id = 1200;

  //����Ϳ��Υꥹ�� (RoomImage->max_user_list ��Ϣư������ �� ���ߤ�����)
  var $max_user_list = array(8, 16, 22, 32);
  var $default_max_user = 22; //�ǥե���Ȥκ���Ϳ� ($max_user_list �ˤ����ͤ�����뤳��)

  //-- OutputCreateRoom() --//
  var $room_name = 45; //¼̾�κ���ʸ����
  var $room_comment = 50; //¼�������κ���ʸ����

  //�ƥ��ץ�����ͭ���� [true:���� / false:���ʤ�]
  //�ǥե���Ȥǥ����å��� [true:�Ĥ��� / false:�Ĥ��ʤ�]
  var $wish_role = true; //����˾��
  var $default_wish_role = false;

  var $real_time = true; //�ꥢ�륿������ (�������� TimeConfig->default_day/night ����)
  var $default_real_time = true;

  var $dummy_boy = true; //��������Ͽ����귯
  var $default_dummy_boy = true;

  var $open_vote = true; //��ɼ����ɼ�����ɽ����
  var $default_open_vote = false;

  var $not_open_cast = true; //��������������ʤ�
  var $default_not_open_cast = true;

  var $decide = true; //����Խи� (ɬ�׿Ϳ��� GameConfig->decide ����)
  var $default_decide = true;

  var $authority = true; //���ϼԽи� (ɬ�׿Ϳ��� GameConfig->authority ����)
  var $default_authority = true;

  var $poison = true; //���ǼԽи� (ɬ�׿Ϳ��� GameConfig->poison ����)
  var $default_poison = true;

  var $cupid = true; //���塼�ԥåɽи� (ɬ�׿Ϳ��� GameConfig->cupid ����)
  var $default_cupid = false;

  var $boss_wolf = true; //��ϵ�и� (ɬ�׿Ϳ��� GameConfig->boss_wolf ����)
  var $default_boss_wolf = false;

  var $poison_wolf = true; //��ϵ�и� (ɬ�׿Ϳ��� GameConfig->poison_wolf ����)
  var $default_poison_wolf = false;

  var $mania = true; //���åޥ˥��и� (ɬ�׿Ϳ��� GameConfig->mania ����)
  var $default_mania = false;

  var $medium = true; //����и� (ɬ�׿Ϳ��� GameConfig->medium ����)
  var $default_medium = false;

  var $liar = true; //ϵ��ǯ¼
  var $default_liar = false;

  var $gentleman = true; //�»Ρ��ʽ�¼
  var $default_gentleman = false;

  var $sudden_death = true; //�����μ�¼
  var $default_sudden_death = false;

  var $perverseness = true; //ŷ�ٵ�¼
  var $default_perverseness = false;

  var $full_mania = true; //���åޥ˥�¼
  var $default_full_mania = false;

  var $chaos = true; //����⡼��
  var $chaosfull = true; //��������⡼��

  //����⡼�ɤΥǥե���� [NULL:�̾��ϵ / 'chaos':�̾���� / 'chaosfull':��������]
  var $default_chaos = NULL; //�̾��ϵ

  var $chaos_open_cast = true; //����������ɽ������ (����⡼�����ѥ��ץ����)
  var $chaos_open_cast_camp = true; //�ر���������ɽ������ (����⡼�����ѥ��ץ����)
  var $chaos_open_cast_role = true; //�򿦤μ�����������ɽ������ (����⡼�����ѥ��ץ����)

  //���Υ⡼�ɤΥǥե���� [NULL:̵�� / 'camp':�ر� / 'role':�� / 'full':����]
  var $default_chaos_open_cast = 'camp'; //�ر�����

  var $secret_sub_role = true; //�����򿦤��ܿͤ����Τ��ʤ� (����⡼�����ѥ��ץ����)
  var $default_secret_sub_role = false;

  var $no_sub_role = true; //�����򿦤�Ĥ��ʤ� (����⡼�����ѥ��ץ����)
  var $default_no_sub_role = true;

  var $quiz = true; //������¼
  var $default_quiz = false;

  var $duel = true; //��Ʈ¼
  var $default_duel = false;
}

//����������
class GameConfig{
  //-- ������Ͽ --//
  //��¼���� (Ʊ��������Ʊ�� IP ��ʣ����Ͽ) (true�����Ĥ��ʤ� / false�����Ĥ���)
  var $entry_one_ip_address = true;

  //�ȥ�å��б� (true���Ѵ����� / false�� "#" ���ޤޤ�Ƥ����饨�顼���֤�)
  // var $trip = true; //�ޤ���������Ƥ��ޤ���
  var $trip = false;

  //ȯ����֡פǳ��
  var $quote_words = false;

  //-- ��ɼ --//
  var $self_kick = false; //��ʬ�ؤ� KICK (true��ͭ�� / false��̵��)
  var $kick = 3; //��ɼ�� KICK ������Ԥ���
  var $draw = 5; //����ɼ�����ܤǰ���ʬ���Ȥ��뤫

  //-- �� --//
  //��˾�����򿦴�˾���̤��Ψ (%) (�����귯��������� 100% �ˤ��Ƥ��ݾڤ���ޤ���)
  var $wish_role_rate = 100;

  //����ơ��֥�
  /* ����θ���
    [�����໲�ÿͿ�] => array([����̾1] => [����̾1�οͿ�], [����̾2] => [����̾2�οͿ�], ...),
    �����໲�ÿͿ�������̾�οͿ��ι�פ����ʤ����ϥ����೫����ɼ���˥��顼���֤�
  */
  var $role_list = array(
     4 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1),
     // 4 => array('wolf' => 1, 'mage' => 1, 'poison' => 1, 'cupid' => 1), //�ǡ�����Ϣ���ƥ�����
     5 => array('wolf' => 1, 'mage' => 2, 'mad' => 2),
     6 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'poison' => 1, 'fox' => 1, 'cupid' => 1),
     7 => array('human' =>  3, 'wolf' => 1, 'mage' => 1, 'guard' => 1, 'fox' => 1),
     8 => array('human' =>  5, 'wolf' => 2, 'mage' => 1),
     9 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1),
    10 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1),
    11 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1),
    12 => array('human' =>  6, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1),
    13 => array('human' =>  5, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common'=> 2),
    14 => array('human' =>  6, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2),
    15 => array('human' =>  6, 'wolf' => 2, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    16 => array('human' =>  6, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    17 => array('human' =>  7, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    18 => array('human' =>  8, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    19 => array('human' =>  9, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    20 => array('human' => 10, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    21 => array('human' => 11, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    22 => array('human' => 12, 'wolf' => 3, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    23 => array('human' => 12, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    24 => array('human' => 13, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    25 => array('human' => 14, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    26 => array('human' => 15, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 1),
    27 => array('human' => 15, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 1, 'guard' => 1, 'common' => 2, 'fox' => 2),
    28 => array('human' => 14, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    29 => array('human' => 15, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    30 => array('human' => 16, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    31 => array('human' => 17, 'wolf' => 4, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 1, 'common' => 3, 'fox' => 2),
    32 => array('human' => 16, 'wolf' => 5, 'mage' => 1, 'necromancer' => 1, 'mad' => 2, 'guard' => 2, 'common' => 3, 'fox' => 2)
                         );

  var $decide      = 16;  //����Խи���ɬ�פʿͿ�
  var $authority   = 16;  //���ϼԽи���ɬ�פʿͿ�
  var $poison      = 20;  //���ǼԽи���ɬ�פʿͿ�
  var $boss_wolf   = 18;  //��ϵ�и���ɬ�פʿͿ�
  var $poison_wolf = 20;  //��ϵ�и���ɬ�פʿͿ�
  var $mania       = 16;  //���åޥ˥��и���ɬ�פʿͿ�
  var $medium      = 20;  //����и���ɬ�פʿͿ�

  //���ǼԤ��ߤä��ݤ˴������ޤ���о� (true:��ɼ�ԥ����� / false:����������)
  var $poison_only_voter = false; //1.3 �ϤΥǥե����
  // var $poison_only_voter = true;

  //ϵ�����ǼԤ������ݤ˴������ޤ���о� (true:��ɼ�Ը��� / false:������)
  var $poison_only_eater = true;

  var $cupid = 16; //���塼�ԥåɽи���ɬ�פʿͿ� (14�ͤ����ϸ��ߥϡ��ɥ�����)
  var $cupid_self_shoot = 18; //���塼�ԥåɤ�¾���Ǥ���ǽ�Ȥʤ����¼�Ϳ�

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

  var $invisible_rate = 10; //�����º̤�ȯ��������������ؤ���Ψ (%)
  var $silent_length  = 25; //̵����ȯ���Ǥ������ʸ����

  //-- �ְ۵ġפ��� --//
  var $objection = 5; //������
  var $objection_image = 'img/objection.gif'; //�ְ۵ġפ���ܥ���β����ѥ�

  //-- ��ư���� --//
  var $auto_reload = true; //game_view.php �Ǽ�ư������ͭ���ˤ��� / ���ʤ� (��������٤����)
  var $auto_reload_list = array(15, 30, 45, 60, 90, 120); //��ư�����⡼�ɤι����ֳ�(��)�Υꥹ��

  //-- ��̾������ --//
  //�ᥤ���򿦤Υꥹ�� (������̾ => ɽ��̾)
  //�����������Υꥹ�ȤϤ��ν��֤�ɽ�������
  var $main_role_list = array(
    'human'              => '¼��',
    'mage'               => '�ꤤ��',
    'soul_mage'          => '�����ꤤ��',
    'psycho_mage'        => '���������',
    'sex_mage'           => '�Ҥ褳�����',
    'voodoo_killer'      => '���ۻ�',
    'dummy_mage'         => '̴����',
    'necromancer'        => '��ǽ��',
    'soul_necromancer'   => '������',
    'yama_necromancer'   => '����',
    'dummy_necromancer'  => '̴���',
    'medium'             => '���',
    'priest'             => '�ʺ�',
    'guard'              => '���',
    'poison_guard'       => '����',
    'reporter'           => '�֥�',
    'anti_voodoo'        => '���',
    'dummy_guard'        => '̴���',
    'common'             => '��ͭ��',
    'dummy_common'       => '̴��ͭ��',
    'poison'             => '���Ǽ�',
    'strong_poison'      => '���Ǽ�',
    'incubate_poison'    => '���Ǽ�',
    'dummy_poison'       => '̴�Ǽ�',
    'poison_cat'         => 'ǭ��',
    'pharmacist'         => '����',
    'assassin'           => '�Ż���',
    'mind_scanner'       => '���Ȥ�',
    'jealousy'           => '��ɱ',
    'suspect'            => '�Կ���',
    'unconscious'        => '̵�ռ�',
    'wolf'               => '��ϵ',
    'boss_wolf'          => '��ϵ',
    'tongue_wolf'        => '���ϵ',
    'wise_wolf'          => '��ϵ',
    'poison_wolf'        => '��ϵ',
    'resist_wolf'        => '����ϵ',
    'cursed_wolf'        => '��ϵ',
    'cute_wolf'          => '˨ϵ',
    'silver_wolf'        => '��ϵ',
    'mad'                => '����',
    'fanatic_mad'        => '������',
    'whisper_mad'        => '�񤭶���',
    'jammer_mad'         => '����',
    'voodoo_mad'         => '���ѻ�',
    'corpse_courier_mad' => '�м�',
    'dream_eater_mad'    => '��',
    'trap_mad'           => '櫻�',
    'fox'                => '�Ÿ�',
    'white_fox'          => '���',
    'poison_fox'         => '�ɸ�',
    'voodoo_fox'         => '����',
    'cursed_fox'         => 'ŷ��',
    'scarlet_fox'        => '�ȸ�',
    'silver_fox'         => '���',
    'child_fox'          => '�Ҹ�',
    'cupid'              => '���塼�ԥå�',
    'self_cupid'         => '�ᰦ��',
    'mind_cupid'         => '����',
    'quiz'               => '�����',
    'chiroptera'         => '����',
    'poison_chiroptera'  => '������',
    'cursed_chiroptera'  => '������',
    'mania'              => '���åޥ˥�',
    'unknown_mania'      => '�');

  //�����򿦤Υꥹ�� (������̾ => ɽ��̾)
  //�����������Υꥹ�ȤϤ��ν��֤�ɽ�������
  var $sub_role_list = array(
    'chicken'       => '������',
    'rabbit'        => '������',
    'perverseness'  => 'ŷ�ٵ�',
    'flattery'      => '���ޤ���',
    'celibacy'      => '�ȿȵ�²',
    'impatience'    => 'û��',
    'panelist'      => '������',
    'liar'          => 'ϵ��ǯ',
    'invisible'     => '�����º�',
    'rainbow'       => '�����º�',
    'weekly'        => '�����º�',
    //'monochrome'    => '����º�',
    'grassy'        => '���º�',
    'side_reverse'  => '�����º�',
    'line_reverse'  => 'ŷ���º�',
    'gentleman'     => '�»�',
    'lady'          => '�ʽ�',
    'authority'     => '���ϼ�',
    'random_voter'  => '��ʬ��',
    'rebel'         => 'ȿ�ռ�',
    'watcher'       => '˵�Ѽ�',
    'decide'        => '�����',
    'plague'        => '���¿�',
    'good_luck'     => '����',
    'bad_luck'      => '�Ա�',
    'upper_luck'    => '����',
    'downer_luck'   => '��ȯ��',
    'random_luck'   => '��������',
    'star'          => '�͵���',
    'disfavor'      => '�Կ͵�',
    'strong_voice'  => '����',
    'normal_voice'  => '�Դ���',
    'weak_voice'    => '����',
    'upper_voice'   => '�ᥬ�ۥ�',
    'downer_voice'  => '�ޥ���',
    'inside_voice'  => '���۷�',
    'outside_voice' => '���۷�',
    'random_voice'  => '���¼�',
    'no_last_words' => 'ɮ����',
    'blinder'       => '�ܱ���',
    'earplug'       => '����',
    'speaker'       => '���ԡ�����',
    'silent'        => '̵��',
    'mower'         => '�𴢤�',
    'mind_read'     => '���ȥ��',
    'mind_open'     => '������',
    'mind_receiver' => '������',
    'mind_friend'   => '���ļ�',
    'lovers'        => '����',
    'copied'        => '�����åޥ˥�');

  //�򿦤ξ�ά̾ (������)
  var $short_role_list = array(
    'human'              => '¼',
    'mage'               => '��',
    'soul_mage'          => '��',
    'psycho_mage'        => '����',
    'sex_mage'           => '����',
    'voodoo_killer'      => '����',
    'dummy_mage'         => '̴��',
    'necromancer'        => '��',
    'soul_necromancer'   => '��',
    'yama_necromancer'   => '��',
    'dummy_necromancer'  => '̴��',
    'medium'             => '��',
    'priest'             => '��',
    'guard'              => '��',
    'poison_guard'       => '��',
    'reporter'           => 'ʹ',
    'anti_voodoo'        => '��',
    'dummy_guard'        => '̴��',
    'common'             => '��',
    'dummy_common'       => '̴��',
    'poison'             => '��',
    'strong_poison'      => '����',
    'incubate_poison'    => '����',
    'dummy_poison'       => '̴��',
    'poison_cat'         => 'ǭ',
    'pharmacist'         => '��',
    'assassin'           => '��',
    'mind_scanner'       => '��',
    'jealousy'           => '��',
    'suspect'            => '�Կ�',
    'unconscious'        => '̵',
    'wolf'               => 'ϵ',
    'boss_wolf'          => '��ϵ',
    'tongue_wolf'        => '��ϵ',
    'wise_wolf'          => '��ϵ',
    'poison_wolf'        => '��ϵ',
    'resist_wolf'        => '��ϵ',
    'cursed_wolf'        => '��ϵ',
    'cute_wolf'          => '˨ϵ',
    'silver_wolf'        => '��ϵ',
    'mad'                => '��',
    'fanatic_mad'        => '����',
    'whisper_mad'        => '��',
    'jammer_mad'         => '����',
    'voodoo_mad'         => '����',
    'corpse_courier_mad' => '�м�',
    'dream_eater_mad'    => '��',
    'trap_mad'           => '�',
    'fox'                => '��',
    'white_fox'          => '���',
    'poison_fox'         => '�ɸ�',
    'voodoo_fox'         => '����',
    'cursed_fox'         => 'ŷ��',
    'scarlet_fox'        => '�ȸ�',
    'silver_fox'         => '���',
    'child_fox'          => '�Ҹ�',
    'cupid'              => 'QP',
    'self_cupid'         => '�ᰦ',
    'mind_cupid'         => '����',
    'quiz'               => 'GM',
    'chiroptera'         => '��',
    'poison_chiroptera'  => '����',
    'cursed_chiroptera'  => '����',
    'mania'              => '��',
    'unknown_mania'      => '�',
    'chicken'            => '��',
    'rabbit'             => '��',
    'perverseness'       => '��',
    'flattery'           => '����',
    'celibacy'           => '��',
    'impatience'         => 'û',
    'panelist'           => '��',
    'liar'               => '��',
    'invisible'          => '����',
    'rainbow'            => '����',
    'weekly'             => '����',
    'grassy'             => '����',
    'side_reverse'       => '����',
    'line_reverse'       => 'ŷ��',
    'gentleman'          => '��',
    'lady'               => '��',
    'authority'          => '��',
    'random_voter'       => '��',
    'rebel'              => 'ȿ',
    'watcher'            => '˵',
    'decide'             => '��',
    'plague'             => '��',
    'good_luck'          => '��',
    'bad_luck'           => '�Ա�',
    'upper_luck'         => '����',
    'downer_luck'        => '��ȯ',
    'random_luck'        => '����',
    'star'               => '�͵�',
    'disfavor'           => '�Կ�',
    'strong_voice'       => '��',
    'normal_voice'       => '��',
    'weak_voice'         => '��',
    'upper_voice'        => '����',
    'downer_voice'       => 'ʤ',
    'inside_voice'       => '����',
    'outside_voice'      => '����',
    'random_voice'       => '��',
    'no_last_words'      => 'ɮ',
    'blinder'            => '��',
    'earplug'            => '��',
    'speaker'            => '����',
    'silent'             => '̵��',
    'mower'              => '��',
    'mind_read'          => 'ϳ',
    'mind_open'          => '��',
    'mind_receiver'      => '��',
    'mind_friend'        => '��',
    'lovers'             => '��',
    'copied'             => '����');

  //�����򿦤Υ��롼�ץꥹ�� (CSS �Υ��饹̾ => ��°��)
  var $sub_role_group_list = array(
    'lovers'       => array('lovers'),
    'mind'         => array('mind_read', 'mind_open', 'mind_receiver', 'mind_friend'),
    'mania'        => array('copied'),
    'sudden-death' => array('chicken', 'rabbit', 'perverseness', 'flattery', 'impatience', 'celibacy'),
    'convert'      => array('liar', 'invisible', 'rainbow', 'weekly', 'grassy', 'side_reverse',
			    'line_reverse', 'gentleman', 'lady'),
    'authority'    => array('authority', 'random_voter', 'rebel', 'watcher'),
    'decide'       => array('decide', 'plague', 'good_luck', 'bad_luck'),
    'luck'         => array('upper_luck', 'downer_luck', 'random_luck', 'star', 'disfavor'),
    'voice'        => array('strong_voice', 'normal_voice', 'weak_voice', 'upper_voice',
			    'downer_voice', 'inside_voice', 'outside_voice', 'random_voice'),
    'seal'         => array('no_last_words', 'blinder', 'earplug', 'speaker', 'silent', 'mower'));

  //�����귯���ʤ�ʤ��򿦥��롼�פΥꥹ��
  var $disable_dummy_boy_role_list = array('wolf', 'fox', 'poison');

  //-- ����������������� --//
  //��������
  var $chaos_fix_role_list = array('wolf' => 1, 'mage' => 1);

  var $min_wolf_rate = 10; //��ϵ�κ���½и�Ψ (��͸�/N)
  var $min_fox_rate  = 15; //�ŸѤκ���½и�Ψ (��͸�/N)

  //-- ����¾ --//
  var $power_gm = false; //���� GM �⡼�� (ON��true / OFF��false)
  var $random_message = true; //�������å����������� (���롧true / ���ʤ���false)

  //-- �ؿ� --//
  function GetRoleName($role, $short = false){
    if($short) return $this->short_role_list[$role];
    return ($this->main_role_list[$role] || $this->sub_role_list[$role]);
  }
}

//������λ�������
class TimeConfig{
  //���ס��������Ĥ���֥���Ǥ������ͤ�᤮�����ɼ���Ƥ��ʤ��ͤ������ष�ޤ�(��)
  var $sudden_death = 180;

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

//������ץ쥤���Υ�������ɽ������
class IconConfig{
  var $path   = './user_icon';   //�桼����������Υѥ�
  var $width  = 45;              //ɽ��������(��)
  var $height = 45;              //ɽ��������(�⤵)
  var $dead   = 'img/grave.gif'; //���
  var $wolf   = 'img/wolf.gif';  //ϵ
}

//����������Ͽ����
class UserIcon{
  var $disable_upload = true; //��������Υ��åץ��ɤ�������� (true:��ߤ��� / false:���ʤ�)
  var $name   = 20;    //��������̾�ˤĤ�����ʸ����(Ⱦ��)
  var $size   = 15360; //���åץ��ɤǤ��륢������ե�����κ�������(ñ�̡��Х���)
  var $width  = 45;    //���åץ��ɤǤ��륢������κ�����
  var $height = 45;    //���åץ��ɤǤ��륢������κ���⤵
  var $number = 1000;  //��Ͽ�Ǥ��륢������κ����

  // ���������ʸ����
  function IconNameMaxLength(){
    return '��������̾��Ⱦ�Ѥ�' . $this->name . 'ʸ�������Ѥ�' . floor($this->name / 2) . 'ʸ���ޤ�';
  }

  // ��������Υե����륵����
  function IconFileSizeMax(){
    return ($this->size > 1024 ? floor($this->size / 1024) . 'k' : $this->size) . 'Byte �ޤ�';
  }

  // ��������νĲ��Υ�����
  function IconSizeMax(){
    return '��' . $this->width . '�ԥ����� �� �⤵' . $this->height . '�ԥ�����ޤ�';
  }
}

//����ɽ������
class OldLogConfig{
  var $one_page = 20;   //����������1�ڡ����Ǥ����Ĥ�¼��ɽ�����뤫
  var $reverse  = true; //�ǥե���Ȥ�¼�ֹ��ɽ���� (true:�դˤ��� / false:���ʤ�)
}
?>
