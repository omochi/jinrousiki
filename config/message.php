<?php
//-- ���ܥ����ƥ��å����� --//
class Message{
  //-- room_manger.php --//
  //CreateRoom() : ¼����
  //�����귯�Υ�����
  var $dummy_boy_comment = '�ͤϤ��������ʤ���';

  //�����귯�ΰ��
  var $dummy_boy_last_words = '�ͤϤ��������ʤ��äƸ��ä��Τˡġ�';

  //-- user_manager.php --//
  //EntryUser() : �桼����Ͽ
  //��¼��å�����
  //var $entry_user = '����¼�ν����ˤ�äƤ��ޤ���'
  //var $entry_user = '����¼�λ��Ҳ��ˤ�äƤ��ޤ���';
  var $entry_user = '���󤬸��۶����ꤷ�ޤ���';

  //-- game_view.php & OutputGameHTMLHeader() --//
  var $vote_announce = '���֤�����ޤ�����ɼ���Ƥ�������'; //���ä����»����ڤ�

  //-- game_functions.php --//
  //OutputRevoteList() : ����ɼ���ʥ���
  var $revote = '����ɼ�Ȥʤ�ޤ���'; //��ɼ���
  var $draw_announce = '����ɼ�Ȥʤ�Ȱ���ʬ���ˤʤ�ޤ�'; //����ʬ������

  //OutputTalkLog() : ���á������ƥ��å���������
  var $objection = '���ְ۵ġפ򿽤�Ω�Ƥޤ���'; //�ְ۵ġפ���
  //var $game_start = '�ϥ����೫�Ϥ���ɼ���ޤ���' //�����೫����ɼ (���ߤ��Ի���)
  var $kick_do          = '�� KICK ��ɼ���ޤ���'; //KICK ��ɼ
  var $vote_do          = '�˽跺��ɼ���ޤ���'; //�跺��ɼ
  var $wolf_eat         = '��������Ĥ��ޤ���'; //��ϵ����ɼ
  var $mage_do          = '���ꤤ�ޤ�'; //�ꤤ�դ���ɼ
  var $voodoo_killer_do = '�μ�����㱤��ޤ�'; //���ۻդ���ɼ
  var $jammer_do        = '���ꤤ��˸�����ޤ�'; //���Ƥ���ɼ
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
  var $mind_scanner_do  = '�ο����ɤߤޤ�'; //���Ȥ����ɼ
  var $cupid_do         = '�˰�����������ޤ���'; //���塼�ԥåɤ���ɼ
  var $mania_do         = '��ǽ�Ϥ򿿻��뤳�Ȥˤ��ޤ���'; //���åޥ˥�����ɼ

  var $morning_header = 'ī��������'; //ī�Υإå���
  var $morning_footer = '���ܤ�ī����äƤ��ޤ���'; //ī�Υեå���
  var $night = '����������Ť��Ť����뤬��äƤ��ޤ���'; //��
  var $dummy_boy = '�����귯��'; //����GM�⡼���ѥإå���

  var $wolf_howl = '���������󡦡���'; //ϵ�α��ʤ�
  //var $common_talk = '�ҥ��ҥ�������'; //��ͭ�Ԥξ���
  var $common_talk = '����������������������������'; //��ͭ�Ԥξ���
  var $howling = '���������󡦡���'; //���ԡ������β������̲�

  //OutputLastWords() : �����ɽ��
  var $lastwords = '�뤬���������������˴���ʤä����ΰ���񤬸��Ĥ���ޤ���';

