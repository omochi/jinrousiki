<?php
//画像管理クラスの基底クラス
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

//村のオプション画像情報
class RoomImage extends ImageManager{
  var $path      = 'img/room_option';
  var $extention = 'gif';
  var $class     = 'option';
  /*
  //村の最大人数リスト (RoomConfig -> max_user_list と連動させる)
  var $max_user_list = array(
			      8 => 'img/room_option/max8.gif',   // 8人
			     16 => 'img/room_option/max16.gif',  //16人
			     22 => 'img/room_option/max22.gif'   //22人
			     );
  */
}
$ROOM_IMG = new RoomImage();

//役職の画像情報
class RoleImage extends ImageManager{
  var $path      = 'img/role';
  var $extention = 'jpg';
  var $class     = '';

  function DisplayImage($name){
    echo $this->GenerateTag($name) . '<br>'."\n";
  }

  //役職の説明
  var $human                  = 'img/role/human.jpg';                  //村人
  var $wolf                   = 'img/role/wolf.jpg';                   //人狼
  var $boss_wolf              = 'img/role/boss_wolf.jpg';              //白狼
  var $cursed_wolf            = 'img/role/cursed_wolf.jpg';            //呪狼
  var $cute_wolf              = 'img/role/cute_wolf.jpg';              //萌狼
  var $poison_wolf            = 'img/role/poison_wolf.jpg';            //毒狼
  var $resist_wolf            = 'img/role/resist_wolf.jpg';            //抗毒狼
  var $tongue_wolf            = 'img/role/tongue_wolf.jpg';            //舌禍狼
  var $wolf_partner           = 'img/role/wolf_partner.jpg';           //人狼の仲間
  var $wolf_result            = 'img/role/wolf_result.jpg';            //噛み結果
  var $mage                   = 'img/role/mage.jpg';                   //占い師
  var $soul_mage              = 'img/role/soul_mage.jpg';              //魂の占い師
  var $dummy_mage             = 'img/role/dummy_mage.jpg';             //夢見人
  var $mage_result            = 'img/role/mage_result.jpg';            //占い結果
  var $necromancer            = 'img/role/necromancer.jpg';            //霊能者
  var $necromancer_result     = 'img/role/necromancer_result.jpg';     //霊能結果
  var $soul_necromancer       = 'img/role/soul_necromancer.jpg';       //雲外鏡
  var $medium                 = 'img/role/medium.jpg';                 //巫女
  var $medium_result          = 'img/role/medium_result.jpg';          //巫女結果
  var $mad                    = 'img/role/mad.jpg';                    //狂人
  var $fanatic_mad            = 'img/role/fanatic_mad.jpg';            //狂信者
  var $whisper_mad            = 'img/role/whisper_mad.jpg';            //囁き狂人
  var $mad_partner            = 'img/role/mad_partner.jpg';            //囁き狂人の仲間
  var $guard                  = 'img/role/guard.jpg';                  //狩人
  var $poison_guard           = 'img/role/poison_guard.jpg';           //騎士
  var $guard_success          = 'img/role/guard_success.jpg';          //狩人の護衛成功
  var $guard_hunted           = 'img/role/guard_hunted.jpg';           //狩人の狩り成功
  var $reporter               = 'img/role/reporter.jpg';               //ブン屋
  var $reporter_result_header = 'img/role/reporter_result_header.jpg'; //張り込み結果 (前)
  var $reporter_result_footer = 'img/role/reporter_result_footer.jpg'; //張り込み結果 (後)
  var $common                 = 'img/role/common.jpg';                 //共有者
  var $common_partner         = 'img/role/common_partner.jpg';         //共有者の仲間
  var $child_fox              = 'img/role/child_fox.jpg';              //子狐
  var $cursed_fox             = 'img/role/cursed_fox.jpg';             //天狐
  var $fox                    = 'img/role/fox.jpg';                    //妖狐
  var $fox_partner            = 'img/role/fox_partner.jpg';            //妖狐の仲間
  var $fox_targeted           = 'img/role/fox_targeted.jpg';           //妖狐襲撃
  var $poison                 = 'img/role/poison.jpg';                 //埋毒者
  var $incubate_poison        = 'img/role/incubate_poison.jpg';        //潜毒者
  var $ability_poison         = 'img/role/ability_poison.jpg';         //毒能力
  var $poison_cat_success     = 'img/role/poison_cat_success.jpg';     //埋毒者
  var $poison_cat_failed      = 'img/role/poison_cat_failed.jpg';      //埋毒者
  var $pharmacist             = 'img/role/pharmacist.jpg';             //薬師
  var $pharmacist_success     = 'img/role/pharmacist_success.jpg';     //薬師の解毒成功
  var $unconscious_list       = 'img/role/unconscious_list.jpg';       //無意識の一覧
  var $cupid                  = 'img/role/cupid.jpg';                  //キューピッド
  var $cupid_pair             = 'img/role/cupid_pair.jpg';             //キューピッドが結びつけた恋人
  var $lovers_header          = 'img/role/lovers_header.jpg';          //恋人(前)
  var $lovers_footer          = 'img/role/lovers_footer.jpg';          //恋人(後)
  var $quiz                   = 'img/role/quiz.jpg';                   //出題者
  var $authority              = 'img/role/authority.jpg';              //権力者
  var $rebel                  = 'img/role/rebel.jpg';                  //反逆者
  var $random_voter           = 'img/role/random_voter.jpg';           //気分屋
  var $watcher                = 'img/role/watcher.jpg';                //傍観者
  var $upper_luck             = 'img/role/upper_luck.jpg';             //雑草魂
  var $downer_luck            = 'img/role/downer_luck.jpg';            //一発屋
  var $random_luck            = 'img/role/random_luck.jpg';            //波乱万丈
  var $star                   = 'img/role/star.jpg';                   //人気者
  var $disfavor               = 'img/role/disfavor.jpg';               //不人気
  var $strong_voice           = 'img/role/strong_voice.jpg';           //大声
  var $normal_voice           = 'img/role/normal_voice.jpg';           //不器用
  var $weak_voice             = 'img/role/weak_voice.jpg';             //小声
  var $upper_voice            = 'img/role/upper_voice.jpg';            //メガホン
  var $downer_voice           = 'img/role/downer_voice.jpg';           //マスク
  var $random_voice           = 'img/role/random_voice.jpg';           //臆病者
  var $no_last_words          = 'img/role/no_last_words.jpg';          //筆不精
  var $blinder                = 'img/role/blinder.jpg';                //目隠し
  var $earplug                = 'img/role/earplug.jpg';                //耳栓
  var $speaker                = 'img/role/speaker.jpg';                //スピーカー
  var $silent                 = 'img/role/silent.jpg';                 //無口
  var $liar                   = 'img/role/liar.jpg';                   //狼少年
  var $invisible              = 'img/role/invisible.jpg';              //光学迷彩
  var $rainbow                = 'img/role/rainbow.jpg';                //虹色迷彩
  var $gentleman              = 'img/role/gentleman.jpg';              //紳士
  var $lady                   = 'img/role/lady.jpg';                   //淑女
  var $chicken                = 'img/role/chicken.jpg';                //小心者
  var $rabbit                 = 'img/role/rabbit.jpg';                 //ウサギ
  var $perverseness           = 'img/role/perverseness.jpg';           //天邪鬼
  var $flattery               = 'img/role/flattery.jpg';               //ゴマすり
  var $impatience             = 'img/role/impatience.jpg';             //短気
  var $panelist               = 'img/role/panelist.jpg';               //解答者

