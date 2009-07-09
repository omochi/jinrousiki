<?php
// �����������饹�δ��쥯�饹��������ޤ���
class ImageManager{
  function GenerateTag($name, $alt, $class='icon'){
    $alt = htmlspecialchars($alt, ENT_QUOTES);
    $class = htmlspecialchars($class, ENT_QUOTES);
    return "<img class='$class' src='{$this->$name}' alt='$alt' title='$alt'>";
  }
}

//¼�Υ��ץ��������ѥ�
class RoomImage extends ImageManager{
  var $waiting = 'img/room_option/waiting.gif'; //¼�ꥹ�Ȥ��罸��β���
  var $playing = 'img/room_option/playing.gif'; //¼�ꥹ�ȤΥ�������β���

  var $wish_role     = 'img/room_option/wish_role.gif';     //����˾��
  var $real_time     = 'img/room_option/real_time.gif';     //����˾��
  var $dummy_boy     = 'img/room_option/dummy_boy.gif';     //�����귯����
  var $open_vote     = 'img/room_option/open_vote.gif';     //ɼ������
  var $not_open_cast = 'img/room_option/not_open_cast.gif'; //���������
  var $decide        = 'img/room_option/decide.gif';        //�����
  var $authority     = 'img/room_option/authority.gif';     //���ϼ�
  var $poison        = 'img/room_option/poison.gif';        //���Ǽ�
  var $cupid         = 'img/room_option/cupid.gif';         //���塼�ԥå�
  var $chaos         = 'img/room_option/chaos.gif';         //����
  var $chaosfull     = 'img/room_option/chaosfull.gif';     //��������

  //¼�κ���Ϳ��ꥹ�� (RoomConfig -> max_user_list ��Ϣư������)
  var $max_user_list = array(
			      8 => 'img/room_option/max8.gif',   // 8��
			     16 => 'img/room_option/max16.gif',  //16��
			     22 => 'img/room_option/max22.gif'   //22��
			     );
}