  //OutoutDeadManType() : �����ɽ��
  //var $vote_killed      = '����ɼ�η�̽跺����ޤ���'; //�ߤ�
  var $vote_killed        = '�����뤴�ä� (��ɼ) �η�̤Ԥ��塼�� (�跺) ���ޤ���';
  //var $deadman           = '��̵�ĤʻѤ�ȯ������ޤ���'; //������ɽ��������å�����
  var $deadman            = '��̵�Ĥ��餱���λѤ�ȯ������ޤ���';
  var $wolf_killed        = '�Ͽ�ϵ�α¿��ˤʤä��褦�Ǥ�'; //��ϵ�ν���
  var $possessed          = '��ï������ͤ����褦�Ǥ�'; //��ϵ�����
  var $possessed_targeted = '����ϵ����ͤ��줿�褦�Ǥ�'; //��ϵ�ν���
  var $possessed_reset    = '����ͤ��鳫�����줿�褦�Ǥ�'; //��ͥꥻ�å�
  var $dream_killed       = '���Ӥα¿��ˤʤä��褦�Ǥ�'; //�Ӥν���
  var $trapped            = '��櫤ˤ����äƻ�˴�����褦�Ǥ�'; //�
  var $fox_dead           = '(�Ÿ�) ���ꤤ�դ˼��������줿�褦�Ǥ�'; //�Ѽ���
  var $cursed             = '�ϼ��Ǥ˼��������줿�褦�Ǥ�'; //���֤�
  var $hunted             = '�ϼ�ͤ˼��줿�褦�Ǥ�'; //��ͤμ��
  var $reporter_duty      = '(�֥�) �Ͽͳ������Ԥ��Ƥ��ޤ�������줿�褦�Ǥ�'; //�֥󲰤ν޿�
  var $poison_dead        = '���Ǥ��������˴�����褦�Ǥ�'; //���ǼԤ�ƻϢ��
  var $assassin_killed    = '�ϰŻ����줿�褦�Ǥ�'; //�Ż��Ԥν���
  var $priest_returned    = '��ŷ�˵��ä��褦�Ǥ�'; //ŷ�ͤε���
  var $revive_success     = '�������֤�ޤ���'; //��������
  var $revive_failed      = '�������˼��Ԥ����褦�Ǥ�'; //��������
  var $lovers_followed    = '�����ͤθ���ɤ��������ޤ���'; //���ͤθ��ɤ�����
  var $vote_sudden_death  = '�ϥ���å��ष�ޤ���'; //��ɼ�ϥ���å���
  var $chicken            = '�Ͼ����Ԥ��ä��褦�Ǥ�'; //������
  var $rabbit             = '�ϥ��������ä��褦�Ǥ�'; //������
  var $perverseness       = '��ŷ�ٵ����ä��褦�Ǥ�'; //ŷ�ٵ�
  var $flattery           = '�ϥ��ޤ�����ä��褦�Ǥ�'; //���ޤ���
  var $impatience         = '��û�����ä��褦�Ǥ�'; //û��
  var $celibacy           = '���ȿȵ�²���ä��褦�Ǥ�'; //�ȿȵ�²
  var $jealousy           = '(����) �϶�ɱ���ʤޤ줿�褦�Ǥ�'; //��ɱ���ʤ��֤�
  var $panelist           = '�ϲ����� (������) ���ä��褦�Ǥ�'; //������

  //OutputAbility() : ǽ�Ϥ�ɽ��
  var $ability_dead = '���ʥ���©�䤨�ޤ���������'; //���Ǥ�����

  //CheckNightVote() : �����ɼ
  var $ability_vote             = '�跺����ͤ����򤷤Ƥ�������'; //��ν跺��ɼ
  var $ability_wolf_eat         = '���������ͤ����򤷤Ƥ�������'; //��ϵ
  var $ability_mage_do          = '�ꤦ�ͤ����򤷤Ƥ�������'; //�ꤤ�շ�
  var $ability_voodoo_killer_do = '������㱤��ͤ����򤷤Ƥ�������'; //���ۻ�
  var $ability_jammer_do        = '�ꤤ��˸������ͤ����򤷤Ƥ�������'; //���ⶸ��
  var $ability_trap_do          = '櫤����֤���ͤ����򤷤Ƥ�������'; //櫻�
  var $ability_dream_eat        = '̴����٤�ͤ����򤷤Ƥ�������'; //��
  var $ability_voodoo_do        = '�����򤫤���ͤ����򤷤Ƥ�������'; //���ѻա�����
  var $ability_guard_do         = '��Ҥ���ͤ����򤷤Ƥ�������'; //��ͷ�
  var $ability_anti_voodoo_do   = '���㱤��ͤ����򤷤Ƥ�������'; //���
  var $ability_reporter_do      = '���Ԥ���ͤ����򤷤Ƥ�������'; //�֥�
  var $ability_revive_do        = '��������ͤ����򤷤Ƥ�������'; //ǭ��
  var $ability_assassin_do      = '�Ż�����ͤ����򤷤Ƥ�������'; //�Ż���
  var $ability_mind_scanner_do  = '�����ɤ�ͤ����򤷤Ƥ�������'; //���Ȥ�
  var $ability_cupid_do         = '��ӤĤ�����ͤ����򤷤Ƥ�������'; //���塼�ԥå�
  var $ability_mania_do         = 'ǽ�Ϥ򿿻���ͤ����򤷤Ƥ�������'; //���åޥ˥�

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

  //-- InsertRandomMessage() --//
  //GameConfig->random_message �� true �ˤ����
  //���������줿��å��������������ɽ�������
  var $random_message_list = array();
}

//-- �����४�ץ����̾ --//
class GameOptionMessage{
  var $room_name            = '¼��̾��';
  var $room_comment         = '¼�ˤĤ��Ƥ�����';
  var $max_user             = '����Ϳ�';
  var $wish_role            = '����˾��';
  var $real_time            = '�ꥢ�륿������';
  var $dummy_boy            = '��������Ͽ����귯';
  var $gm_login             = '�����귯�� GM';
  var $open_vote            = '��ɼ����ɼ�����ɽ����';
  var $not_open_cast        = '��������������ʤ�';
  var $decide               = '������о�';
  var $authority            = '���ϼ��о�';
  var $poison               = '���Ǽ��о�';
  var $cupid                = '���塼�ԥå��о�';
  var $boss_wolf            = '��ϵ�о�';
  var $poison_wolf          = '��ϵ�о�';
  var $mania                = '���åޥ˥��о�';
  var $medium               = '����о�';
  var $liar                 = 'ϵ��ǯ¼';
  var $gentleman            = '�»Ρ��ʽ�¼';
  var $sudden_death         = '�����μ�¼';
  var $perverseness         = 'ŷ�ٵ�¼';
  var $full_mania           = '���åޥ˥�¼';
  var $chaos                = '����⡼��';
  var $chaosfull            = '��������⡼��';
  var $chaos_open_cast      = '��������Τ���';
  var $chaos_open_cast_camp = '�رĤ����Τ���';
  var $chaos_open_cast_role = '�򿦤����Τ���';
  var $secret_sub_role      = '�����򿦤�ɽ�����ʤ�';
  var $no_sub_role          = '�����򿦤�Ĥ��ʤ�';
  var $quiz                 = '������¼';
  var $duel                 = '��Ʈ¼';
}

