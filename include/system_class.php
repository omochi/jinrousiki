<?php
// 画像管理クラスの基底クラスを定義します。
class ImageManager{
  function GenerateTag($name, $alt, $class='icon'){
    $alt = htmlspecialchars($alt, ENT_QUOTES);
    $class = htmlspecialchars($class, ENT_QUOTES);
    return "<img class=\"$class\" src=\"{$this->$name}\" alt=\"$alt\" title=\"$alt\">";
  }
}

//村のオプション画像パス
class RoomImage extends ImageManager{
  var $waiting = 'img/room_option/waiting.gif'; //村リストの募集中の画像
  var $playing = 'img/room_option/playing.gif'; //村リストのゲーム中の画像

  var $wish_role       = 'img/room_option/wish_role.gif';       //役割希望制
  var $real_time       = 'img/room_option/real_time.gif';       //役割希望制
  var $dummy_boy       = 'img/room_option/dummy_boy.gif';       //身代わり君使用
  var $open_vote       = 'img/room_option/open_vote.gif';       //票数公開
  var $not_open_cast   = 'img/room_option/not_open_cast.gif';   //配役非公開
  var $decide          = 'img/room_option/decide.gif';          //決定者
  var $authority       = 'img/room_option/authority.gif';       //権力者
  var $poison          = 'img/room_option/poison.gif';          //埋毒者
  var $cupid           = 'img/room_option/cupid.gif';           //キューピッド
  var $boss_wolf       = 'img/room_option/boss_wolf.gif';       //白狼
  var $poison_wolf     = 'img/room_option/poison_wolf.gif';     //毒狼
  var $mania           = 'img/room_option/mania.gif';           //神話マニア
  var $medium          = 'img/room_option/medium.gif';          //巫女
  var $liar            = 'img/room_option/liar.gif';            //狼少年
  var $gentleman       = 'img/room_option/gentleman.gif';       //紳士・淑女
  var $sudden_death    = 'img/room_option/sudden_death.gif';    //虚弱体質
  var $chaos           = 'img/room_option/chaos.gif';           //闇鍋
  var $chaosfull       = 'img/room_option/chaosfull.gif';       //真・闇鍋
  var $chaos_open_cast = 'img/room_option/chaos_open_cast.gif'; //配役公開
  var $secret_sub_role = 'img/room_option/secret_sub_role.gif'; //サブ役職非表示
  var $no_sub_role     = 'img/room_option/no_sub_role.gif';     //サブ役職無し

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
  var $human                  = 'img/role/human.jpg';                  //村人
  var $wolf                   = 'img/role/wolf.jpg';                   //人狼
  var $boss_wolf              = 'img/role/boss_wolf.jpg';              //白狼
  var $poison_wolf            = 'img/role/poison_wolf.jpg';            //毒狼
  var $tongue_wolf            = 'img/role/tongue_wolf.jpg';            //舌禍狼
  var $cute_wolf              = 'img/role/cute_wolf.jpg';              //萌狼
  var $wolf_partner           = 'img/role/wolf_partner.jpg';           //人狼の仲間
  var $wolf_result            = 'img/role/wolf_result.jpg';            //噛み結果
  var $mage                   = 'img/role/mage.jpg';                   //占い師
  var $soul_mage              = 'img/role/soul_mage.jpg';              //魂の占い師
  var $dummy_mage             = 'img/role/dummy_mage.jpg';             //夢見人
  var $mage_result            = 'img/role/mage_result.jpg';            //占い結果
  var $necromancer            = 'img/role/necromancer.jpg';            //霊能者
  var $necromancer_result     = 'img/role/necromancer_result.jpg';     //霊能結果
  var $medium                 = 'img/role/medium.jpg';                 //巫女
  var $medium_result          = 'img/role/medium_result.jpg';          //巫女結果
  var $mad                    = 'img/role/mad.jpg';                    //狂人
  var $fanatic_mad            = 'img/role/fanatic_mad.jpg';            //狂信者
  var $guard                  = 'img/role/guard.jpg';                  //狩人
  var $poison_guard           = 'img/role/poison_guard.jpg';           //騎士
  var $guard_success          = 'img/role/guard_success.jpg';          //狩人の護衛成功
  var $reporter               = 'img/role/reporter.jpg';               //ブン屋
  var $reporter_result_header = 'img/role/reporter_result_header.jpg'; //張り込み結果 (前)
  var $reporter_result_footer = 'img/role/reporter_result_footer.jpg'; //張り込み結果 (後)
  var $common                 = 'img/role/common.jpg';                 //共有者
  var $common_partner         = 'img/role/common_partner.jpg';         //共有者の仲間
  var $child_fox              = 'img/role/child_fox.jpg';              //子狐
  var $fox                    = 'img/role/fox.jpg';                    //妖狐
  var $fox_partner            = 'img/role/fox_partner.jpg';            //妖狐の仲間
  var $fox_target             = 'img/role/fox_targeted.jpg';           //妖狐襲撃
  var $poison                 = 'img/role/poison.jpg';                 //埋毒者
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
  var $strong_voice           = 'img/role/strong_voice.jpg';           //大声
  var $star                   = 'img/role/star.jpg';                   //人気者
  var $disfavor               = 'img/role/disfavor.jpg';               //不人気
  var $normal_voice           = 'img/role/normal_voice.jpg';           //不器用
  var $weak_voice             = 'img/role/weak_voice.jpg';             //小声
  var $random_voice           = 'img/role/random_voice.jpg';           //臆病者
  var $no_last_words          = 'img/role/no_last_words.jpg';          //筆不精
  var $blinder                = 'img/role/blinder.jpg';                //目隠し
  var $earplug                = 'img/role/earplug.jpg';                //耳栓
  var $silent                 = 'img/role/silent.jpg';                 //無口
  var $liar                   = 'img/role/liar.jpg';                   //狼少年
  var $invisible              = 'img/role/invisible.jpg';              //光学迷彩
  var $gentleman              = 'img/role/gentleman.jpg';              //紳士
  var $lady                   = 'img/role/lady.jpg';                   //淑女
  var $chicken                = 'img/role/chicken.jpg';                //小心者
  var $rabbit                 = 'img/role/rabbit.jpg';                 //ウサギ
  var $perverseness           = 'img/role/perverseness.jpg';           //天邪鬼
  var $flattery               = 'img/role/flattery.jpg';               //ゴマすり
  var $impatience             = 'img/role/impatience.jpg';             //短気

