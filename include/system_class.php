<?php
//²èÁü´ÉÍı¥¯¥é¥¹¤Î´ğÄì¥¯¥é¥¹
class ImageManager{
  function GenerateTag($name, $alt = ''){
    $str = '<img';
    if($this->class != '') $str .= ' class="' . $this->class . '"';
    $str .= ' src="' . $this->path . '/' . $name . '.' . $this->extention . '"';
    if($alt != ''){
      EscapeStrings(&$alt);
      $str .= ' alt="' . $alt . '" title="' . $alt . '"';
    }
    return $str . '>';
  }
}

//Â¼¤Î¥ª¥×¥·¥ç¥ó²èÁü¾ğÊó
class RoomImage extends ImageManager{
  var $path      = 'img/room_option';
  var $extention = 'gif';
  var $class     = 'option';
  /*
  //Â¼¤ÎºÇÂç¿Í¿ô¥ê¥¹¥È (RoomConfig -> max_user_list ¤ÈÏ¢Æ°¤µ¤»¤ë)
  var $max_user_list = array(
			      8 => 'img/room_option/max8.gif',   // 8¿Í
			     16 => 'img/room_option/max16.gif',  //16¿Í
			     22 => 'img/room_option/max22.gif'   //22¿Í
			     );
  */
}
$ROOM_IMG = new RoomImage();

//Ìò¿¦¤Î²èÁü¾ğÊó
class RoleImage extends ImageManager{
  var $path      = 'img/role';
  var $extention = 'jpg';
  var $class     = '';

  function DisplayImage($name){
    echo $this->GenerateTag($name) . '<br>'."\n";
  }

