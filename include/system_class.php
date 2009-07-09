<?php
// ²èÁü´ÉÍý¥¯¥é¥¹¤Î´ðÄì¥¯¥é¥¹¤òÄêµÁ¤·¤Þ¤¹¡£
class ImageManager{
  function GenerateTag($name, $alt, $class='icon'){
    $alt = htmlspecialchars($alt, ENT_QUOTES);
    $class = htmlspecialchars($class, ENT_QUOTES);
    return "<img class='$class' src='{$this->$name}' alt='$alt' title='$alt'>";
  }
}

//Â¼¤Î¥ª¥×¥·¥ç¥ó²èÁü¥Ñ¥¹
class RoomImage extends ImageManager{
  var $waiting = 'img/room_option/waiting.gif'; //Â¼¥ê¥¹¥È¤ÎÊç½¸Ãæ¤Î²èÁü
  var $playing = 'img/room_option/playing.gif'; //Â¼¥ê¥¹¥È¤Î¥²¡¼¥àÃæ¤Î²èÁü

  var $wish_role     = 'img/room_option/wish_role.gif';     //Ìò³ä´õË¾À©
  var $real_time     = 'img/room_option/real_time.gif';     //Ìò³ä´õË¾À©
  var $dummy_boy     = 'img/room_option/dummy_boy.gif';     //¿ÈÂå¤ï¤ê·¯»ÈÍÑ
  var $open_vote     = 'img/room_option/open_vote.gif';     //É¼¿ô¸ø³«
  var $not_open_cast = 'img/room_option/not_open_cast.gif'; //ÇÛÌòÈó¸ø³«
  var $decide        = 'img/room_option/decide.gif';        //·èÄê¼Ô
  var $authority     = 'img/room_option/authority.gif';     //¸¢ÎÏ¼Ô
  var $poison        = 'img/room_option/poison.gif';        //ËäÆÇ¼Ô
  var $cupid         = 'img/room_option/cupid.gif';         //¥­¥å¡¼¥Ô¥Ã¥É
  var $chaos         = 'img/room_option/chaos.gif';         //°ÇÆé
  var $chaosfull     = 'img/room_option/chaosfull.gif';     //¿¿¡¦°ÇÆé

  //Â¼¤ÎºÇÂç¿Í¿ô¥ê¥¹¥È (RoomConfig -> max_user_list ¤ÈÏ¢Æ°¤µ¤»¤ë)
  var $max_user_list = array(
			      8 => 'img/room_option/max8.gif',   // 8¿Í
			     16 => 'img/room_option/max16.gif',  //16¿Í
			     22 => 'img/room_option/max22.gif'   //22¿Í
			     );
}