  //占い・霊能・巫女判定
  var $result_human             = 'img/role/result_human.jpg';              //村人
  var $result_wolf              = 'img/role/result_wolf.jpg';               //人狼
  var $result_boss_wolf         = 'img/role/result_boss_wolf.jpg';          //白狼
  var $result_cursed_wolf       = 'img/role/result_cursed_wolf.jpg';        //呪狼
  var $result_cute_wolf         = 'img/role/result_cute_wolf.jpg';          //萌狼
  var $result_poison_wolf       = 'img/role/result_poison_wolf.jpg';        //毒狼
  var $result_resist_wolf       = 'img/role/result_resist_wolf.jpg';        //抗毒狼
  var $result_tongue_wolf       = 'img/role/result_tongue_wolf.jpg';        //舌禍狼
  var $result_mage              = 'img/role/result_mage.jpg';               //占い師
  var $result_soul_mage         = 'img/role/result_soul_mage.jpg';          //魂の占い師
  var $result_dummy_mage        = 'img/role/result_dummy_mage.jpg';         //夢見人
  var $result_necromancer       = 'img/role/result_necromancer.jpg';        //霊能者
  var $result_soul_necromancer  = 'img/role/result_soul_necromancer.jpg';   //雲外鏡
  var $result_dummy_necromancer = 'img/role/result_dummy_necromancer.jpg';  //夢枕人
  var $result_medium            = 'img/role/result_medium.jpg';             //巫女
  var $result_mad               = 'img/role/result_mad.jpg';                //狂人
  var $result_fanatic_mad       = 'img/role/result_fanatic_mad.jpg';        //狂信者
  var $result_whisper_mad       = 'img/role/result_whisper_mad.jpg';        //囁き狂人
  var $result_guard             = 'img/role/result_guard.jpg';              //狩人
  var $result_poison_guard      = 'img/role/result_poison_guard.jpg';       //騎士
  var $result_dummy_guard       = 'img/role/result_dummy_guard.jpg';        //夢守人
  var $result_reporter          = 'img/role/result_reporter.jpg';           //ブン屋
  var $result_common            = 'img/role/result_common.jpg';             //共有者
  var $result_dummy_common      = 'img/role/result_dummy_common.jpg';       //夢共有者
  var $result_fox               = 'img/role/result_fox.jpg';                //妖狐
  var $result_child_fox         = 'img/role/result_child_fox.jpg';          //子狐
  var $result_cursed_fox        = 'img/role/result_cursed_fox.jpg';         //天狐
  var $result_poison            = 'img/role/result_poison.jpg';             //埋毒者
  var $result_strong_poison     = 'img/role/result_strong_poison.jpg';      //強毒者
  var $result_incubate_poison   = 'img/role/result_incubate_poison.jpg';    //潜毒者
  var $result_dummy_poison      = 'img/role/result_dummy_poison.jpg';       //夢毒者
  var $result_mania             = 'img/role/result_mania.jpg';              //神話マニア
  var $result_pharmacist        = 'img/role/result_pharmacist.jpg';         //薬師
  var $result_suspect           = 'img/role/result_suspect.jpg';            //不審者
  var $result_unconscious       = 'img/role/result_unconscious.jpg';        //無意識
  var $result_cupid             = 'img/role/result_cupid.jpg';              //キューピッド
  var $result_lovers            = 'img/role/result_lovers.jpg';             //恋人
  var $result_quiz              = 'img/role/result_quiz.jpg';               //出題者
  var $lost_ability             = 'img/role/lost_ability.jpg';              //能力失効
}