  //Ìò¿¦¤ÎÀâÌÀ
  var $human                  = 'img/role/human.jpg';                  //Â¼¿Í
  var $wolf                   = 'img/role/wolf.jpg';                   //¿ÍÏµ
  var $boss_wolf              = 'img/role/boss_wolf.jpg';              //ÇòÏµ
  var $cursed_wolf            = 'img/role/cursed_wolf.jpg';            //¼öÏµ
  var $cute_wolf              = 'img/role/cute_wolf.jpg';              //Ë¨Ïµ
  var $poison_wolf            = 'img/role/poison_wolf.jpg';            //ÆÇÏµ
  var $resist_wolf            = 'img/role/resist_wolf.jpg';            //¹³ÆÇÏµ
  var $tongue_wolf            = 'img/role/tongue_wolf.jpg';            //Àå²ÒÏµ
  var $wolf_partner           = 'img/role/wolf_partner.jpg';           //¿ÍÏµ¤ÎÃç´Ö
  var $wolf_result            = 'img/role/wolf_result.jpg';            //³ú¤ß·ë²Ì
  var $mage                   = 'img/role/mage.jpg';                   //Àê¤¤»Õ
  var $soul_mage              = 'img/role/soul_mage.jpg';              //º²¤ÎÀê¤¤»Õ
  var $dummy_mage             = 'img/role/dummy_mage.jpg';             //Ì´¸«¿Í
  var $mage_result            = 'img/role/mage_result.jpg';            //Àê¤¤·ë²Ì
  var $necromancer            = 'img/role/necromancer.jpg';            //ÎîÇ½¼Ô
  var $necromancer_result     = 'img/role/necromancer_result.jpg';     //ÎîÇ½·ë²Ì
  var $soul_necromancer       = 'img/role/soul_necromancer.jpg';       //±À³°¶À
  var $medium                 = 'img/role/medium.jpg';                 //Öà½÷
  var $medium_result          = 'img/role/medium_result.jpg';          //Öà½÷·ë²Ì
  var $mad                    = 'img/role/mad.jpg';                    //¶¸¿Í
  var $fanatic_mad            = 'img/role/fanatic_mad.jpg';            //¶¸¿®¼Ô
  var $whisper_mad            = 'img/role/whisper_mad.jpg';            //Óñ¤­¶¸¿Í
  var $mad_partner            = 'img/role/mad_partner.jpg';            //Óñ¤­¶¸¿Í¤ÎÃç´Ö
  var $guard                  = 'img/role/guard.jpg';                  //¼í¿Í
  var $poison_guard           = 'img/role/poison_guard.jpg';           //µ³»Î
  var $guard_success          = 'img/role/guard_success.jpg';          //¼í¿Í¤Î¸î±ÒÀ®¸ù
  var $guard_hunted           = 'img/role/guard_hunted.jpg';           //¼í¿Í¤Î¼í¤êÀ®¸ù
  var $reporter               = 'img/role/reporter.jpg';               //¥Ö¥ó²°
  var $reporter_result_header = 'img/role/reporter_result_header.jpg'; //Ä¥¤ê¹ş¤ß·ë²Ì (Á°)
  var $reporter_result_footer = 'img/role/reporter_result_footer.jpg'; //Ä¥¤ê¹ş¤ß·ë²Ì (¸å)
  var $common                 = 'img/role/common.jpg';                 //¶¦Í­¼Ô
  var $common_partner         = 'img/role/common_partner.jpg';         //¶¦Í­¼Ô¤ÎÃç´Ö
  var $child_fox              = 'img/role/child_fox.jpg';              //»Ò¸Ñ
  var $cursed_fox             = 'img/role/cursed_fox.jpg';             //Å·¸Ñ
  var $fox                    = 'img/role/fox.jpg';                    //ÍÅ¸Ñ
  var $fox_partner            = 'img/role/fox_partner.jpg';            //ÍÅ¸Ñ¤ÎÃç´Ö
  var $fox_targeted           = 'img/role/fox_targeted.jpg';           //ÍÅ¸Ñ½±·â
  var $poison                 = 'img/role/poison.jpg';                 //ËäÆÇ¼Ô
  var $incubate_poison        = 'img/role/incubate_poison.jpg';        //ÀøÆÇ¼Ô
  var $ability_poison         = 'img/role/ability_poison.jpg';         //ÆÇÇ½ÎÏ
  var $poison_cat_success     = 'img/role/poison_cat_success.jpg';     //ËäÆÇ¼Ô
  var $poison_cat_failed      = 'img/role/poison_cat_failed.jpg';      //ËäÆÇ¼Ô
  var $pharmacist             = 'img/role/pharmacist.jpg';             //Ìô»Õ
  var $pharmacist_success     = 'img/role/pharmacist_success.jpg';     //Ìô»Õ¤Î²òÆÇÀ®¸ù
  var $unconscious_list       = 'img/role/unconscious_list.jpg';       //Ìµ°Õ¼±¤Î°ìÍ÷
  var $cupid                  = 'img/role/cupid.jpg';                  //¥­¥å¡¼¥Ô¥Ã¥É
  var $cupid_pair             = 'img/role/cupid_pair.jpg';             //¥­¥å¡¼¥Ô¥Ã¥É¤¬·ë¤Ó¤Ä¤±¤¿Îø¿Í
  var $lovers_header          = 'img/role/lovers_header.jpg';          //Îø¿Í(Á°)
  var $lovers_footer          = 'img/role/lovers_footer.jpg';          //Îø¿Í(¸å)
  var $quiz                   = 'img/role/quiz.jpg';                   //½ĞÂê¼Ô
  var $authority              = 'img/role/authority.jpg';              //¸¢ÎÏ¼Ô
  var $rebel                  = 'img/role/rebel.jpg';                  //È¿µÕ¼Ô
  var $random_voter           = 'img/role/random_voter.jpg';           //µ¤Ê¬²°
  var $watcher                = 'img/role/watcher.jpg';                //Ëµ´Ñ¼Ô
  var $upper_luck             = 'img/role/upper_luck.jpg';             //»¨Áğº²
  var $downer_luck            = 'img/role/downer_luck.jpg';            //°ìÈ¯²°
  var $random_luck            = 'img/role/random_luck.jpg';            //ÇÈÍğËü¾æ
  var $star                   = 'img/role/star.jpg';                   //¿Íµ¤¼Ô
  var $disfavor               = 'img/role/disfavor.jpg';               //ÉÔ¿Íµ¤
  var $strong_voice           = 'img/role/strong_voice.jpg';           //ÂçÀ¼
  var $normal_voice           = 'img/role/normal_voice.jpg';           //ÉÔ´ïÍÑ
  var $weak_voice             = 'img/role/weak_voice.jpg';             //¾®À¼
  var $upper_voice            = 'img/role/upper_voice.jpg';            //¥á¥¬¥Û¥ó
  var $downer_voice           = 'img/role/downer_voice.jpg';           //¥Ş¥¹¥¯
  var $random_voice           = 'img/role/random_voice.jpg';           //²²ÉÂ¼Ô
  var $no_last_words          = 'img/role/no_last_words.jpg';          //É®ÉÔÀº
  var $blinder                = 'img/role/blinder.jpg';                //ÌÜ±£¤·
  var $earplug                = 'img/role/earplug.jpg';                //¼ªÀò
  var $speaker                = 'img/role/speaker.jpg';                //¥¹¥Ô¡¼¥«¡¼
  var $silent                 = 'img/role/silent.jpg';                 //Ìµ¸ı
  var $liar                   = 'img/role/liar.jpg';                   //Ïµ¾¯Ç¯
  var $invisible              = 'img/role/invisible.jpg';              //¸÷³ØÌÂºÌ
  var $rainbow                = 'img/role/rainbow.jpg';                //Æú¿§ÌÂºÌ
  var $gentleman              = 'img/role/gentleman.jpg';              //¿Â»Î
  var $lady                   = 'img/role/lady.jpg';                   //½Ê½÷
  var $chicken                = 'img/role/chicken.jpg';                //¾®¿´¼Ô
  var $rabbit                 = 'img/role/rabbit.jpg';                 //¥¦¥µ¥®
  var $perverseness           = 'img/role/perverseness.jpg';           //Å·¼Ùµ´
  var $flattery               = 'img/role/flattery.jpg';               //¥´¥Ş¤¹¤ê
  var $impatience             = 'img/role/impatience.jpg';             //Ã»µ¤
  var $panelist               = 'img/role/panelist.jpg';               //²òÅú¼Ô