//�򿦤β����ѥ�
class RoleImage extends ImageManager{
  //�򿦤�����
  var $human                = 'img/role/human.jpg';                //¼��
  var $wolf                 = 'img/role/wolf.jpg';                 //��ϵ
  var $boss_wolf            = 'img/role/boss_wolf.jpg';            //��ϵ
  var $poison_wolf          = 'img/role/poison_wolf.jpg';          //��ϵ
  var $tongue_wolf          = 'img/role/tongue_wolf.jpg';          //���ϵ
  var $wolf_partner         = 'img/role/wolf_partner.jpg';         //��ϵ�����
  var $wolf_result          = 'img/role/wolf_result.jpg';          //���߷��
  var $mage                 = 'img/role/mage.jpg';                 //�ꤤ��
  var $soul_mage            = 'img/role/soul_mage.jpg';            //�����ꤤ��
  var $mage_result          = 'img/role/mage_result.jpg';          //�ꤤ���
  var $necromancer          = 'img/role/necromancer.jpg';          //��ǽ��
  var $necromancer_result   = 'img/role/necromancer_result.jpg';   //��ǽ���
  var $medium               = 'img/role/medium.jpg';               //���
  var $medium_result        = 'img/role/medium_result.jpg';        //������
  var $mad                  = 'img/role/mad.jpg';                  //����
  var $fanatic_mad          = 'img/role/fanatic_mad.jpg';          //������
  var $guard                = 'img/role/guard.jpg';                //���
  var $poison_guard         = 'img/role/poison_guard.jpg';         //����
  var $guard_success        = 'img/role/guard_success.jpg';        //��ͤθ������
  var $common               = 'img/role/common.jpg';               //��ͭ��
  var $common_partner       = 'img/role/common_partner.jpg';       //��ͭ�Ԥ����
  var $child_fox            = 'img/role/child_fox.jpg';            //�Ҹ�
  var $fox                  = 'img/role/fox.jpg';                  //�Ÿ�
  var $fox_partner          = 'img/role/fox_partner.jpg';          //�ŸѤ����
  var $fox_target           = 'img/role/fox_targeted.jpg';         //�Ÿѽ���
  var $poison               = 'img/role/poison.jpg';               //���Ǽ�
  var $pharmacist           = 'img/role/pharmacist.jpg';           //����
  var $pharmacist_success   = 'img/role/pharmacist_success.jpg';   //���դβ�������
  var $unconscious_list     = 'img/role/unconscious_list.jpg';     //̵�ռ��ΰ���
  var $cupid                = 'img/role/cupid.jpg';                //���塼�ԥå�
  var $cupid_pair           = 'img/role/cupid_pair.jpg';           //���塼�ԥåɤ���ӤĤ�������
  var $lovers_header        = 'img/role/lovers_header.jpg';        //����(��)
  var $lovers_footer        = 'img/role/lovers_footer.jpg';        //����(��)
  var $quiz                 = 'img/role/quiz.jpg';                 //�����
  var $authority            = 'img/role/authority.jpg';            //���ϼ�
  var $strong_voice         = 'img/role/strong_voice.jpg';         //����
  var $normal_voice         = 'img/role/normal_voice.jpg';         //�Դ���
  var $weak_voice           = 'img/role/weak_voice.jpg';           //����
  var $no_last_words        = 'img/role/no_last_words.jpg';        //ɮ����
  var $chicken              = 'img/role/chicken.jpg';              //������
  var $rabbit               = 'img/role/rabbit.jpg';               //������
  var $perverseness         = 'img/role/perverseness.jpg';         //ŷ�ٵ�
  //�ꤤ����ǽ�����Ƚ��
  var $result_human        = 'img/role/result_human.jpg';        //¼��
  var $result_wolf         = 'img/role/result_wolf.jpg';         //��ϵ
  var $result_boss_wolf    = 'img/role/result_boss_wolf.jpg';    //��ϵ
  var $result_poison_wolf  = 'img/role/result_poison_wolf.jpg';  //��ϵ
  var $result_tongue_wolf  = 'img/role/result_tongue_wolf.jpg';  //���ϵ
  var $result_mage         = 'img/role/result_mage.jpg';         //�ꤤ��
  var $result_soul_mage    = 'img/role/result_soul_mage.jpg';    //�����ꤤ��
  var $result_necromancer  = 'img/role/result_necromancer.jpg';  //��ǽ��
  var $result_medium       = 'img/role/result_medium.jpg';       //���
  var $result_mad          = 'img/role/result_mad.jpg';          //����
  var $result_fanatic_mad  = 'img/role/result_fanatic_mad.jpg';  //������
  var $result_guard        = 'img/role/result_guard.jpg';        //���
  var $result_poison_guard = 'img/role/result_poison_guard.jpg'; //����
  var $result_common       = 'img/role/result_common.jpg';       //��ͭ��
  var $result_fox          = 'img/role/result_fox.jpg';          //�Ÿ�
  var $result_child_fox    = 'img/role/result_child_fox.jpg';    //�Ҹ�
  var $result_poison       = 'img/role/result_poison.jpg';       //���Ǽ�
  var $result_pharmacist   = 'img/role/result_pharmacist.jpg';   //����
  var $result_suspect      = 'img/role/result_suspect.jpg';      //�Կ���
  var $result_unconscious  = 'img/role/result_unconscious.jpg';  //̵�ռ�
  var $result_cupid        = 'img/role/result_cupid.jpg';        //���塼�ԥå�
  var $result_lovers       = 'img/role/result_lovers.jpg';       //����
  var $result_quiz         = 'img/role/result_quiz.jpg';         //�����
  var $lost_ability        = 'img/role/lost_ability.jpg';        //ǽ�ϼ���
}

//�����رĤβ����ѥ�
class VictoryImage extends ImageManager{
  var $human  = 'img/victory_role/human.jpg';  //¼��
  var $wolf   = 'img/victory_role/wolf.jpg';   //��ϵ
  var $fox    = 'img/victory_role/fox.jpg';    //�Ÿ�
  var $lovers = 'img/victory_role/lovers.jpg'; //����
  var $draw   = 'img/victory_role/draw.jpg';   //����ʬ��
}

//�����ѥ�
class Sound{
  var $morning          = 'swf/sound_morning.swf';          //������
  var $revote           = 'swf/sound_revote.swf';           //����ɼ
  var $objection_male   = 'swf/sound_objection_male.swf';   //�۵Ĥ���(��)
  var $objection_female = 'swf/sound_objection_female.swf'; //�۵Ĥ���(��)
}
?>