//-- �����४�ץ����̾������ --//
class GameOptionCaptionMessage{
  var $max_user             = '�����<a href="rule.php">�롼��</a>���ǧ���Ʋ�����';
  var $wish_role            = '��˾���������Ǥ��ޤ������ʤ�뤫�ϱ��Ǥ�';
  var $real_time            = '���»��֤��»��֤Ǿ��񤵤�ޤ�';
  var $no_dummy_boy         = '�����귯�ʤ�';
  var $dummy_boy            = '�����귯����(�������롢�����귯��ϵ�˿��٤��ޤ�)';
  var $gm_login_header      = '���� GM �������귯�Ȥ��ƥ����󤷤ޤ�';
  var $gm_login_footer      = '������桼��̾�ϡ�dummy_boy�פǤ���GM ����¼ľ���ɬ��̾��äƤ�������';
  var $open_vote            = '�ָ��ϼԡפʤɤΥ����򿦤�ʬ����䤹���ʤ�ޤ�';
  var $not_open_cast        = '��Ǥ�ï���ɤ��򿦤ʤΤ�����������ޤ���ǭ���������Ǥ��ޤ�';
  var $decide               = '��ɼ��Ʊ���λ�������Ԥ���ɼ�褬ͥ�褵��ޤ���[��Ǥ]';
  var $authority            = '��ɼ��ɼ������ɼ�ˤʤ�ޤ���[��Ǥ]';
  var $poison               = '�跺���줿��ϵ�˿��٤�줿��硢ƻϢ��ˤ��ޤ���[¼��2������1����ϵ1]';
  var $cupid         = '�������������������ͤˤ��ޤ������ͤȤʤä���ͤϾ�����郎�Ѳ����ޤ�<br>������[¼��1�����塼�ԥå�1]';
  var $boss_wolf            = '�ꤤ��̤���¼�͡ס���ǽ��̤�����ϵ�פ�ɽ�������ϵ�Ǥ���[��ϵ1����ϵ1]';
  var $poison_wolf          = '�ߤ�줿���˥������¼�Ͱ�ͤ򴬤�ź���ˤ���ϵ�Ǥ���<br>������[��ϵ1����ϵ1��¼��1������1]';
  var $mania                = '�������¾��¼�ͤ��򿦤򥳥ԡ������ü���򿦤Ǥ���[¼��1�����åޥ˥�1]';
  var $medium               = '�����ष���ͤν�°�رĤ�ʬ�����ü����ǽ�ԤǤ���[¼��2�����1��������1]';
  var $liar                 = '������ǡ�ϵ��ǯ�פ��Ĥ��ޤ�';
  var $gentleman            = '���������̤˱������ֿ»Ρסֽʽ��פ��Ĥ��ޤ�';
  var $sudden_death         = '��������ɼ�ǥ���å��ह�륵���򿦤Τɤ줫���Ĥ��ޤ�';
  var $perverseness         = '�����ˡ�ŷ�ٵ��פ��Ĥ��ޤ��������Υ����򿦷ϥ��ץ���󤬶������դˤʤ�ޤ�';
  var $full_mania           = '��¼�͡פ������ֿ��åޥ˥��פ������ؤ��ޤ�';
  var $no_chaos             = '�̾��ϵ';
  var $chaos                = '�̾�¼�ܦ����٤����򤬤֤�����⡼�ɤǤ�';
  var $chaosfull            = '��ϵ1���ꤤ��1�ʳ������Ƥ��򿦤�������Ȥʤ뿿������⡼�ɤǤ�';
  var $chaos_not_open_cast  = '����̵��';
  var $chaos_open_cast_camp = '�ر����� (�ر���ι�פ�����)';
  var $chaos_open_cast_role = '������ (�򿦤μ����̤˹�פ�����)';
  var $chaos_open_cast_full = '�������� (�̾�¼����)';
  var $secret_sub_role      = '�����򿦤�ʬ����ʤ��ʤ�ޤ�������⡼�����ѥ��ץ����';
  var $no_sub_role          = '�����򿦤�Ĥ��ޤ��󡧰���⡼�����ѥ��ץ����';
  var $quiz                 = '�����귯���ֽ���ԡפˤʤäƥ�������Ф��ޤ�';
  var $duel                 = '�ֿ�ϵ�ס�櫻աסְŻ��ԡפΤߤ����о줷�ʤ�¼�Ǥ�';
}

//-- ¼���ܿͤξ��Է�� --//
class VictoryMessage{
  //¼�;���
  var $human = '[¼�;���] ¼�ͤ����Ͽ�ϵ�η���䤹�뤳�Ȥ��������ޤ���';