  //Àê¤¤¡¦ÎîÇ½¡¦Öà½÷È½Äê
  var $result_human             = 'img/role/result_human.jpg';              //Â¼¿Í
  var $result_wolf              = 'img/role/result_wolf.jpg';               //¿ÍÏµ
  var $result_boss_wolf         = 'img/role/result_boss_wolf.jpg';          //ÇòÏµ
  var $result_cursed_wolf       = 'img/role/result_cursed_wolf.jpg';        //¼öÏµ
  var $result_cute_wolf         = 'img/role/result_cute_wolf.jpg';          //Ë¨Ïµ
  var $result_poison_wolf       = 'img/role/result_poison_wolf.jpg';        //ÆÇÏµ
  var $result_resist_wolf       = 'img/role/result_resist_wolf.jpg';        //¹³ÆÇÏµ
  var $result_tongue_wolf       = 'img/role/result_tongue_wolf.jpg';        //Àå²ÒÏµ
  var $result_mage              = 'img/role/result_mage.jpg';               //Àê¤¤»Õ
  var $result_soul_mage         = 'img/role/result_soul_mage.jpg';          //º²¤ÎÀê¤¤»Õ
  var $result_dummy_mage        = 'img/role/result_dummy_mage.jpg';         //Ì´¸«¿Í
  var $result_necromancer       = 'img/role/result_necromancer.jpg';        //ÎîÇ½¼Ô
  var $result_soul_necromancer  = 'img/role/result_soul_necromancer.jpg';   //±À³°¶À
  var $result_dummy_necromancer = 'img/role/result_dummy_necromancer.jpg';  //Ì´Ëí¿Í
  var $result_medium            = 'img/role/result_medium.jpg';             //Öà½÷
  var $result_mad               = 'img/role/result_mad.jpg';                //¶¸¿Í
  var $result_fanatic_mad       = 'img/role/result_fanatic_mad.jpg';        //¶¸¿®¼Ô
  var $result_whisper_mad       = 'img/role/result_whisper_mad.jpg';        //Óñ¤­¶¸¿Í
  var $result_guard             = 'img/role/result_guard.jpg';              //¼í¿Í
  var $result_poison_guard      = 'img/role/result_poison_guard.jpg';       //µ³»Î
  var $result_dummy_guard       = 'img/role/result_dummy_guard.jpg';        //Ì´¼é¿Í
  var $result_reporter          = 'img/role/result_reporter.jpg';           //¥Ö¥ó²°
  var $result_common            = 'img/role/result_common.jpg';             //¶¦Í­¼Ô
  var $result_dummy_common      = 'img/role/result_dummy_common.jpg';       //Ì´¶¦Í­¼Ô
  var $result_fox               = 'img/role/result_fox.jpg';                //ÍÅ¸Ñ
  var $result_child_fox         = 'img/role/result_child_fox.jpg';          //»Ò¸Ñ
  var $result_cursed_fox        = 'img/role/result_cursed_fox.jpg';         //Å·¸Ñ
  var $result_poison            = 'img/role/result_poison.jpg';             //ËäÆÇ¼Ô
  var $result_strong_poison     = 'img/role/result_strong_poison.jpg';      //¶¯ÆÇ¼Ô
  var $result_incubate_poison   = 'img/role/result_incubate_poison.jpg';    //ÀøÆÇ¼Ô
  var $result_dummy_poison      = 'img/role/result_dummy_poison.jpg';       //Ì´ÆÇ¼Ô
  var $result_mania             = 'img/role/result_mania.jpg';              //¿ÀÏÃ¥Ş¥Ë¥¢
  var $result_pharmacist        = 'img/role/result_pharmacist.jpg';         //Ìô»Õ
  var $result_suspect           = 'img/role/result_suspect.jpg';            //ÉÔ¿³¼Ô
  var $result_unconscious       = 'img/role/result_unconscious.jpg';        //Ìµ°Õ¼±
  var $result_cupid             = 'img/role/result_cupid.jpg';              //¥­¥å¡¼¥Ô¥Ã¥É
  var $result_lovers            = 'img/role/result_lovers.jpg';             //Îø¿Í
  var $result_quiz              = 'img/role/result_quiz.jpg';               //½ĞÂê¼Ô
  var $lost_ability             = 'img/role/lost_ability.jpg';              //Ç½ÎÏ¼º¸ú
}

