<?php
// �����ƥ��å�������Ǽ���饹
class Message{
  //-- room_manger.php --//
  //CreateRoom() : ¼����
  //�����귯�Υ�����
  var $dummy_boy_comment = '�ͤϤ��������ʤ���';

  //�����귯�ΰ��
  var $dummy_boy_last_words = '�ͤϤ��������ʤ��äƸ��ä��Τˡġ�';

  var $game_option_wish_role            = '����˾��';
  var $game_option_real_time            = '�ꥢ�륿������';
  var $game_option_dummy_boy            = '��������Ͽ����귯';
  var $game_option_gm_login             = '�����귯�� GM';
  var $game_option_open_vote            = '��ɼ����ɼ�����ɽ����';
  var $game_option_not_open_cast        = '��������������ʤ�';
  var $game_option_decide               = '������о�';
  var $game_option_authority            = '���ϼ��о�';
  var $game_option_poison               = '���Ǽ��о�';
  var $game_option_cupid                = '���塼�ԥå��о�';
  var $game_option_boss_wolf            = '��ϵ�о�';
  var $game_option_poison_wolf          = '��ϵ�о�';
  var $game_option_mania                = '���åޥ˥��о�';
  var $game_option_medium               = '����о�';
  var $game_option_liar                 = 'ϵ��ǯ¼';
  var $game_option_gentleman            = '�»Ρ��ʽ�¼';
  var $game_option_sudden_death         = '�����μ�¼';
  var $game_option_perverseness         = 'ŷ�ٵ�¼';
  var $game_option_full_mania           = '���åޥ˥�¼';
  var $game_option_chaos                = '����⡼��';
  var $game_option_chaosfull            = '��������⡼��';
  var $game_option_chaos_open_cast      = '��������Τ���';
  var $game_option_chaos_open_cast_camp = '�رĤ����Τ���';
  var $game_option_chaos_open_cast_role = '�򿦤����Τ���';
  var $game_option_secret_sub_role      = '�����򿦤�ɽ�����ʤ�';
  var $game_option_no_sub_role          = '�����򿦤�Ĥ��ʤ�';
  var $game_option_quiz                 = '������¼';
  var $game_option_duel                 = '��Ʈ¼';

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

  //����Ծ���
  var $victory_quiz = '[����Ծ���] ���β����ԤˤϤޤ��󤤡ġĽ��Ԥ���Τ�';

  //����Ի�˴
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
  var $objection = '���ְ۵ġפ򿽤�Ω�Ƥޤ���'; //�ְ۵ġפ���
  // var $game_start = '�ϥ����೫�Ϥ���ɼ���ޤ���' //�����೫����ɼ //���ߤ��Ի���
  var $kick_do          = '�� KICK ��ɼ���ޤ���'; //KICK ��ɼ
  var $vote_do          = '�˽跺��ɼ���ޤ���'; //�跺��ɼ
  var $wolf_eat         = '��������Ĥ��ޤ���'; //��ϵ����ɼ
  var $mage_do          = '���ꤤ�ޤ�'; //�ꤤ�դ���ɼ
  var $voodoo_killer_do = '�μ�����㱤��ޤ�'; //���ۻդ���ɼ
  var $jammer_do        = '���ꤤ��˸�����ޤ�'; //���ⶸ�ͤ���ɼ
  var $trap_do          = '�μ��դ�櫤�ųݤ��ޤ���'; //櫻դ���ɼ
  var $trap_not_do      = '������֤�Ԥ��ޤ���Ǥ���'; //櫻դΥ���󥻥���ɼ
  var $voodoo_do        = '�˼����򤫤��ޤ�'; //���ѻդ���ɼ
  var $guard_do         = '�θ�Ҥ��դ��ޤ���'; //��ͤ���ɼ
  var $anti_voodoo_do   = '�����㱤��ޤ�'; //�������ɼ
  var $reporter_do      = '�����Ԥ��ޤ���'; //�֥󲰤���ɼ
  var $revive_do        = '���������֤򤷤ޤ���'; //ǭ������ɼ
  var $revive_not_do    = '���������֤򤷤ޤ���Ǥ���'; //ǭ���Υ���󥻥���ɼ
  var $assassin_do      = '��������Ĥ��ޤ���'; //�Ż��Ԥ���ɼ
  var $assassin_not_do  = '�ϰŻ���Ԥ��ޤ���Ǥ���'; //�Ż��ԤΥ���󥻥���ɼ
  var $mania_do         = '��ǽ�Ϥ򿿻��뤳�Ȥˤ��ޤ���'; //���åޥ˥�����ɼ
  var $cupid_do         = '�˰�����������ޤ���'; //���塼�ԥåɤ���ɼ

  var $morning_header = 'ī��������'; //ī�Υإå���
  var $morning_footer = '���ܤ�ī����äƤ��ޤ���'; //ī�Υեå���
  var $night = '����������Ť��Ť����뤬��äƤ��ޤ���'; //��
  var $dummy_boy = '�����귯��'; //����GM�⡼���ѥإå���

