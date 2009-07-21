<?php
// ²èÁü´ÉÍý¥¯¥é¥¹¤Î´ðÄì¥¯¥é¥¹¤òÄêµÁ¤·¤Þ¤¹¡£
class ImageManager{
  function GenerateTag($name, $alt, $class='icon'){
    $alt = htmlspecialchars($alt, ENT_QUOTES);
    $class = htmlspecialchars($class, ENT_QUOTES);
    return "<img class=\"$class\" src=\"{$this->$name}\" alt=\"$alt\" title=\"$alt\">";
  }
}

//Â¼¤Î¥ª¥×¥·¥ç¥ó²èÁü¥Ñ¥¹
class RoomImage extends ImageManager{
  var $waiting = 'img/room_option/waiting.gif'; //Â¼¥ê¥¹¥È¤ÎÊç½¸Ãæ¤Î²èÁü
  var $playing = 'img/room_option/playing.gif'; //Â¼¥ê¥¹¥È¤Î¥²¡¼¥àÃæ¤Î²èÁü

  var $wish_role       = 'img/room_option/wish_role.gif';       //Ìò³ä´õË¾À©
  var $real_time       = 'img/room_option/real_time.gif';       //Ìò³ä´õË¾À©
  var $dummy_boy       = 'img/room_option/dummy_boy.gif';       //¿ÈÂå¤ï¤ê·¯»ÈÍÑ
  var $open_vote       = 'img/room_option/open_vote.gif';       //É¼¿ô¸ø³«
  var $not_open_cast   = 'img/room_option/not_open_cast.gif';   //ÇÛÌòÈó¸ø³«
  var $decide          = 'img/room_option/decide.gif';          //·èÄê¼Ô
  var $authority       = 'img/room_option/authority.gif';       //¸¢ÎÏ¼Ô
  var $poison          = 'img/room_option/poison.gif';          //ËäÆÇ¼Ô
  var $cupid           = 'img/room_option/cupid.gif';           //¥­¥å¡¼¥Ô¥Ã¥É
  var $boss_wolf       = 'img/room_option/boss_wolf.gif';       //ÇòÏµ
  var $poison_wolf     = 'img/room_option/poison_wolf.gif';     //ÆÇÏµ
  var $mania           = 'img/room_option/mania.gif';           //¿ÀÏÃ¥Þ¥Ë¥¢
  var $medium          = 'img/room_option/medium.gif';          //Öà½÷
  var $liar            = 'img/room_option/liar.gif';            //Ïµ¾¯Ç¯
  var $gentleman       = 'img/room_option/gentleman.gif';       //¿Â»Î¡¦½Ê½÷
  var $sudden_death    = 'img/room_option/sudden_death.gif';    //µõ¼åÂÎ¼Á
  var $chaos           = 'img/room_option/chaos.gif';           //°ÇÆé
  var $chaosfull       = 'img/room_option/chaosfull.gif';       //¿¿¡¦°ÇÆé
  var $chaos_open_cast = 'img/room_option/chaos_open_cast.gif'; //ÇÛÌò¸ø³«
  var $secret_sub_role = 'img/room_option/secret_sub_role.gif'; //¥µ¥ÖÌò¿¦ÈóÉ½¼¨
  var $no_sub_role     = 'img/room_option/no_sub_role.gif';     //¥µ¥ÖÌò¿¦Ìµ¤·

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
  var $human                  = 'img/role/human.jpg';                  //Â¼¿Í
  var $wolf                   = 'img/role/wolf.jpg';                   //¿ÍÏµ
  var $boss_wolf              = 'img/role/boss_wolf.jpg';              //ÇòÏµ
  var $poison_wolf            = 'img/role/poison_wolf.jpg';            //ÆÇÏµ
  var $tongue_wolf            = 'img/role/tongue_wolf.jpg';            //Àå²ÒÏµ
  var $cute_wolf              = 'img/role/cute_wolf.jpg';              //Ë¨Ïµ
  var $wolf_partner           = 'img/role/wolf_partner.jpg';           //¿ÍÏµ¤ÎÃç´Ö
  var $wolf_result            = 'img/role/wolf_result.jpg';            //³ú¤ß·ë²Ì
  var $mage                   = 'img/role/mage.jpg';                   //Àê¤¤»Õ
  var $soul_mage              = 'img/role/soul_mage.jpg';              //º²¤ÎÀê¤¤»Õ
  var $dummy_mage             = 'img/role/dummy_mage.jpg';             //Ì´¸«¿Í
  var $mage_result            = 'img/role/mage_result.jpg';            //Àê¤¤·ë²Ì
  var $necromancer            = 'img/role/necromancer.jpg';            //ÎîÇ½¼Ô
  var $necromancer_result     = 'img/role/necromancer_result.jpg';     //ÎîÇ½·ë²Ì
  var $medium                 = 'img/role/medium.jpg';                 //Öà½÷
  var $medium_result          = 'img/role/medium_result.jpg';          //Öà½÷·ë²Ì
  var $mad                    = 'img/role/mad.jpg';                    //¶¸¿Í
  var $fanatic_mad            = 'img/role/fanatic_mad.jpg';            //¶¸¿®¼Ô
  var $guard                  = 'img/role/guard.jpg';                  //¼í¿Í
  var $poison_guard           = 'img/role/poison_guard.jpg';           //µ³»Î
  var $guard_success          = 'img/role/guard_success.jpg';          //¼í¿Í¤Î¸î±ÒÀ®¸ù
  var $reporter               = 'img/role/reporter.jpg';               //¥Ö¥ó²°
  var $reporter_result_header = 'img/role/reporter_result_header.jpg'; //Ä¥¤ê¹þ¤ß·ë²Ì (Á°)
  var $reporter_result_footer = 'img/role/reporter_result_footer.jpg'; //Ä¥¤ê¹þ¤ß·ë²Ì (¸å)
  var $common                 = 'img/role/common.jpg';                 //¶¦Í­¼Ô
  var $common_partner         = 'img/role/common_partner.jpg';         //¶¦Í­¼Ô¤ÎÃç´Ö
  var $child_fox              = 'img/role/child_fox.jpg';              //»Ò¸Ñ
  var $fox                    = 'img/role/fox.jpg';                    //ÍÅ¸Ñ
  var $fox_partner            = 'img/role/fox_partner.jpg';            //ÍÅ¸Ñ¤ÎÃç´Ö
  var $fox_target             = 'img/role/fox_targeted.jpg';           //ÍÅ¸Ñ½±·â
  var $poison                 = 'img/role/poison.jpg';                 //ËäÆÇ¼Ô
  var $poison_cat_success     = 'img/role/poison_cat_success.jpg';     //ËäÆÇ¼Ô
  var $poison_cat_failed      = 'img/role/poison_cat_failed.jpg';      //ËäÆÇ¼Ô
  var $pharmacist             = 'img/role/pharmacist.jpg';             //Ìô»Õ
  var $pharmacist_success     = 'img/role/pharmacist_success.jpg';     //Ìô»Õ¤Î²òÆÇÀ®¸ù
  var $unconscious_list       = 'img/role/unconscious_list.jpg';       //Ìµ°Õ¼±¤Î°ìÍ÷
  var $cupid                  = 'img/role/cupid.jpg';                  //¥­¥å¡¼¥Ô¥Ã¥É
  var $cupid_pair             = 'img/role/cupid_pair.jpg';             //¥­¥å¡¼¥Ô¥Ã¥É¤¬·ë¤Ó¤Ä¤±¤¿Îø¿Í
  var $lovers_header          = 'img/role/lovers_header.jpg';          //Îø¿Í(Á°)
  var $lovers_footer          = 'img/role/lovers_footer.jpg';          //Îø¿Í(¸å)
  var $quiz                   = 'img/role/quiz.jpg';                   //½ÐÂê¼Ô
  var $authority              = 'img/role/authority.jpg';              //¸¢ÎÏ¼Ô
  var $rebel                  = 'img/role/rebel.jpg';                  //È¿µÕ¼Ô
  var $random_voter           = 'img/role/random_voter.jpg';           //µ¤Ê¬²°
  var $watcher                = 'img/role/watcher.jpg';                //Ëµ´Ñ¼Ô
  var $upper_luck             = 'img/role/upper_luck.jpg';             //»¨Áðº²
  var $downer_luck            = 'img/role/downer_luck.jpg';            //°ìÈ¯²°
  var $random_luck            = 'img/role/random_luck.jpg';            //ÇÈÍðËü¾æ
  var $strong_voice           = 'img/role/strong_voice.jpg';           //ÂçÀ¼
  var $star                   = 'img/role/star.jpg';                   //¿Íµ¤¼Ô
  var $disfavor               = 'img/role/disfavor.jpg';               //ÉÔ¿Íµ¤
  var $normal_voice           = 'img/role/normal_voice.jpg';           //ÉÔ´ïÍÑ
  var $weak_voice             = 'img/role/weak_voice.jpg';             //¾®À¼
  var $random_voice           = 'img/role/random_voice.jpg';           //²²ÉÂ¼Ô
  var $no_last_words          = 'img/role/no_last_words.jpg';          //É®ÉÔÀº
  var $blinder                = 'img/role/blinder.jpg';                //ÌÜ±£¤·
  var $earplug                = 'img/role/earplug.jpg';                //¼ªÀò
  var $silent                 = 'img/role/silent.jpg';                 //Ìµ¸ý
  var $liar                   = 'img/role/liar.jpg';                   //Ïµ¾¯Ç¯
  var $invisible              = 'img/role/invisible.jpg';              //¸÷³ØÌÂºÌ
  var $gentleman              = 'img/role/gentleman.jpg';              //¿Â»Î
  var $lady                   = 'img/role/lady.jpg';                   //½Ê½÷
  var $chicken                = 'img/role/chicken.jpg';                //¾®¿´¼Ô
  var $rabbit                 = 'img/role/rabbit.jpg';                 //¥¦¥µ¥®
  var $perverseness           = 'img/role/perverseness.jpg';           //Å·¼Ùµ´
  var $flattery               = 'img/role/flattery.jpg';               //¥´¥Þ¤¹¤ê
  var $impatience             = 'img/role/impatience.jpg';             //Ã»µ¤

  //Àê¤¤¡¦ÎîÇ½¡¦Öà½÷È½Äê
  var $result_human        = 'img/role/result_human.jpg';        //Â¼¿Í
  var $result_wolf         = 'img/role/result_wolf.jpg';         //¿ÍÏµ
  var $result_boss_wolf    = 'img/role/result_boss_wolf.jpg';    //ÇòÏµ
  var $result_poison_wolf  = 'img/role/result_poison_wolf.jpg';  //ÆÇÏµ
  var $result_tongue_wolf  = 'img/role/result_tongue_wolf.jpg';  //Àå²ÒÏµ
  var $result_cute_wolf    = 'img/role/result_cute_wolf.jpg';    //Ë¨Ïµ
  var $result_mage         = 'img/role/result_mage.jpg';         //Àê¤¤»Õ
  var $result_soul_mage    = 'img/role/result_soul_mage.jpg';    //º²¤ÎÀê¤¤»Õ
  var $result_reporter     = 'img/role/result_reporter.jpg';     //¥Ö¥ó²°
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
  var $result_mania        = 'img/role/result_mania.jpg';        //¿ÀÏÃ¥Þ¥Ë¥¢
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
