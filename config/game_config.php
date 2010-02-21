<?php
//-- ¼���ƥʥ󥹡��������� --//
class RoomConfig{
  //¼��κǸ��ȯ��������¼�ˤʤ�ޤǤλ��� (��)
  //(���ޤ�û��������������ȶ��礹���ǽ������)
  #var $die_room = 1200;
  var $die_room = 12000; //�ƥ�����

  //��������ץ쥤��ǽ¼��
  var $max_active_room = 4;

  //����¼��Ω�Ƥ���ޤǤ��Ԥ����� (��)
  var $establish_wait = 120;

  //��λ����¼�Υ桼���Υ��å���� ID �ǡ����򥯥ꥢ����ޤǤλ��� (��)
  //���λ�����Ǥ���С������ڡ����˺���¼�Υ�󥯤��и����ޤ�
  var $clear_session_id = 86400; //24����

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

  var $open_vote = true; //��ɼ����ɼ�����ɽ����
  var $default_open_vote = false;

  var $dummy_boy = true; //��������Ͽ����귯
  var $default_dummy_boy = true;

  var $not_open_cast = true; //��������������ʤ�
  var $auto_open_cast = true; //��������ư�Ǹ�������

  //����ե⡼�ɤΥǥե���� [NULL:̵�� / 'auto':��ư���� / 'full': �������� ]
  #var $default_not_open_cast = NULL;
  var $default_not_open_cast = 'auto'; //�ƥ�����

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

//-- ���������� --//
class GameConfig{
  //-- ������Ͽ --//
  //��¼���� (Ʊ��������Ʊ�� IP ��ʣ����Ͽ) (true�����Ĥ��ʤ� / false�����Ĥ���)
  var $entry_one_ip_address = true;
  #var $entry_one_ip_address = false; //�ƥ�����

  //�ȥ�å��б� (true���Ѵ����� / false�� "#" ���ޤޤ�Ƥ����饨�顼���֤�)
  //var $trip = true; //�ޤ���������Ƥ��ޤ���
  var $trip = false;

  //ȯ����֡פǳ��
  var $quote_words = false;

  //-- ��ɼ --//
  var $self_kick = true; //��ʬ�ؤ� KICK (true��ͭ�� / false��̵��)
  var $kick = 3; //��ɼ�� KICK ������Ԥ���
  var $draw = 5; //����ɼ�����ܤǰ���ʬ���Ȥ��뤫

  //-- �򿦤�ǽ������ --//
  //��ǽ�ϼԤ��ߤä��ݤ˴������ޤ���о� (true:��ɼ�ԥ����� / false:����������)
  var $poison_only_voter = false; //1.3 �ϤΥǥե����

  //ϵ����ǽ�ϼԤ������ݤ˴������ޤ���о� (true:��ɼ�Ը��� / false:������)
  #var $poison_only_eater = false; //1.3 �ϤΥǥե����
  var $poison_only_eater = true;

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
    'crisis_priest'      => '�¸���',
    'revive_priest'      => 'ŷ��',
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
    'revive_cat'         => '��ì',
    'pharmacist'         => '����',
    'assassin'           => '�Ż���',
    'mind_scanner'       => '���Ȥ�',
    'evoke_scanner'      => '������',
    'jealousy'           => '��ɱ',
    'suspect'            => '�Կ���',
    'unconscious'        => '̵�ռ�',
    'elder'              => 'ĹϷ',
    'wolf'               => '��ϵ',
    'boss_wolf'          => '��ϵ',
    'tongue_wolf'        => '���ϵ',
    'wise_wolf'          => '��ϵ',
    'poison_wolf'        => '��ϵ',
    'resist_wolf'        => '����ϵ',
    'cursed_wolf'        => '��ϵ',
    'possessed_wolf'     => '��ϵ',
    'cute_wolf'          => '˨ϵ',
    'scarlet_wolf'       => '��ϵ',
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
    'black_fox'          => '����',
    'poison_fox'         => '�ɸ�',
    'voodoo_fox'         => '����',
    'revive_fox'         => '���',
    'cursed_fox'         => 'ŷ��',
    'cute_fox'           => '˨��',
    'scarlet_fox'        => '�ȸ�',
    'silver_fox'         => '���',
    'child_fox'          => '�Ҹ�',
    'cupid'              => '���塼�ԥå�',
    'self_cupid'         => '�ᰦ��',
    'mind_cupid'         => '����',
    //'possessed_cupid'    => 'QP',
    'quiz'               => '�����',
    'chiroptera'         => '����',
    'poison_chiroptera'  => '������',
    'cursed_chiroptera'  => '������',
    'dummy_chiroptera'   => '̴�ᰦ��',
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
    'mind_evoke'    => '����',
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
    'crisis_priest'      => '��',
    'revive_priest'      => 'ŷ��',
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
    'revive_cat'         => '��ì',
    'pharmacist'         => '��',
    'assassin'           => '��',
    'mind_scanner'       => '��',
    'evoke_scanner'      => '��',
    'jealousy'           => '��',
    'suspect'            => '�Կ�',
    'unconscious'        => '̵',
    'elder'              => 'Ϸ',
    'wolf'               => 'ϵ',
    'boss_wolf'          => '��ϵ',
    'tongue_wolf'        => '��ϵ',
    'wise_wolf'          => '��ϵ',
    'poison_wolf'        => '��ϵ',
    'resist_wolf'        => '��ϵ',
    'cursed_wolf'        => '��ϵ',
    'possessed_wolf'     => '��ϵ',
    'cute_wolf'          => '˨ϵ',
    'scarlet_wolf'       => '��ϵ',
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
    'black_fox'          => '����',
    'poison_fox'         => '�ɸ�',
    'voodoo_fox'         => '����',
    'revive_fox'         => '���',
    'cursed_fox'         => 'ŷ��',
    'cute_fox'           => '˨��',
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
    'dummy_chiroptera'   => '̴��',
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
    'mind_evoke'         => '����',
    'mind_open'          => '��',
    'mind_receiver'      => '��',
    'mind_friend'        => '��',
    'lovers'             => '��',
    'copied'             => '����');