  var $wolf_howl = '���������󡦡���'; //ϵ�α��ʤ�
  // var $common_talk = '�ҥ��ҥ�������'; //��ͭ�Ԥξ���
  var $common_talk = '����������������������������'; //��ͭ�Ԥξ���
  var $howling = '���������󡦡���'; //���ԡ������β������̲�

  //OutputLastWords() : �����ɽ��
  var $lastwords = '�뤬���������������˴���ʤä����ΰ���񤬸��Ĥ���ޤ���';

  //OutoutDeadManType() : �����ɽ��
  // var $vote_killed      = '����ɼ�η�̽跺����ޤ���'; //�ߤ�
  var $vote_killed       = '�����뤴�ä� (��ɼ) �η�̤Ԥ��塼�� (�跺) ���ޤ���';
  // var $deadman           = '��̵�ĤʻѤ�ȯ������ޤ���'; //������ɽ��������å�����
  var $deadman           = '��̵�Ĥ��餱���λѤ�ȯ������ޤ���';
  var $wolf_killed       = '�Ͽ�ϵ�α¿��ˤʤä��褦�Ǥ�'; //��ϵ�ν���
  var $trapped           = '��櫤ˤ����äƻ�˴�����褦�Ǥ�'; //�
  var $fox_dead          = '(�Ÿ�) ���ꤤ�դ˼��������줿�褦�Ǥ�'; //�Ѽ���
  var $cursed            = '�ϼ��Ǥ˼��������줿�褦�Ǥ�'; //���֤�
  var $hunted            = '�ϼ�ͤ˼��줿�褦�Ǥ�'; //��ͤμ��
  var $reporter_duty     = '(�֥�) �Ͽͳ������Ԥ��Ƥ��ޤ�������줿�褦�Ǥ�'; //�֥󲰤ν޿�
  var $poison_dead       = '���Ǥ��������˴�����褦�Ǥ�'; //���ǼԤ�ƻϢ��
  var $assassin_killed   = '�ϰŻ����줿�褦�Ǥ�'; //�Ż��Ԥν���
  var $revive_success    = '�������֤�ޤ���'; //��������
  var $revive_failed     = '�������˼��Ԥ����褦�Ǥ�'; //��������
  var $lovers_followed   = '�����ͤθ���ɤ��������ޤ���'; //���ͤθ��ɤ�����
  var $vote_sudden_death = '�ϥ���å��ष�ޤ���'; //��ɼ�ϥ���å���
  var $chicken           = '�Ͼ����Ԥ��ä��褦�Ǥ�'; //������
  var $rabbit            = '�ϥ��������ä��褦�Ǥ�'; //������
  var $perverseness      = '��ŷ�ٵ����ä��褦�Ǥ�'; //ŷ�ٵ�
  var $flattery          = '�ϥ��ޤ�����ä��褦�Ǥ�'; //���ޤ���
  var $impatience        = '��û�����ä��褦�Ǥ�'; //û��
  var $panelist          = '�ϲ����� (������) ���ä��褦�Ǥ�'; //������

  //OutputAbility() : ǽ�Ϥ�ɽ��
  var $ability_dead     = '���ʥ���©�䤨�ޤ���������'; //���Ǥ�����

  //CheckNightVote() : �����ɼ
  var $ability_wolf_eat         = '���������ͤ����򤷤Ƥ�������'; //��ϵ
  var $ability_mage_do          = '�ꤦ�ͤ����򤷤Ƥ�������'; //�ꤤ�շ�
  var $ability_voodoo_killer_do = '������㱤��ͤ����򤷤Ƥ�������'; //���ۻ�
  var $ability_jammer_do        = '�ꤤ��˸������ͤ����򤷤Ƥ�������'; //���ⶸ��
  var $ability_trap_do          = '櫤����֤���ͤ����򤷤Ƥ�������'; //櫻�
  var $ability_voodoo_do        = '�����򤫤���ͤ����򤷤Ƥ�������'; //���ѻա�����
  var $ability_guard_do         = '��Ҥ���ͤ����򤷤Ƥ�������'; //��ͷ�
  var $ability_anti_voodoo_do   = '���㱤��ͤ����򤷤Ƥ�������'; //���
  var $ability_reporter_do      = '���Ԥ���ͤ����򤷤Ƥ�������'; //�֥�
  var $ability_revive_do        = '��������ͤ����򤷤Ƥ�������'; //ǭ��
  var $ability_assassin_do      = '�Ż�����ͤ����򤷤Ƥ�������'; //�Ż���
  var $ability_mania_do         = 'ǽ�Ϥ򿿻���ͤ����򤷤Ƥ�������'; //���åޥ˥�
  var $ability_cupid_do         = '��ӤĤ�����ͤ����򤷤Ƥ�������'; //���塼�ԥå�

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
  var $cute_wolf = ''; //˨ϵ���Կ��� (���ʤ�ϵ�α��ʤ��ˤʤ�)
  // var $gentleman_header = "���Ԥ���������\n";  //�»� (��Ⱦ)
  // var $gentleman_footer = '���󡢥ϥ󥱥����դ���Ȥ��Ƥ���ޤ�����'; //�»� (��Ⱦ)
  var $gentleman_header = "���Ԥ���������\n�����ġ�";  //�»� (��Ⱦ)
  var $gentleman_footer = '�ͤλĤ�����̣�ˤ������ޤ��ġġ�'; //�»� (��Ⱦ)
  //var $lady_header = "���Ԥ��ʤ�����\n"; //�ʽ� (��Ⱦ)
  //var $lady_footer = '���������ʤ��äƤ��Ƥ衣'; //�ʽ� (��Ⱦ)
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
  //��ɼ�ܥ���
  var $submit_wolf_eat         = '�оݤ�������� (����)'; //��ϵ
  var $submit_mage_do          = '�оݤ��ꤦ'; //�ꤤ�շ�
  var $submit_voodoo_killer_do = '�оݤμ�����㱤�'; //���ۻ�
  var $submit_jammer_do        = '�оݤ��ꤤ��˸������'; //���ⶸ��
  var $submit_trap_do          = '�оݤμ��դ�櫤����֤���'; //櫻�
  var $submit_trap_not_do      = '櫤����֤��ʤ�'; //櫻�(����󥻥�)
  var $submit_voodoo_do        = '�оݤ˼����򤫤���'; //���ѻ�
  var $submit_guard_do         = '�оݤ��Ҥ���'; //��ͷ�
  var $submit_anti_voodoo_do   = '�оݤ����㱤�'; //���
  var $submit_reporter_do      = '�оݤ����Ԥ���'; //�֥�
  var $submit_revive_do        = '�оݤ���������'; //ǭ��
  var $submit_revive_not_do    = 'ï���������ʤ�'; //ǭ��(����󥻥�)
  var $submit_assassin_do      = '�оݤ�Ż�����'; //�Ż��Է�
  var $submit_assassin_not_do  = 'ï��Ż����ʤ�'; //�Ż��Է�(����󥻥�)
  var $submit_mania_do         = '�оݤ򿿻���'; //���åޥ˥�
  var $submit_cupid_do         = '�оݤ˰����������'; //���塼�ԥåɷ�

  //InsertRandomMessage()
  var $random_message_list = array(
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á��¤ϼ�����ȯ­����� PHP ��ޤȤ�˽񤤤����Ȥ���ͤϤ��ޤ���Ǥ�����',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á��¤ϼ�����ȯ­����� mysql ��ޤȤ�˰�����ͤϤۤȤ�ɤ��ޤ���Ǥ�����',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á��������γ�ȯ���Υ����ɤ�񤤤��ͤ�桹�ϡֹ䵣�ʿ͡פ�ɾ�����Ƥ��ޤ�����ͳ���������򸫤��餭�ä�ʬ����ޤ���',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á�����Υ����ɤ�ή�л���GM������ĺ������ΤǤ���',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á��������ΰ���Υ��󥻥ץȤϡ����Կԡסֿ����ʤ�Ƥ����ʤ��סֳڤ�����Ծ����פǤ���',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á��ֺ����ꤤ�աפˤϥ�ǥ뤬���ޤ����ֺ��פǼ����ͳ������Ƥ������ꤤ�դ��٤�餸���ץ쥤�䡼����Ǥ���',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á��ֵ��Ρפˤϥ�ǥ뤬���ޤ���¿���ο�ϵ��㤫��������ǽ��ͤ��٤�餸���ץ쥤�䡼����Ǥ���',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á���˨ϵ�פˤϥ�ǥ뤬���ޤ���2���ܤ�ī��ˡ֤Ϥ����Ǥ϶����ޤ��礦���פ�ȯ�����Ƥ��ޤä��İ�����ϵ����Ǥ���',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á��ֱ������פˤϥ�ǥ뤬���ޤ�������Ū�����٤�ؤ���ǽ�Ԥ��٤�餸���ץ쥤�䡼����Ǥ���',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á��ֶ��Ǽԡפˤϥ�ǥ뤬���ޤ����ߤ�줿���˹����٤ǿͳ��򴬤��������ǼԤ��٤�餸���ץ쥤�䡼����Ǥ���',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á������Ǽԡפˤϥ�ǥ뤬���ޤ���������³���ƿ�ϵ�γ��ߤ�����󤻤����ǼԤ��٤�餸���ץ쥤�䡼����Ǥ���',
    '��ȯ�Ԥ���Τɤ��Ǥ⤤���á���������͵��Ρ֤Ҥ褳����ΡפΥ����ǥ�����ή�л���GM������ĺ������ΤǤ���',
			      );
}
$MESSAGE = new Message();
?>
