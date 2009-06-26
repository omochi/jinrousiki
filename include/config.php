<?php
require_once(dirname(__FILE__) . '/message_class.php'); //�����ƥ��å�������Ǽ���饹
require_once(dirname(__FILE__) . '/system_class.php');  //�����ƥ�����Ǽ���饹

//�������ƥʥ�����
class RoomConfig{
  //�����Ǹ�β��ä�����¼�ˤʤ�ޤǤλ��� (��)
  //(���ޤ�û��������������ȶ��礹���ǽ������)
  var $die_room = 1200;
  // var $die_room = 12000; //�ǥХå��Ѥ�Ĺ�����Ƥ���

  //��λ���������Υ桼���Υ��å���� ID �ǡ����򥯥ꥢ����ޤǤλ��� (��)
  var $clear_session_id = 1200;

  //����Ϳ��Υꥹ�� (RoomImage->max_user_list ��Ϣư������)
  var $max_user_list = array(8, 16, 22);
}

//����������
class GameConfig{
  // ������Ͽ //
  //��¼���� (Ʊ��������Ʊ�� IP ��ʣ����Ͽ) (true�����Ĥ��ʤ� / false�����Ĥ���)
  var $entry_one_ip_address = true;
  // var $entry_one_ip_address = false; //�ǥХå���

  // ��ɼ //
  var $kick = 3; //��ɼ�� KICK ������Ԥ���
  var $draw = 5; //����ɼ�����ܤǰ���ʬ���Ȥ��뤫

  // �� //
  //���ǼԤ��ߤä��ݤ˴������ޤ���о� (true:��ɼ�ԥ����� / false:����������)
  // var $poison_only_voter = false; // �ޤ���������Ƥ��ޤ���
  var $poison_only_eater = true; //ϵ�����ǼԤ������ݤ˴������ޤ���о� (true:��ɼ�Ը��� / false:������)
  var $cupid_self_shoot  = 10; //���塼�ԥåɤ�¾���Ǥ���ǽ�Ȥʤ����¼�Ϳ�

  // �ְ۵ġפ��� //
  var $objection = 5; //������
  var $objection_image = 'img/objection.gif'; //�ְ۵ġפ���ܥ���β����ѥ�

  // ��ư���� //
  var $auto_reload = true; //game_view.php �Ǽ�ư������ͭ���ˤ��� / ���ʤ� (��������٤����)
  var $auto_reload_list = array(30, 45, 60); //��ư�����⡼�ɤι����ֳ�(��)�Υꥹ��
}

//������λ�������
class TimeConfig{
  //���ס��������Ĥ���֥���Ǥ������ͤ�᤮�����ɼ���Ƥ��ʤ��ͤ������ष�ޤ�(��)
  var $sudden_death = 180;

  // --�ꥢ�륿������-- //
  var $default_day   = 5; //�ǥե���ȤΥꥢ�륿�������ξ���������»���(ʬ)
  var $default_night = 3; //�ǥե���ȤΥꥢ�륿�������ξ���������»���(ʬ)

  // --���ä��Ѥ������ۻ�����-- //
  //������»���(���12���֡�spend_time=1(Ⱦ��100ʸ������) �� 12���� �� $day �ʤߤޤ�)
  var $day = 48;

  //������»���(��� 6���֡�spend_time=1(Ⱦ��100ʸ������) ��  6���� �� $night �ʤߤޤ�)
  var $night = 24;

  //��ꥢ�륿�������Ǥ������ͤ�᤮������ۤȤʤꡢ���ꤷ�����֤��ʤߤޤ�(��)
  var $silence = 60;

  //���۷в���� (12���� �� $day(��) or 6���� �� $night (��) �� $silence_pass �ܤλ��֤��ʤߤޤ�)
  var $silence_pass = 4;
}

//������ץ쥤���Υ����������
class IconConfig{
  var $path   = './user_icon';   //�桼����������ǥ��쥯�ȥ�
  var $width  = 45;              //ɽ��������(��)
  var $height = 45;              //ɽ��������(�⤵)
  var $dead   = 'img/grave.jpg'; //���
  var $wolf   = 'img/wolf.gif';  //ϵ
}

//���ϻ������ꥹ�ȡ�����ԡ����ϼԡ����Ǽԥ��ץ���󤬤���Ȥ�����Ƭ���������񤭤���ޤ�
$role_list = array(
	  4 => array('human','wolf','mage','mad') ,
	 // 4 => array('human','wolf','poison','cupid') ,  //�ǡ�����Ϣ���ƥ�����
	 5 => array('human','wolf','mage','mad','poison') ,
	 6 => array('human','mage','poison','wolf','mad','cupid') ,
	 7 => array('human','human','human','wolf','mage','guard','fox') ,
	 8 => array('human','human','human','human','human','wolf','wolf','mage') ,
	 9 => array('human','human','human','human','human','wolf','wolf','mage','necromancer') ,
	10 => array('human','human','human','human','human','wolf','wolf','mage','necromancer','mad') ,
	11 => array('human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard') ,
	12 => array('human','human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard') ,
	13 => array('human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard','common','common') ,
	14 => array('human','human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard','common','common') ,
	15 => array('human','human','human','human','human','human','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	16 => array('human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	17 => array('human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	18 => array('human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	19 => array('human','human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	20 => array('human','human','human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	21 => array('human','human','human','human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox') ,
	22 => array('human','human','human','human','human','human','human','human','human','human','human','human','wolf','wolf','wolf','mage','necromancer','mad','guard','common','common','fox')
	);

// ����������Ͽ���� //
class UserIcon{
  var $name   = 20;    //��������̾�ˤĤ�����ʸ����(Ⱦ��)
  var $size   = 15360; //���åץ��ɤǤ��륢������ե�����κ�������(ñ�̡��Х���)
  var $width  = 45;    //���åץ��ɤǤ��륢������κ�����
  var $height = 45;    //���åץ��ɤǤ��륢������κ���⤵
  var $number = 1000;  //��Ͽ�Ǥ��륢������κ����
}

// ����ɽ������ //
class OldLogConfig{
  var $one_page = 20;   //����������1�ڡ����Ǥ����Ĥ�¼��ɽ�����뤫
  var $reverse  = true; //�ǥե���Ȥ�¼�ֹ��ɽ���� (on:�դˤ��� / off:���ʤ�)
}

// �ǡ�����Ǽ���饹����� //
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