  //�ᥤ���򿦤Υ��롼�ץꥹ�� (�� => ��°���롼��)
  // ���Υꥹ�Ȥ��¤ӽ�� strpos ��Ƚ�̤��� (�ǷϤʤɡ����ְ�¸���򿦤�����Τ����)
  var $main_role_group_list = array(
    'wolf' => 'wolf',
    'mad' => 'mad',
    'fox' => 'fox',
    'cupid' => 'cupid',
    'quiz' => 'quiz',
    'chiroptera' => 'chiroptera',
    'mage' => 'mage', 'voodoo_killer' => 'mage',
    'necromancer' => 'necromancer', 'medium' => 'necromancer',
    'priest' => 'priest',
    'guard' => 'guard', 'anti_voodoo' => 'guard', 'reporter' => 'guard',
    'common' => 'common',
    'cat' => 'poison_cat',
    'poison' => 'poison',
    'pharmacist' => 'pharmacist',
    'assassin' => 'assassin',
    'scanner' => 'mind_scanner',
    'jealousy' => 'jealousy',
    'mania' => 'mania');

  //�����򿦤Υ��롼�ץꥹ�� (CSS �Υ��饹̾ => ��°��)
  var $sub_role_group_list = array(
    'lovers'       => array('lovers'),
    'mind'         => array('mind_read', 'mind_open', 'mind_receiver', 'mind_friend', 'mind_evoke'),
    'mania'        => array('copied'),
    'sudden-death' => array('chicken', 'rabbit', 'perverseness', 'flattery', 'impatience',
			    'celibacy', 'panelist'),
    'convert'      => array('liar', 'invisible', 'rainbow', 'weekly', 'grassy', 'side_reverse',
			    'line_reverse', 'gentleman', 'lady'),
    'authority'    => array('authority', 'random_voter', 'rebel', 'watcher'),
    'decide'       => array('decide', 'plague', 'good_luck', 'bad_luck'),
    'luck'         => array('upper_luck', 'downer_luck', 'random_luck', 'star', 'disfavor'),
    'voice'        => array('strong_voice', 'normal_voice', 'weak_voice', 'upper_voice',
			    'downer_voice', 'inside_voice', 'outside_voice', 'random_voice'),
    'seal'         => array('no_last_words', 'blinder', 'earplug', 'speaker', 'silent', 'mower'));

  //-- ����¾ --//
  var $power_gm = false; //���� GM �⡼�� (ON��true / OFF��false)
  var $random_message = false; //�������å����������� (���롧true / ���ʤ���false)

  //-- �ؿ� --//
  function GetRoleName($role, $short = false){
    if($short) return $this->short_role_list[$role];
    return ($this->main_role_list[$role] || $this->sub_role_list[$role]);
  }
}

