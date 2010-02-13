<?php
class TimeCalculation{
  var $spend_day;      //��ꥢ�륿��������ȯ���Ǿ��񤵤����� (��)
  var $spend_night;    //��ꥢ�륿��������ȯ���Ǿ��񤵤����� (��)
  var $silence_day;    //��ꥢ�륿�����������ۤǷв᤹����� (��)
  var $silence_night;  //��ꥢ�륿�����������ۤǷв᤹����� (��)
  var $silence;        //��ꥢ�륿�����������ۤˤʤ�ޤǤλ���
  var $sudden_death;   //���»��֤�����������ह��ޤǤλ���
  var $die_room;       //��ư��¼�ˤʤ�ޤǤλ���
  var $establish_wait; //����¼��Ω�Ƥ���ޤǤ��Ԥ�����

  function TimeCalculation(){ $this->__construct(); }
  function __construct(){
    global $ROOM_CONF, $TIME_CONF;

    $day_seconds   = floor(12 * 60 * 60 / $TIME_CONF->day);
    $night_seconds = floor( 6 * 60 * 60 / $TIME_CONF->night);

    $this->spend_day      = ConvertTime($day_seconds);
    $this->spend_night    = ConvertTime($night_seconds);
    $this->silence_day    = ConvertTime($TIME_CONF->silence_pass * $day_seconds);
    $this->silence_night  = ConvertTime($TIME_CONF->silence_pass * $night_seconds);
    $this->silence        = ConvertTime($TIME_CONF->silence);
    $this->sudden_death   = ConvertTime($TIME_CONF->sudden_death);
    $this->die_room       = ConvertTime($ROOM_CONF->die_room);
    $this->establish_wait = ConvertTime($ROOM_CONF->establish_wait);
  }
}