  //占い・霊能・巫女判定
  var $result_human        = 'img/role/result_human.jpg';        //村人
  var $result_wolf         = 'img/role/result_wolf.jpg';         //人狼
  var $result_boss_wolf    = 'img/role/result_boss_wolf.jpg';    //白狼
  var $result_poison_wolf  = 'img/role/result_poison_wolf.jpg';  //毒狼
  var $result_tongue_wolf  = 'img/role/result_tongue_wolf.jpg';  //舌禍狼
  var $result_cute_wolf    = 'img/role/result_cute_wolf.jpg';    //萌狼
  var $result_mage         = 'img/role/result_mage.jpg';         //占い師
  var $result_soul_mage    = 'img/role/result_soul_mage.jpg';    //魂の占い師
  var $result_reporter     = 'img/role/result_reporter.jpg';     //ブン屋
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
  var $result_mania        = 'img/role/result_mania.jpg';        //神話マニア
  var $result_pharmacist   = 'img/role/result_pharmacist.jpg';   //薬師
  var $result_suspect      = 'img/role/result_suspect.jpg';      //不審者
  var $result_unconscious  = 'img/role/result_unconscious.jpg';  //無意識
  var $result_cupid        = 'img/role/result_cupid.jpg';        //キューピッド
  var $result_lovers       = 'img/role/result_lovers.jpg';       //恋人
  var $result_quiz         = 'img/role/result_quiz.jpg';         //出題者
  var $lost_ability        = 'img/role/lost_ability.jpg';        //能力失効
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