//������λ�������
class TimeConfig{
  //���ס��������Ĥ���֥���Ǥ������ͤ�᤮�����ɼ���Ƥ��ʤ��ͤ������ष�ޤ�(��)
  var $sudden_death = 120;

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

//-- �������� --//
class CastConfig{
  //-- ����ơ��֥� --//
  /* ����θ���
    [�����໲�ÿͿ�] => array([����̾1] => [����̾1�οͿ�], [����̾2] => [����̾2�οͿ�], ...),
    �����໲�ÿͿ�������̾�οͿ��ι�פ����ʤ����ϥ����೫����ɼ���˥��顼���֤�
  */
  var $role_list = array(
     4 => array('human' =>  1, 'wolf' => 1, 'mage' => 1, 'mad' => 1),
     4 => array('human' =>  3, 'wolf' => 1),
     5 => array('wolf' =>   1, 'mage' => 2, 'mad' => 2),
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
  //-- �򿦽и��Ϳ� --//
  //���򿦤νи���ɬ�פʿͿ������ꤹ��
  var $poison         = 20; //���Ǽ� [¼��2 �� ���Ǽ�1����ϵ1]
  var $assassin       = 22; //�Ż��� [¼��2 �� �Ż���1����ϵ1]
  var $boss_wolf      = 18; //��ϵ [��ϵ1 �� ��ϵ]
  var $poison_wolf    = 20; //��ϵ (+ ����) [��ϵ1 �� ��ϵ1��¼��1 �� ����1]
  var $possessed_wolf = 17; //��ϵ [��ϵ1 �� ��ϵ1]
  var $cupid          = 16; //���塼�ԥå� (14�ͤ����ϸ��ߥϡ��ɥ�����) [¼��1 �� ���塼�ԥå�1]
  var $medium         = 20; //��� (+ ����) [¼��2 �� ���1������1]
  var $mania          = 16; //���åޥ˥� [¼��1 �� ���åޥ˥�1]
  var $decide         = 16; //����� [��Ǥ]
  var $authority      = 16; //���ϼ� [��Ǥ]

  //��˾�����򿦴�˾���̤��Ψ (%) (�����귯��������� 100% �ˤ��Ƥ��ݾڤ���ޤ���)
  var $wish_role_rate = 100;

  //�����귯���ʤ�ʤ��򿦥��롼�פΥꥹ��
  var $disable_dummy_boy_role_list = array('wolf', 'fox', 'poison');

  //-- ����������������� --//
  //��������
  var $chaos_fix_role_list = array('wolf' => 1, 'mage' => 1);

  var $min_wolf_rate = 10; //��ϵ�κ���и��� (��͸�/N)
  var $min_fox_rate  = 15; //�ŸѤκ���и��� (��͸�/N)

  //�򿦥��롼�פκ���и�Ψ (���롼�� => ����͸���)
  var $chaos_role_group_rate_list = array(
    'wolf' => 0.21, 'mad' => 0.15, 'fox' => 0.12, 'cupid' => 0.1, 'chiroptera' => 0.15,
    'mage' => 0.18, 'necromancer' => 0.15, 'priest' => 0.1, 'guard' => 0.15,
    'common' => 0.18, 'poison' => 0.15, 'cat' => 0.1, 'pharmacist' => 0.15,
    'assassin' => 0.15, 'scanner' => 0.15, 'jealousy' => 0.1);

  //¼�ͤνи��������
  var $max_human_rate = 0.1; //¼�ͤκ���͸��� (1.0 = 100%)
  var $chaos_replace_human_role = 'mania'; //¼�ͤ��鿶���֤���
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
  var $extension = 'jpg';
  var $class     = 'winner';
}

//������ץ쥤���Υ�������ɽ������
class IconConfig{
  var $width  = 45; //ɽ��������(��)
  var $height = 45; //ɽ��������(�⤵)
  var $path   = 'user_icon'; //�桼����������Υѥ�
  var $dead   = 'img/grave.gif'; //���
  var $wolf   = 'img/wolf.gif';  //ϵ

  function IconConfig(){ $this->__construct(); }
  function __construct(){
    $this->path = JINRO_ROOT . '/' . $this->path;
    $this->dead = JINRO_ROOT . '/' . $this->dead;
    $this->wolf = JINRO_ROOT . '/' . $this->wolf;
  }
}

//����������Ͽ����
class UserIcon{
  var $disable_upload = false; //true; //��������Υ��åץ��ɤ�������� (true:��ߤ��� / false:���ʤ�)
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
  var $room = 20;  //����̤�ɽ������¼�ο�
  var $page = 5; //����̤�ɽ������ڡ������ο�
  var $reverse  = true; //�ǥե���Ȥ�¼�ֹ��ɽ���� (true:�դˤ��� / false:���ʤ�)
}