  //��ϵ�����;���
  var $wolf = '[��ϵ�����;���] �Ǹ�ΰ�ͤ򿩤������ȿ�ϵã�ϼ��γ�ʪ�����¼���ˤ���';

  //�ŸѾ��� (¼�;�����)
  var $fox1 = '[�ŸѾ���] ��ϵ�����ʤ��ʤä��������Ũ�ʤɤ⤦���ʤ�';

  //�ŸѾ��� (��ϵ������)
  var $fox2 = '[�ŸѾ���] �ޥ̥��ʿ�ϵ�ɤ���٤����Ȥʤ��ưפ����Ȥ�';

  //���͡����塼�ԥåɾ���
  var $lovers = '[���͡����塼�ԥåɾ���] �������ˤϲ��Ԥ�̵�Ϥ��ä��ΤǤ���';

  //����Ծ���
  var $quiz = '[����Ծ���] ���β����ԤˤϤޤ��󤤡ġĽ��Ԥ���Τ�';

  //����Ի�˴
  var $quiz_dead = '[����ʬ��] ���Ȥ������������ΤޤޤǤϷ��夬�դ��ʤ�����';

  //����ʬ��
  var $draw = '[����ʬ��] ����ʬ���Ȥʤ�ޤ���';

  //����
  var $vanish = '[����ʬ��] ������ï���ʤ��ʤä��ġ�';

  //������¼
  var $unfinished = '[����ʬ��] ̸��ǻ���ʤäƲ��⸫���ʤ��ʤ�ޤ����ġ�';

  //��¼
  var $none = '���¤��ʹԤ��ƿͤ����ʤ��ʤ�ޤ���';

  var $self_win  = '���ʤ��Ͼ������ޤ���'; //�ܿ;���
  var $self_lose = '���ʤ������̤��ޤ���'; //�ܿ�����
  var $self_draw = '����ʬ���Ȥʤ�ޤ���'; //����ʬ��
}

//-- ��ɼ�������ѥ�å����� --//
class VoteMessage{
  //OutputVoteBeforeGame()
  var $kick_do    = '�оݤ򥭥å�����˰�ɼ'; //Kick ��ɼ�ܥ���
  var $game_start = '������򳫻Ϥ���˰�ɼ'; //�����೫�ϥܥ���

  //OutputVoteDay()
  var $vote_do = '�оݤ�跺����˰�ɼ'; //�跺��ɼ�ܥ���

  //OutputVoteNight()
  //��ɼ�ܥ���
  var $wolf_eat         = '�оݤ�������� (����)'; //��ϵ
  var $mage_do          = '�оݤ��ꤦ'; //�ꤤ��
  var $voodoo_killer_do = '�оݤμ�����㱤�'; //���ۻ�
  var $guard_do         = '�оݤ��Ҥ���'; //���
  var $anti_voodoo_do   = '�оݤ����㱤�'; //���
  var $reporter_do      = '�оݤ����Ԥ���'; //�֥�
  var $revive_do        = '�оݤ���������'; //ǭ��
  var $revive_not_do    = 'ï���������ʤ�'; //ǭ��(����󥻥�)
  var $assassin_do      = '�оݤ�Ż�����'; //�Ż���
  var $assassin_not_do  = 'ï��Ż����ʤ�'; //�Ż���(����󥻥�)
  var $mind_scanner_do  = '�оݤο����ɤ�'; //���Ȥ�
  var $voodoo_do        = '�оݤ˼����򤫤���'; //���ѻ�
  var $jammer_do        = '�оݤ��ꤤ��˸������'; //����
  var $dream_eat        = '�оݤ�̴�����'; //��
  var $trap_do          = '�оݤμ��դ�櫤����֤���'; //櫻�
  var $trap_not_do      = '櫤����֤��ʤ�'; //櫻�(����󥻥�)
  var $cupid_do         = '�оݤ˰����������'; //���塼�ԥå�
  var $mania_do         = '�оݤ򿿻���'; //���åޥ˥�
}
