<?php
// 画像管理クラスの基底クラスを定義します。
class ImageManager{
  function GenerateTag($name, $alt, $class='icon'){
    $alt = htmlspecialchars($alt, ENT_QUOTES);
    $class = htmlspecialchars($class, ENT_QUOTES);
    return "<img class='$class' src='{$this->$name}' alt='$alt' title='$alt'>";
  }
}

//村のオプション画像パス
class RoomImage extends ImageManager{
  var $waiting = 'img/room_option/waiting.gif'; //村リストの募集中の画像
  var $playing = 'img/room_option/playing.gif'; //村リストのゲーム中の画像

  var $wish_role     = 'img/room_option/wish_role.gif';     //役割希望制
  var $real_time     = 'img/room_option/real_time.gif';     //役割希望制
  var $dummy_boy     = 'img/room_option/dummy_boy.gif';     //身代わり君使用
  var $open_vote     = 'img/room_option/open_vote.gif';     //票数公開
  var $not_open_cast = 'img/room_option/not_open_cast.gif'; //配役非公開
  var $decide        = 'img/room_option/decide.gif';        //決定者
  var $authority     = 'img/room_option/authority.gif';     //権力者
  var $poison        = 'img/room_option/poison.gif';        //埋毒者
  var $cupid         = 'img/room_option/cupid.gif';         //キューピッド
  var $chaos         = 'img/room_option/chaos.gif';         //闇鍋
  var $chaosfull     = 'img/room_option/chaosfull.gif';     //真・闇鍋

  //村の最大人数リスト (RoomConfig -> max_user_list と連動させる)
  var $max_user_list = array(
			      8 => 'img/room_option/max8.gif',   // 8人
			     16 => 'img/room_option/max16.gif',  //16人
			     22 => 'img/room_option/max22.gif'   //22人
			     );
}

//役職の画像パス
class RoleImage extends ImageManager{
  //役職の説明
  var $human              = 'img/role/human.jpg';              //村人
  var $wolf               = 'img/role/wolf.jpg';               //人狼
  var $boss_wolf          = 'img/role/boss_wolf.jpg';          //白狼
  var $wolf_partner       = 'img/role/wolf_partner.jpg';       //人狼の仲間
  var $mage               = 'img/role/mage.jpg';               //占い師
  var $soul_mage          = 'img/role/soul_mage.jpg';          //魂の占い師
  var $mage_result        = 'img/role/mage_result.jpg';        //占い結果
  var $necromancer        = 'img/role/necromancer.jpg';        //霊能者
  var $medium             = 'img/role/medium.jpg';             //巫女
  var $necromancer_result = 'img/role/necromancer_result.jpg'; //霊能結果
  var $mad                = 'img/role/mad.jpg';                //狂人
  var $fanatic_mad        = 'img/role/fanatic_mad.jpg';        //狂信者
  var $guard              = 'img/role/guard.jpg';              //狩人
  var $poison_guard       = 'img/role/poison_guard.jpg';       //騎士
  var $guard_success      = 'img/role/guard_success.jpg';      //狩人の護衛成功
  var $common             = 'img/role/common.jpg';             //共有者
  var $common_partner     = 'img/role/common_partner.jpg';     //共有者の仲間
  var $fox                = 'img/role/fox.jpg';                //妖狐
  var $fox_partner        = 'img/role/fox_partner.jpg';        //妖狐の仲間
  var $fox_target         = 'img/role/fox_targeted.jpg';       //妖狐が狙われた
  var $poison             = 'img/role/poison.jpg';             //埋毒者
  var $cupid              = 'img/role/cupid.jpg';              //キューピッド
  var $cupid_pair         = 'img/role/cupid_pair.jpg';         //キューピッドが結びつけた恋人
  var $lovers_header      = 'img/role/lovers_header.jpg';      //恋人(前)
  var $lovers_footer      = 'img/role/lovers_footer.jpg';      //恋人(後)
  var $quiz               = 'img/role/quiz.jpg';               //出題者
  var $authority          = 'img/role/authority.jpg';          //権力者
  var $strong_voice       = 'img/role/strong_voice.jpg';       //大声
  var $normal_voice       = 'img/role/normal_voice.jpg';       //不器用
  var $weak_voice         = 'img/role/weak_voice.jpg';         //小声
  var $no_last_words      = 'img/role/no_last_words.jpg';      //筆不精
  var $chicken            = 'img/role/chicken.jpg';            //小心者
  var $rabbit             = 'img/role/rabbit.jpg';             //ウサギ
  var $perverseness       = 'img/role/perverseness.jpg';       //天邪鬼
  //占い・霊能・巫女判定
  var $result_human        = 'img/role/result_human.jpg';        //村人
  var $result_wolf         = 'img/role/result_wolf.jpg';         //人狼
  var $result_boss_wolf    = 'img/role/result_boss_wolf.jpg';    //白狼
  var $result_mage         = 'img/role/result_mage.jpg';         //占い師
  var $result_soul_mage    = 'img/role/result_soul_mage.jpg';    //魂の占い師
  var $result_necromancer  = 'img/role/result_necromancer.jpg';  //霊能者
  var $result_medium       = 'img/role/result_medium.jpg';       //巫女
  var $result_mad          = 'img/role/result_mad.jpg';          //狂人
  var $result_fanatic_mad  = 'img/role/result_fanatic_mad.jpg';  //狂信者
  var $result_guard        = 'img/role/result_guard.jpg';        //狩人
  var $result_poison_guard = 'img/role/result_poison_guard.jpg'; //騎士
  var $result_common       = 'img/role/result_common.jpg';       //共有者
  var $result_fox          = 'img/role/result_fox.jpg';          //妖狐
  var $result_child_fox    = 'img/role/result_child_fox.jpg';    //子狐
  var $result_poison       = 'img/role/result_poison.jpg';       //埋毒者
  var $result_cupid        = 'img/role/result_cupid.jpg';        //キューピッド
  var $result_quiz         = 'img/role/result_quiz.jpg';         //出題者
}

//勝利陣営の画像パス
class VictoryImage extends ImageManager{
  var $human  = 'img/victory_role/human.jpg';  //村人
  var $wolf   = 'img/victory_role/wolf.jpg';   //人狼
  var $fox    = 'img/victory_role/fox.jpg';    //妖狐
  var $lovers = 'img/victory_role/lovers.jpg'; //恋人
  var $draw   = 'img/victory_role/draw.jpg';   //引き分け
}

//音源パス
class Sound{
  var $morning          = 'swf/sound_morning.swf';          //夜明け
  var $revote           = 'swf/sound_revote.swf';           //再投票
  var $objection_male   = 'swf/sound_objection_male.swf';   //異議あり(男)
  var $objection_female = 'swf/sound_objection_female.swf'; //異議あり(女)
}
?>