//Ìò¿¦¤Î²èÁü¥Ñ¥¹
class RoleImage extends ImageManager{
  //Ìò¿¦¤ÎÀâÌÀ
  var $human                = 'img/role/human.jpg';                //Â¼¿Í
  var $wolf                 = 'img/role/wolf.jpg';                 //¿ÍÏµ
  var $boss_wolf            = 'img/role/boss_wolf.jpg';            //ÇòÏµ
  var $poison_wolf          = 'img/role/poison_wolf.jpg';          //ÆÇÏµ
  var $tongue_wolf          = 'img/role/tongue_wolf.jpg';          //Àå²ÒÏµ
  var $wolf_partner         = 'img/role/wolf_partner.jpg';         //¿ÍÏµ¤ÎÃç´Ö
  var $wolf_result          = 'img/role/wolf_result.jpg';          //³ú¤ß·ë²Ì
  var $mage                 = 'img/role/mage.jpg';                 //Àê¤¤»Õ
  var $soul_mage            = 'img/role/soul_mage.jpg';            //º²¤ÎÀê¤¤»Õ
  var $mage_result          = 'img/role/mage_result.jpg';          //Àê¤¤·ë²Ì
  var $necromancer          = 'img/role/necromancer.jpg';          //ÎîÇ½¼Ô
  var $necromancer_result   = 'img/role/necromancer_result.jpg';   //ÎîÇ½·ë²Ì
  var $medium               = 'img/role/medium.jpg';               //Öà½÷
  var $medium_result        = 'img/role/medium_result.jpg';        //Öà½÷·ë²Ì
  var $mad                  = 'img/role/mad.jpg';                  //¶¸¿Í
  var $fanatic_mad          = 'img/role/fanatic_mad.jpg';          //¶¸¿®¼Ô
  var $guard                = 'img/role/guard.jpg';                //¼í¿Í
  var $poison_guard         = 'img/role/poison_guard.jpg';         //µ³»Î
  var $guard_success        = 'img/role/guard_success.jpg';        //¼í¿Í¤Î¸î±ÒÀ®¸ù
  var $common               = 'img/role/common.jpg';               //¶¦Í­¼Ô
  var $common_partner       = 'img/role/common_partner.jpg';       //¶¦Í­¼Ô¤ÎÃç´Ö
  var $child_fox            = 'img/role/child_fox.jpg';            //»Ò¸Ñ
  var $fox                  = 'img/role/fox.jpg';                  //ÍÅ¸Ñ
  var $fox_partner          = 'img/role/fox_partner.jpg';          //ÍÅ¸Ñ¤ÎÃç´Ö
  var $fox_target           = 'img/role/fox_targeted.jpg';         //ÍÅ¸Ñ½±·â
  var $poison               = 'img/role/poison.jpg';               //ËäÆÇ¼Ô
  var $pharmacist           = 'img/role/pharmacist.jpg';           //Ìô»Õ
  var $pharmacist_success   = 'img/role/pharmacist_success.jpg';   //Ìô»Õ¤Î²òÆÇÀ®¸ù
  var $unconscious_list     = 'img/role/unconscious_list.jpg';     //Ìµ°Õ¼±¤Î°ìÍ÷
  var $cupid                = 'img/role/cupid.jpg';                //¥­¥å¡¼¥Ô¥Ã¥É
  var $cupid_pair           = 'img/role/cupid_pair.jpg';           //¥­¥å¡¼¥Ô¥Ã¥É¤¬·ë¤Ó¤Ä¤±¤¿Îø¿Í
  var $lovers_header        = 'img/role/lovers_header.jpg';        //Îø¿Í(Á°)
  var $lovers_footer        = 'img/role/lovers_footer.jpg';        //Îø¿Í(¸å)
  var $quiz                 = 'img/role/quiz.jpg';                 //½ÐÂê¼Ô
  var $authority            = 'img/role/authority.jpg';            //¸¢ÎÏ¼Ô
  var $strong_voice         = 'img/role/strong_voice.jpg';         //ÂçÀ¼
  var $normal_voice         = 'img/role/normal_voice.jpg';         //ÉÔ´ïÍÑ
  var $weak_voice           = 'img/role/weak_voice.jpg';           //¾®À¼
  var $no_last_words        = 'img/role/no_last_words.jpg';        //É®ÉÔÀº
  var $chicken              = 'img/role/chicken.jpg';              //¾®¿´¼Ô
  var $rabbit               = 'img/role/rabbit.jpg';               //¥¦¥µ¥®
  var $perverseness         = 'img/role/perverseness.jpg';         //Å·¼Ùµ´
  //Àê¤¤¡¦ÎîÇ½¡¦Öà½÷È½Äê
  var $result_human        = 'img/role/result_human.jpg';        //Â¼¿Í
  var $result_wolf         = 'img/role/result_wolf.jpg';         //¿ÍÏµ
  var $result_boss_wolf    = 'img/role/result_boss_wolf.jpg';    //ÇòÏµ
  var $result_poison_wolf  = 'img/role/result_poison_wolf.jpg';  //ÆÇÏµ
  var $result_tongue_wolf  = 'img/role/result_tongue_wolf.jpg';  //Àå²ÒÏµ
  var $result_mage         = 'img/role/result_mage.jpg';         //Àê¤¤»Õ
  var $result_soul_mage    = 'img/role/result_soul_mage.jpg';    //º²¤ÎÀê¤¤»Õ
  var $result_necromancer  = 'img/role/result_necromancer.jpg';  //ÎîÇ½¼Ô
  var $result_medium       = 'img/role/result_medium.jpg';       //Öà½÷
  var $result_mad          = 'img/role/result_mad.jpg';          //¶¸¿Í
  var $result_fanatic_mad  = 'img/role/result_fanatic_mad.jpg';  //¶¸¿®¼Ô
  var $result_guard        = 'img/role/result_guard.jpg';        //¼í¿Í
  var $result_poison_guard = 'img/role/result_poison_guard.jpg'; //µ³»Î
  var $result_common       = 'img/role/result_common.jpg';       //¶¦Í­¼Ô
  var $result_fox          = 'img/role/result_fox.jpg';          //ÍÅ¸Ñ
  var $result_child_fox    = 'img/role/result_child_fox.jpg';    //»Ò¸Ñ
  var $result_poison       = 'img/role/result_poison.jpg';       //ËäÆÇ¼Ô
  var $result_pharmacist   = 'img/role/result_pharmacist.jpg';   //Ìô»Õ
  var $result_suspect      = 'img/role/result_suspect.jpg';      //ÉÔ¿³¼Ô
  var $result_unconscious  = 'img/role/result_unconscious.jpg';  //Ìµ°Õ¼±
  var $result_cupid        = 'img/role/result_cupid.jpg';        //¥­¥å¡¼¥Ô¥Ã¥É
  var $result_lovers       = 'img/role/result_lovers.jpg';       //Îø¿Í
  var $result_quiz         = 'img/role/result_quiz.jpg';         //½ÐÂê¼Ô
  var $lost_ability        = 'img/role/lost_ability.jpg';        //Ç½ÎÏ¼º¸ú
}

//¾¡Íø¿Ø±Ä¤Î²èÁü¥Ñ¥¹
class VictoryImage extends ImageManager{
  var $human  = 'img/victory_role/human.jpg';  //Â¼¿Í
  var $wolf   = 'img/victory_role/wolf.jpg';   //¿ÍÏµ
  var $fox    = 'img/victory_role/fox.jpg';    //ÍÅ¸Ñ
  var $lovers = 'img/victory_role/lovers.jpg'; //Îø¿Í
  var $draw   = 'img/victory_role/draw.jpg';   //°ú¤­Ê¬¤±
}

//²»¸»¥Ñ¥¹
class Sound{
  var $morning          = 'swf/sound_morning.swf';          //ÌëÌÀ¤±
  var $revote           = 'swf/sound_revote.swf';           //ºÆÅêÉ¼
  var $objection_male   = 'swf/sound_objection_male.swf';   //°ÛµÄ¤¢¤ê(ÃË)
  var $objection_female = 'swf/sound_objection_female.swf'; //°ÛµÄ¤¢¤ê(½÷)
}
?>
