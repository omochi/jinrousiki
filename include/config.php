<?php
require_once(dirname(__FILE__) . '/message_class.php'); //�����ƥ��å�������Ǽ���饹
require_once(dirname(__FILE__) . '/system_class.php');  //�����ƥ�����Ǽ���饹

//�������ƥʥ󥹡���������
class RoomConfig{
  //�����Ǹ�β��ä�����¼�ˤʤ�ޤǤλ��� (��)
  //(���ޤ�û��������������ȶ��礹���ǽ������)
  var $die_room = 1200;
  #var $die_room = 12000; //�ǥХå��Ѥ�Ĺ�����Ƥ���

  //��λ���������Υ桼���Υ��å���� ID �ǡ����򥯥ꥢ����ޤǤλ��� (��)
  var $clear_session_id = 1200;

  //����Ϳ��Υꥹ�� (RoomImage->max_user_list ��Ϣư������ �� ���ߤ�Ϣư������)
  var $max_user_list = array(8, 16, 22, 32);
  var $default_max_user = 22; //�ǥե���Ȥκ���Ϳ� ($max_user_list �˴ޤळ��)

  //-- OutputCreateRoom() --//
  var $room_name = 45; //¼̾�κ���ʸ����
  var $room_comment = 50; //¼�������κ���ʸ����

  //�ƥ��ץ�����ͭ����[���� / ���ʤ�]���ǥե���Ȥǥ����å��� [�Ĥ��� / �Ĥ��ʤ�]
  var $wish_role = true; //����˾��
  var $default_wish_role = false;

  var $real_time = true; //�ꥢ�륿������ (�������� TimeConfig->default_day/night ����)
  var $default_real_time = true;

  var $dummy_boy = true; //��������Ͽ����귯
  var $default_dummy_boy = true;

  var $open_vote = true; //��ɼ����ɼ�����ɽ����
  var $default_open_vote = true;

  var $not_open_cast = true; //��������������ʤ�
  var $default_not_open_cast = false;

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

  var $full_mania = true; //���åޥ˥�¼
  var $default_full_mania = false;

  var $quiz = true; //������¼ //����Ĵ����
  //var $quiz = false; //������¼
  var $default_quiz = false;

  var $chaos = true; //����
  // var $default_chaos = false; //����̤�б�

  var $chaosfull = true; //��������

  var $chaos_open_cast = true; //����¼�Ǥ�����������ɽ������
  var $secret_sub_role = true; //�����򿦤��ܿͤ����Τ��ʤ�
  var $no_sub_role = true; //�����򿦤�Ĥ��ʤ�

  //ɽ������¾�Υ����ФΥꥹ��
  var $shared_server_list = array(
	'satori' => array('name' => '���Ȥ껪',
			  'url' => 'http://satori.crz.jp/'),

	'sakuya' => array('name' => '���뻪',
			  'url' => 'http://www7.atpages.jp/izayoi398/',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),
	/*
	'cirno' => array('name' => '����λ�',
			 'url' => 'http://www12.atpages.jp/cirno/',
			 'separator' => '<!-- atpages banner tag -->',
			 'footer' => '</a><br>'),
	*/
	'nico' => array('name' => '�˥�����',
			'url' => 'http://jinro.ebb.jp/'),
	/*
	'nico_test' => array('name' => '�˥����ƥ��Ȼ�',
			     'url' => 'http://jinro.s369.xrea.com/'),
	*/
	'sasuga' => array('name' => 'ή�з��ﻪ',
			  'url' => 'http://www12.atpages.jp/yaruo/jinro/',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</div></small></a><br>'),

	'bara' => array('name' => '�鯻��廪',
			'url' => 'http://www13.atpages.jp/yaranai/',
			'encode' => 'UTF-8',
			'separator' => '<!-- atpages banner tag -->',
			'footer' => '</a><br>'),

	'suigin' => array('name' => '��仪',
			  'url' => 'http://www13.atpages.jp/suigintou/',
			  'encode' => 'UTF-8',
			  'separator' => '<!-- atpages banner tag -->',
			  'footer' => '</a><br>')
				  );

}

//����������
class GameConfig{
  //-- ������Ͽ --//
  //��¼���� (Ʊ��������Ʊ�� IP ��ʣ����Ͽ) (true�����Ĥ��ʤ� / false�����Ĥ���)
  #var $entry_one_ip_address = true;
  var $entry_one_ip_address = false; //�ǥХå���

  //�ȥ�å��б� (true���Ѵ����� / false�� "#" ���ޤޤ�Ƥ����饨�顼���֤�)
  // var $trip = true; //�ޤ���������Ƥ��ޤ���
  var $trip = false;

  //ȯ����֡פǳ��
  var $quote_words = false;