//勝利陣営の画像情報
class VictoryImage extends ImageManager{
  var $path      = 'img/victory_role';
  var $extention = 'jpg';
  var $class     = 'winner';

  function MakeVictoryImage($victory_role){
    $name = $victory_role;
    switch($victory_role){
    case 'human':
      $alt = '村人勝利';
      break;

    case 'wolf':
      $alt = '人狼勝利';
      break;

    case 'fox1':
    case 'fox2':
      $name = 'fox';
      $alt = '妖狐勝利';
      break;

    case 'lovers':
      $alt = '恋人勝利';
      break;

    case 'quiz':
      $alt = '出題者勝利';
      break;

    case 'draw':
    case 'vanish':
    case 'quiz_dead':
      $name = 'draw';
      $alt = '引き分け';
      break;

    default:
      return '-';
      break;
    }
    return $this->GenerateTag($name, $alt);
  }
}
$VICTORY_IMG = new VictoryImage();

//音源パス
class Sound{
  var $morning          = 'swf/sound_morning.swf';          //夜明け
  var $revote           = 'swf/sound_revote.swf';           //再投票
  var $objection_male   = 'swf/sound_objection_male.swf';   //異議あり(男)
  var $objection_female = 'swf/sound_objection_female.swf'; //異議あり(女)
}
$SOUND = new Sound();
?>
