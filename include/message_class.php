<?php
// �����ƥ��å�������Ǽ���饹
class Message{
  //-- room_manger.php --//
  //CreateRoom() : ¼����
  //�����귯�Υ�����
  var $dummy_boy_comment = '�ͤϤ��������ʤ���';

  //�����귯�ΰ��
  var $dummy_boy_last_words = '�ͤϤ��������ʤ��äƸ��ä��Τˡġ�';

  var $game_option_wish_role     = '����˾��';
  var $game_option_real_time     = '�ꥢ�륿������';
  var $game_option_dummy_boy     = '��������Ͽ����귯';
  var $game_option_open_vote     = '��ɼ����ɼ�����ɽ����';
  var $game_option_not_open_cast = '��������������ʤ�';
  var $game_option_decide        = '������о�';
  var $game_option_authority     = '���ϼ��о�';
  var $game_option_poison        = '���Ǽ��о�';
  var $game_option_cupid         = '���塼�ԥå��о�';
  var $game_option_boss_wolf     = '��ϵ�о�';
  var $game_option_poison_wolf   = '��ϵ�о�';
  var $game_option_mania         = '���åޥ˥��о�';
  var $game_option_medium        = '����о�';
  var $game_option_liar          = 'ϵ��ǯ¼';
  var $game_option_sudden_death  = '�����μ�¼';
  var $game_option_gentleman     = '�»Ρ��ʽ�¼';
  var $game_option_quiz          = '������¼';
  var $game_option_chaos         = '����⡼��';
  var $game_option_chaosfull     = '��������⡼��';
  var $game_option_chaos_open_cast = '��������Τ���';
  var $game_option_secret_sub_role = '�����򿦤�ɽ�����ʤ�';
  var $game_option_no_sub_role     = '�����򿦤�Ĥ��ʤ�';

  //-- user_manager.php --//
  //EntryUser() : �桼����Ͽ
  //��¼��å�����
  // var $entry_user = '����¼�ν����ˤ�äƤ��ޤ���'
  var $entry_user = '�����۶����ꤷ�ޤ���';
  // var $entry_user = '��¼�λ��Ҳ��ˤ�äƤ��ޤ���';

  //-- game_view.php & OutputGameHTMLHeader() --//
  var $vote_announce = '���֤�����ޤ�����ɼ���Ƥ�������'; //���ä����»����ڤ�

  //-- game_functions.php --//
  //OutputVictory() : ¼���ܿͤξ��Է��
  //¼�;���
  var $victory_human = '[¼�;���] ¼�ͤ����Ͽ�ϵ�η���䤹�뤳�Ȥ��������ޤ���';

  //��ϵ�����;���
  var $victory_wolf = '[��ϵ�����;���] �Ǹ�ΰ�ͤ򿩤������ȿ�ϵã�ϼ��γ�ʪ�����¼���ˤ���';

  //�ŸѾ��� (¼�;�����)
  var $victory_fox1 = '[�ŸѾ���] ��ϵ�����ʤ��ʤä��������Ũ�ʤɤ⤦���ʤ�';

  //�ŸѾ��� (��ϵ������)
  var $victory_fox2 = '[�ŸѾ���] �ޥ̥��ʿ�ϵ�ɤ���٤����Ȥʤ��ưפ����Ȥ�';

  //���͡����塼�ԥåɾ���
  var $victory_lovers = '[���͡����塼�ԥåɾ���] �������ˤϲ��Ԥ�̵�Ϥ��ä��ΤǤ���';

  //������ GM ����
  var $victory_quiz = '[GM ����] ���β����ԤˤϤޤ��󤤡ġĽ��Ԥ���Τ�';

  //������ GM ��˴
  var $victory_quiz_dead = '[����ʬ��] ���Ȥ������������ΤޤޤǤϷ��夬�դ��ʤ�����';

  //����ʬ��
  var $victory_draw = '[����ʬ��] ����ʬ���Ȥʤ�ޤ���';

  //����
  var $victory_vanish = '[����ʬ��] ������ï���ʤ��ʤä��ġ�';

  //��¼
  var $victory_none = '���¤��ʹԤ��ƿͤ����ʤ��ʤ�ޤ���';

  var $win  = '���ʤ��Ͼ������ޤ���'; //�ܿ;���
  var $lose = '���ʤ������̤��ޤ���'; //�ܿ�����
  var $draw = '����ʬ���Ȥʤ�ޤ���'; //����ʬ��

  //OutputRevoteList() : ����ɼ���ʥ���
  var $revote = '����ɼ�Ȥʤ�ޤ���'; //��ɼ���
  var $draw_announce = '����ɼ�Ȥʤ�Ȱ���ʬ���ˤʤ�ޤ�'; //����ʬ������

  //OutputTalkLog() : ���á������ƥ��å���������
  var $objection = '���ְ۵ġפ򿽤�Ω�Ƥޤ���'; //�۵Ĥ���
  //var $game_start = '�ϥ����೫�Ϥ���ɼ���ޤ���' //�����೫����ɼ //���ߤ��Ի���
  var $kick_do        = '�� KICK ��ɼ���ޤ���';         //KICK ��ɼ
  var $vote_do        = '�˽跺��ɼ���ޤ���';           //�跺��ɼ
  var $wolf_eat       = '��������Ĥ��ޤ���';           //��ϵ����ɼ
  var $mage_do        = '���ꤤ�ޤ�';                   //�ꤤ�դ���ɼ
  var $guard_do       = '�θ�Ҥ��դ��ޤ���';           //��ͤ���ɼ
  var $reporter_do    = '�����Ԥ��ޤ���';               //�֥󲰤���ɼ
  var $cupid_do       = '�˰�����������ޤ���';         //���塼�ԥåɤ���ɼ
  var $mania_do       = '��ǽ�Ϥ򿿻��뤳�Ȥˤ��ޤ���'; //���åޥ˥�����ɼ
  var $poison_cat_do  = '���������֤򤷤ޤ���';         //ǭ������ɼ

  var $morning_header = 'ī��������'; //ī�Υإå���
  var $morning_footer = '���ܤ�ī����äƤ��ޤ���'; //ī�Υեå���
  var $night = '����������Ť��Ť����뤬��äƤ��ޤ���'; //��
  var $dummy_boy = '�����귯��'; //����GM�⡼���ѥإå���

  var $wolf_howl = '���������󡦡���'; //ϵ�α��ʤ�
  // var $common_talk = '�ҥ��ҥ�������'; //��ͭ�Ԥξ���
  var $common_talk = '����������������������������'; //��ͭ�Ԥξ���

  //OutputLastWords() : �����ɽ��
  var $lastwords = '�뤬���������������˴���ʤä����ΰ���񤬸��Ĥ���ޤ���';

  //OutoutDeadManType() : �����ɽ��
  // var $deadman           = '��̵�ĤʻѤ�ȯ������ޤ���'; //������ɽ��������å�����
  var $deadman           = '��̵�Ĥ��餱���λѤ�ȯ������ޤ���';
  var $wolf_killed       = '��ϵ�α¿��ˤʤä��褦�Ǥ�'; //ϵ�ν���
  var $fox_dead          = '(�Ÿ�) ���ꤤ�դ˼��������줿�褦�Ǥ�'; //�Ѽ���
  var $poison_dead       = '���Ǥ��������˴�����褦�Ǥ�'; //���ǼԤ�ƻϢ��
  // var $vote_killed      = '����ɼ�η�̽跺����ޤ���'; //�ߤ�
  var $vote_killed       = '�����뤴�ä� (��ɼ) �η�̤Ԥ��塼�� (�跺) ���ޤ���';
  var $lovers_followed   = '�����ͤθ���ɤ��������ޤ���'; //���ͤθ��ɤ�����
  var $reporter_duty     = '(�֥�) �Ͽͳ��˽���줿�褦�Ǥ�'; //�֥󲰤ν޿�
  var $vote_sudden_death = '�ϥ���å��ष�ޤ���'; //��ɼ�ϥ�����
  var $chicken           = '�Ͼ����Ԥ��ä��褦�Ǥ�';   //������
  var $rabbit            = '�ϥ��������ä��褦�Ǥ�';   //������
  var $perverseness      = '��ŷ�ٵ����ä��褦�Ǥ�';   //ŷ�ٵ�
  var $flattery          = '�ϥ��ޤ�����ä��褦�Ǥ�'; //���ޤ���
  var $impatience        = '��û�����ä��褦�Ǥ�';     //û��

  //OutputAbility() : ǽ�Ϥ�ɽ��
  var $ability_dead     = '���ʥ���©�䤨�ޤ���������'; //���Ǥ�����

  //CheckNightVote() : �����ɼ
  var $ability_wolf_eat      = '���������ͤ����򤷤Ƥ�������';     //��ϵ����ɼ
  var $ability_mage_do       = '�ꤦ�ͤ����򤷤Ƥ�������';         //�ꤤ�դ���ɼ
  var $ability_guard_do      = '��Ҥ���ͤ����򤷤Ƥ�������';     //��ͤ���ɼ
  var $ability_reporter_do   = '���Ԥ���ͤ����򤷤Ƥ�������';     //�֥󲰤���ɼ
  var $ability_cupid_do      = '��ӤĤ�����ͤ����򤷤Ƥ�������'; //���塼�ԥåɤ���ɼ
  var $ability_mania_do      = 'ǽ�Ϥ򿿻���ͤ����򤷤Ƥ�������'; //���åޥ˥�����ɼ
  var $ability_poison_cat_do = '��������ͤ����򤷤Ƥ�������';     //ǭ������ɼ

  //-- game_play.php --//
  //CheckSilence()
  var $silence = '�ۤɤ����ۤ�³����'; //���ۤǻ��ַв� (���äǻ��ַв���)
  //������ηٹ��å�����
  // var $sudden_death_announce = '��ɼ��λ����ʤ����ϻष���Ϲ����Ĥ��Ƥ��ޤ��ޤ�';
  var $sudden_death_announce = '��ɼ��λ����ʤ����ϥ��������ꤵ��Ƥ��ޤ��ޤ�';
  // var $sudden_death_time = '������ˤʤ�ޤǸ塧'; //������ȯư�ޤ�
  var $sudden_death_time = '���������ꤵ���ޤǸ塧';
  // var $sudden_death = '�����������˴���ʤ�ˤʤ��ޤ���'; //������
  var $sudden_death = '����ϻ��Ϣ�����ޤ���';

  //��ɼ�ꥻ�å�
  var $vote_reset = '����ɼ���ꥻ�åȤ���ޤ�����������ɼ���Ƥ���������';

  //ȯ���ִ�����
  // var $gentleman_header = "�����ڤ��Τ��餤���̤ꤹ����Υʥ��ȡ�\n";  //�»� (��Ⱦ)
  // var $gentleman_footer = '�Ϥ狼�äƤ�褦���ʡ������ߤ��뤫�饸�塼�������äƤ��'; //�»� (��Ⱦ)
  var $gentleman_header = "���Ԥ���������\n�����ġ�";  //�»� (��Ⱦ)
  var $gentleman_footer = '�ͤλĤ�����̣�ˤ������ޤ��ġġ�'; //�»� (��Ⱦ)
  var $lady_header = "����ʤΤ������Ρ���\n"; //�ʽ� (��Ⱦ)
  var $lady_footer = '����������ľ�äơ����錄������­��ʤ�ʤ�������'; //�ʽ� (��Ⱦ)

  //-- game_vote.php --//
  //Kick ��¼�����ä���
  var $kick_out = '������ʤ򤢤��錄����¼������ޤ���';

  //CheckVoteGameStart()
  // var $chaos = '����⡼�ɤǤ��������������ޤ���'; //����¼����������
  var $chaos = '����⡼�ɤ������������̩����������ڤ���Ǥ͢�';

  //OutputVoteBeforeGame()
  var $submit_kick_do    = '�оݤ򥭥å�����˰�ɼ'; //Kick ��ɼ�ܥ���
  var $submit_game_start = '������򳫻Ϥ���˰�ɼ'; //�����೫�ϥܥ���

  //OutputVoteDay()
  var $submit_vote_do = '�оݤ�跺����˰�ɼ'; //�跺��ɼ�ܥ���

  //OutputVoteNight()
  var $submit_wolf_eat      = '�оݤ�������� (����)'; //��ϵ�ν���ܥ���
  var $submit_mage_do       = '�оݤ��ꤦ';            //�ꤤ�դ���ɼ�ܥ���
  var $submit_guard_do      = '�оݤ��Ҥ���';        //��ͤ���ɼ�ܥ���
  var $submit_reporter_do   = '�оݤ����Ԥ���';        //�֥󲰤���ɼ�ܥ���
  var $submit_cupid_do      = '�оݤ˰����������';    //���塼�ԥåɤ���ɼ�ܥ���
  var $submit_mania_do      = '�оݤ򿿻���';          //���åޥ˥�����ɼ�ܥ���
  var $submit_poison_cat_do = '�оݤ���������';        //ǭ������ɼ�ܥ���
}
?>