  //-- ��ɼ --//
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
     #4 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1),
     4 => array('human' =>  3, 'wolf' => 1),
     // 4 => array('wolf' => 1, 'mage' => 1, 'poison' => 1, 'cupid' => 1), //�ǡ�����Ϣ���ƥ�����
     5 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1, 'poison' => 1),
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

  var $invisible_rate = 15; //�����º̤�ȯ��������������ؤ���Ψ
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
  var $main_role_list = array('human'             => '¼��',
			      'wolf'              => '��ϵ',
			      'boss_wolf'         => '��ϵ',
			      'cursed_wolf'       => '��ϵ',
			      'cute_wolf'         => '˨ϵ',
			      'poison_wolf'       => '��ϵ',
			      'resist_wolf'       => '����ϵ',
			      'tongue_wolf'       => '���ϵ',
			      'mage'              => '�ꤤ��',
			      'soul_mage'         => '�����ꤤ��',
			      'dummy_mage'        => '̴����',
			      'necromancer'       => '��ǽ��',
			      'soul_necromancer'  => '������',
			      'dummy_necromancer' => '̴���',
			      'medium'            => '���',
			      'mad'               => '����',
			      'fanatic_mad'       => '������',
			      'whisper_mad'       => '�񤭶���',
			      'guard'             => '���',
			      'poison_guard'      => '����',
			      'dummy_guard'       => '̴���',
			      'reporter'          => '�֥�',
			      'common'            => '��ͭ��',
			      'dummy_common'      => '̴��ͭ��',
			      'fox'               => '�Ÿ�',
			      'child_fox'         => '�Ҹ�',
			      'cursed_fox'        => 'ŷ��',
			      'poison_fox'        => '�ɸ�',
			      'white_fox'         => '���',
			      'poison'            => '���Ǽ�',
			      'strong_poison'     => '���Ǽ�',
			      'incubate_poison'   => '���Ǽ�',
			      'dummy_poison'      => '̴�Ǽ�',
			      'poison_cat'        => 'ǭ��',
			      'pharmacist'        => '����',
			      'suspect'           => '�Կ���',
			      'unconscious'       => '̵�ռ�',
			      'cupid'             => '���塼�ԥå�',
			      'mania'             => '���åޥ˥�',
			      'quiz'              => 'GM');

  //�����򿦤Υꥹ�� (������̾ => ɽ��̾)
  //�����������Υꥹ�ȤϤ��ν��֤�ɽ�������
  var $sub_role_list = array('authority'     => '���ϼ�',
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
			     'random_voice'  => '���¼�',
			     'no_last_words' => 'ɮ����',
			     'blinder'       => '�ܱ���',
			     'earplug'       => '����',
			     'speaker'       => '���ԡ�����',
			     'silent'        => '̵��',
			     'liar'          => 'ϵ��ǯ',
			     'invisible'     => '�����º�',
			     'rainbow'       => '�����º�',
			     'gentleman'     => '�»�',
			     'lady'          => '�ʽ�',
			     'chicken'       => '������',
			     'rabbit'        => '������',
			     'perverseness'  => 'ŷ�ٵ�',
			     'flattery'      => '���ޤ���',
			     'impatience'    => 'û��',
			     'panelist'      => '������',
			     'lovers'        => '����',
			     'copied'        => '�����åޥ˥�');