//¾¡Íø¿Ø±Ä¤Î²èÁü¾ğÊó
class VictoryImage extends ImageManager{
  var $path      = 'img/victory_role';
  var $extention = 'jpg';
  var $class     = 'winner';

  function MakeVictoryImage($victory_role){
    $name = $victory_role;
    switch($victory_role){
    case 'human':
      $alt = 'Â¼¿Í¾¡Íø';
      break;

    case 'wolf':
      $alt = '¿ÍÏµ¾¡Íø';
      break;

    case 'fox1':
    case 'fox2':
      $name = 'fox';
      $alt = 'ÍÅ¸Ñ¾¡Íø';
      break;

    case 'lovers':
      $alt = 'Îø¿Í¾¡Íø';
      break;

    case 'quiz':
      $alt = '½ĞÂê¼Ô¾¡Íø';
      break;

    case 'draw':
    case 'vanish':
    case 'quiz_dead':
      $name = 'draw';
      $alt = '°ú¤­Ê¬¤±';
      break;

    default:
      return '-';
      break;
    }
    return $this->GenerateTag($name, $alt);
  }
}
$VICTORY_IMG = new VictoryImage();

//²»¸»¥Ñ¥¹
class Sound{
  var $morning          = 'swf/sound_morning.swf';          //ÌëÌÀ¤±
  var $revote           = 'swf/sound_revote.swf';           //ºÆÅêÉ¼
  var $objection_male   = 'swf/sound_objection_male.swf';   //°ÛµÄ¤¢¤ê(ÃË)
  var $objection_female = 'swf/sound_objection_female.swf'; //°ÛµÄ¤¢¤ê(½÷)
}
$SOUND = new Sound();
?>
