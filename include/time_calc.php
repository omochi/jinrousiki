<?php
class TimeCalculation{
  var $spend_day;     //��ꥢ�륿��������ȯ���Ǿ��񤵤����� (��)
  var $spend_night;   //��ꥢ�륿��������ȯ���Ǿ��񤵤����� (��)
  var $silence_day;   //��ꥢ�륿�����������ۤǷв᤹����� (��)
  var $silence_night; //��ꥢ�륿�����������ۤǷв᤹����� (��)
  var $silence;       //��ꥢ�륿�����������ۤˤʤ�ޤǤλ���
  var $sudden_death;  //���»��֤�����������ह��ޤǤλ���
  var $die_room;      //��ư��¼�ˤʤ�ޤǤλ���

  function TimeCalculation(){
    global $ROOM_CONF, $TIME_CONF;

    $day_seconds = floor(12 * 60 * 60 / $TIME_CONF->day);
    $this->spend_day = ConvertTime($day_seconds);

    $night_seconds = floor(6 * 60 * 60 / $TIME_CONF->night);
    $this->spend_night = ConvertTime($night_seconds);

    $this->silence_day   = ConvertTime($day_seconds   * $TIME_CONF->silence_pass);
    $this->silence_night = ConvertTime($night_seconds * $TIME_CONF->silence_pass);
    $this->silence       = ConvertTime($TIME_CONF->silence);
    $this->sudden_death  = ConvertTime($TIME_CONF->sudden_death);
    $this->die_room      = ConvertTime($ROOM_CONF->die_room);
  }
}
?>