  function GetRoleName($role, $short = false){
    if(! $short) return ($this->main_role_list[$role] || $this->sub_role_list[$role]);

    static $short_role_list = array('human'             => '¼',
				    'wolf'              => 'ϵ',
				    'boss_wolf'         => '��ϵ',
				    'cursed_wolf'       => '��ϵ',
				    'cute_wolf'         => '˨ϵ',
				    'poison_wolf'       => '��ϵ',
				    'resist_wolf'       => '��ϵ',
				    'tongue_wolf'       => '��ϵ',
				    'mage'              => '��',
				    'soul_mage'         => '��',
				    'dummy_mage'        => '̴��',
				    'reporter'          => 'ʹ',
				    'necromancer'       => '��',
				    'soul_necromancer'  => '��',
				    'dummy_necromancer' => '̴��',
				    'medium'            => '��',
				    'mad'               => '��',
				    'fanatic_mad'       => '����',
				    'whisper_mad'       => '��',
				    'guard'             => '��',
				    'poison_guard'      => '��',
				    'dummy_guard'       => '̴��',
				    'common'            => '��',
				    'dummy_common'      => '̴��',
				    'fox'               => '��',
				    'child_fox'         => '�Ҹ�',
				    'cursed_fox'        => 'ŷ��',
				    'poison_fox'        => '�ɸ�',
				    'white_fox'         => '���',
				    'poison'            => '��',
				    'strong_poison'     => '����',
				    'dummy_poison'      => '̴��',
				    'poison_cat'        => 'ǭ',
				    'pharmacist'        => '��',
				    'suspect'           => '�Կ�',
				    'unconscious'       => '̵',
				    'cupid'             => 'QP',
				    'mania'             => '��',
				    'quiz'              => 'GM',
				    'authority'         => '��',
				    'random_voter'      => '��',
				    'rebel'             => 'ȿ',
				    'watcher'           => '˵',
				    'decide'            => '��',
				    'plague'            => '��',
				    'good_luck'         => '��',
				    'bad_luck'          => '�Ա�',
				    'upper_luck'        => '����',
				    'downer_luck'       => '��ȯ',
				    'random_luck'       => '����',
				    'star'              => '�͵�',
				    'disfavor'          => '�Կ�',
				    'strong_voice'      => '��',
				    'normal_voice'      => '��',
				    'weak_voice'        => '��',
				    'upper_voice'       => '����',
				    'downer_voice'      => 'ʤ',
				    'random_voice'      => '��',
				    'no_last_words'     => 'ɮ',
				    'blinder'           => '��',
				    'earplug'           => '��',
				    'speaker'           => '����',
				    'silent'            => '̵��',
				    'liar'              => '��',
				    'invisible'         => '�º�',
				    'rainbow'           => '��',
				    'gentleman'         => '��',
				    'lady'              => '��',
				    'chicken'           => '��',
				    'rabbit'            => '��',
				    'perverseness'      => '��',
				    'flattery'          => '����',
				    'impatience'        => 'û',
				    'panelist'          => '��',
				    'copied'            => '����',
				    'lovers'            => '��');
    return $short_role_list[$role];
  }
}

//������λ�������
class TimeConfig{
  //���ס��������Ĥ���֥���Ǥ������ͤ�᤮�����ɼ���Ƥ��ʤ��ͤ������ष�ޤ�(��)
  var $sudden_death = 180;
  #var $sudden_death = 30; //�ǥХå���

  //-- �ꥢ�륿������ --//
  var $default_day   = 5; //�ǥե���Ȥ�������»���(ʬ)
  var $default_night = 3; //�ǥե���Ȥ�������»���(ʬ)

  //-- ���ä��Ѥ������ۻ����� --//
  //������»���(���12���֡�spend_time=1(Ⱦ��100ʸ������) �� 12���� �� $day �ʤߤޤ�)
  var $day = 48;

  //������»���(��� 6���֡�spend_time=1(Ⱦ��100ʸ������) ��  6���� �� $night �ʤߤޤ�)
  var $night = 24;

  //��ꥢ�륿�������Ǥ������ͤ�᤮������ۤȤʤꡢ���ꤷ�����֤��ʤߤޤ�(��)
  var $silence = 60;

  //���۷в���� (12���� �� $day(��) or 6���� �� $night (��) �� $silence_pass �ܤλ��֤��ʤߤޤ�)
  var $silence_pass = 4;
}

//������ץ쥤���Υ�������ɽ������
class IconConfig{
  var $path   = './user_icon';   //�桼����������Υѥ�
  var $width  = 45;              //ɽ��������(��)
  var $height = 45;              //ɽ��������(�⤵)
  var $dead   = 'img/grave.jpg'; //���
  var $wolf   = 'img/wolf.gif';  //ϵ
}

//����������Ͽ����
class UserIcon{
  var $name   = 20;    //��������̾�ˤĤ�����ʸ����(Ⱦ��)
  var $size   = 15360; //���åץ��ɤǤ��륢������ե�����κ�������(ñ�̡��Х���)
  var $width  = 45;    //���åץ��ɤǤ��륢������κ�����
  var $height = 45;    //���åץ��ɤǤ��륢������κ���⤵
  var $number = 1000;  //��Ͽ�Ǥ��륢������κ����
}

//����ɽ������
class OldLogConfig{
  var $one_page = 20;   //����������1�ڡ����Ǥ����Ĥ�¼��ɽ�����뤫
  var $reverse  = true; //�ǥե���Ȥ�¼�ֹ��ɽ���� (true:�դˤ��� / false:���ʤ�)
}

//�ǡ�����Ǽ���饹�����
$ROOM_CONF   = new RoomConfig();   //�������ƥʥ�����
$GAME_CONF   = new GameConfig();   //����������
$TIME_CONF   = new TimeConfig();   //������λ�������
$ICON_CONF   = new IconConfig();   //�桼�������������
$ROOM_IMG    = new RoomImage();    //¼����β����ѥ�
$ROLE_IMG    = new RoleImage();    //�򿦤β����ѥ�
$VICTORY_IMG = new VictoryImage(); //�����رĤβ����ѥ�
$SOUND       = new Sound();        //���Ǥ��Τ餻��ǽ�Ѳ����ѥ�
$MESSAGE     = new Message();      //�����ƥ��å�����
?>
