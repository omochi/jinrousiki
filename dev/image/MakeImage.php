<?php
require_once('MessageImageGenerator2.php');
$font_name = 'azuki.ttf';
#$font_name = 'yutaCo2_ttc_027.ttc';
#$font_name = 'aquafont.ttf';

$gen = new MessageImageGenerator("C:\\WINDOWS\\Fonts\\" . $font_name, 10, 3, 3, false);

function MakeImage1($generator, $class){
  return $generator->GetImage($class['message'], $class['R'], $class['G'], $class['B'],
			      $class['R2'], $class['G2'], $class['B2'],
			      $class['R3'], $class['G3'], $class['B3'],
			      $class['R4'], $class['G4'], $class['B4']);
}

function MakeImage($generator, $class){
  foreach($class['delimiter'] as $delimiter => $colors){
    $generator->AddDelimiter(new Delimiter($delimiter, $colors['R'], $colors['G'], $colors['B']));
  }
  return $generator->GetImage($class['message']);
}

class MainRoleList{
  var $human = array(
    'message' => "[役割] [#村人#陣営] [|村人|系]\n　あなたは|村人|です。特殊な能力はありませんが、あなたの知恵と勇気で村を救えるはずです。",
    'R' => 0, 'G' => 0, 'B' => 0, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $human_new = array(
    'message' => "[役割] [#村人#陣営] [|村人|系]\n　あなたは|村人|です。特殊な能力はありませんが、あなたの知恵と勇気で村を救えるはずです。",
    'delimiter' => array('|' => array('R' => 96, 'G' => 96, 'B' => 96),
			 '#' => array('R' => 255, 'G' => 0, 'B')));

  var $mage = array(
    'message' => "[役割] [村人陣営] [|占い師|系]\n　あなたは|占い師|、夜の間に村人一人を占うことで翌朝その人が「人」か「#狼#」か知ることができます。あなたが村人の勝利を握っています。",
    'R' => 153, 'G' => 51, 'B' => 255, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $soul_mage = array(
    'message' => "[役割] [村人陣営] [|占い師|系]\n　あなたは|魂の占い師|、役職を知ることができる##|占い師|です。自らの運命をも、その魂で切り開くことができるはずです。",
    'R' => 153, 'G' => 51, 'B' => 255, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $psycho_mage = array(
    'message' => "[役割] [村人陣営] [|占い師|系]\n　あなたは|精神鑑定士|、心理を図ることができる##|占い師|です。#嘘つき#||や#夢#を見ている人を探し出して村の混乱を収めるのです！",
    'R' => 153, 'G' => 51, 'B' => 255, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $sex_mage = array(
    'message' => "[役割] [#村人#陣営] [|占い師|系]\n　あなたは|ひよこ鑑定士|、性別が分かる##|占い師|です。特にメリットはありませんが楽しんでください。",
    'R' => 153, 'G' => 51, 'B' => 255, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $voodoo_killer = array(
    'message' => "[役割] [#村人#陣営] [|占い師|系]\n　あなたは|陰陽師|です。夜の間に村人一人を占うことでその人の呪いを祓うことができます。\n　呪われた人外を呪返しで祓い、村を清めるのです！",
    'R' => 153, 'G' => 51, 'B' => 255, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $necromancer = array(
    'message' => "[役割] [村人陣営] [|霊能者|系]\n　あなたは|霊能者|、その日の処刑者が「人」か「#狼#」か翌日の朝に知ることができます。\n　地味ですがあなたの努力次第で大きく貢献することも不可能ではありません。",
    'R' => 0, 'G' => 102, 'B' => 153, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $soul_necromancer = array(
    'message' => "[役割] [#村人#陣営] [|霊能者|系]\n　あなたは|雲外鏡|、役職を知ることができる##|霊能者|です。全てを見抜くその鏡で処刑者の正体を暴くのです！",
    'R' => 0, 'G' => 102, 'B' => 153, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $yama_necromancer = array(
    'message' => "[役割] [#村人#陣営] [|霊能者|系]\n　あなたは|閻魔|です。前日の死者の死因を知ることができます。死者の内訳に白黒はっきりつけてやりましょう！",
    'R' => 0, 'G' => 102, 'B' => 153, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $medium = array(
    'message' => "[役割] [#村人#陣営] [|霊能者|系]\n　あなたは|巫女|、突然死した人の所属陣営を知ることができます。不慮の死を遂げた人の正体を知らせ、村の推理に貢献するのです！",
    'R' => 0, 'G' => 102, 'B' => 153, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $priest = array(
    'message' => "[役割] [#村人#陣営] [|司祭|系]\n　あなたは|司祭|です。一定日数おきに現在生きている村人陣営の総数を知ることができます。",
    'R' => 0, 'G' => 102, 'B' => 153, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $revive_priest = array(
    'message' => "[役割] [|村人|陣営] [#司祭#系]\n　あなたは#天人#です。初日に一度天に帰って下界の様子を眺める事になります。\n　後で颯爽と降臨して華麗に村を勝利に導きましょう。",
    'delimiter' => array('|' => array('R' => 96, 'G' => 96, 'B' => 96),
			 '#' => array('R' => 0, 'G' => 102, 'B' => 153)));

  var $guard = array(
    'message' => "[役割] [村人陣営] [|狩人|系]\n　あなたは|狩人|です。夜の間に村人一人を#人狼#から護ることができます。||#人狼#のココロを読むのです。",
    'R' => 51, 'G' => 153, 'B' => 255, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $poison_guard = array(
    'message' => "[役割] [村人陣営] [|狩人|系]\n　あなたは|騎士|です。夜の間に村人一人を#人狼#から護ることができます。もし、あなたが||#人狼#に襲われたら刺し違えてでも倒すのです！",
    'R' => 51, 'G' => 153, 'B' => 255, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $reporter = array(
    'message' => "[役割] [村人陣営] [|狩人|系]\n　あなたは|ブン屋|、尾行した人が襲撃されたらスクープを入手できます。#人狼#や_妖狐_に気づかれないよう、慎重かつ大胆に行動するのです！",
    'R' => 51, 'G' => 153, 'B' => 255, 'R2' => 255, 'G2' => 0, 'B2' => 0,
    'R3' => 204, 'G3' => 0, 'B3' => 153);

  var $anti_voodoo = array(
    'message' => "[役割] [村人陣営] [|狩人|系]\n　あなたは|厄神|です。夜の間に村人一人の災厄を祓うことができます。\n　呪いから#占い師#を護り、村を浄化するのです！",
    'R' => 51, 'G' => 153, 'B' => 255, 'R2' => 153, 'G2' => 51, 'B2' => 255);

  var $common = array(
    'message' => "[役割] [#村人#陣営] [|共有者|系]\n　あなたは|共有者|、もう一人の##|共有者|が誰であるか知ることができます。生存期間が他と比べ永い能力です。\n　あなたは推理する時間が与えられたのです。悩みなさい！",
    'R' => 204, 'G' => 102, 'B' => 51, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $poison = array(
    'message' => "[役割] [村人陣営] [|埋毒者|系]\n　あなたは|埋毒者|です。#人狼#に襲われた場合は||#人狼#の中から、処刑された場合は生きている村の人たちの中からランダムで一人道連れにします。",
    'R' => 0, 'G' => 153, 'B' => 102, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $incubate_poison = array(
    'message' => "[役割] [#村人#陣営] [|埋毒者|系]\n　あなたは|潜毒者|です。時が経つにつれてあなたの体内に秘められた毒が顕在化してきます。まずは時間を稼ぐのです。",
    'R' => 0, 'G' => 153, 'B' => 102, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $poison_cat = array(
    'message' => "[役割] [|村人|陣営] [#猫又#系]\n　あなたは#猫又#、_毒_をもっています。また、死んだ人を誰か一人蘇らせる事ができます。",
    'delimiter' => array('|' => array('R' => 96, 'G' => 96, 'B' => 96),
			 '#' => array('R' => 0, 'G' => 153, 'B' => 102),
			 '_' => array('R' => 0, 'G' => 153, 'B' => 102)));

  var $revive_cat = array(
    'message' => "[役割] [|村人|陣営] [#猫又#系]\n　あなたは#仙狸#です。死んだ人を誰か一人蘇らせる事ができます。\n　何回でも蘇生できますが、成功するたびに成功率が大幅に下がるので計画的に行動しましょう。",
    'delimiter' => array('|' => array('R' => 96, 'G' => 96, 'B' => 96),
			 '#' => array('R' => 0, 'G' => 153, 'B' => 102)));

  var $pharmacist = array(
    'message' => "[役割] [#村人#陣営] [|薬師|系]\n　あなたは|薬師|です。前日に投票した人が毒を持っているか翌朝に知ることができます。\n　また、投票した人が処刑された場合はその人を無毒化させることができます。村人への二次被害を未然に防ぐのです！",
    'R' => 0, 'G' => 153, 'B' => 102, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $assassin = array(
    'message' => "[役割] [#村人#陣営] [|暗殺者|系]\n　あなたは|暗殺者|です。夜に村人一人を暗殺することができます。",
    'R' => 144, 'G' => 64, 'B' => 64, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $mind_scanner = array(
    'message' => "[役割] [#村人#陣営] [|さとり|系]\n　あなたは|さとり|です。誰か一人の心を読むことができます。心を読んだ結果を活かして村を勝利に導きましょう。",
    'R' => 160, 'G' => 160, 'B' => 0, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $evoke_scanner = array(
    'message' => "[役割] [|村人|陣営] [#さとり#系]\n　あなたは#イタコ#です。誰か一人の心を#口寄せ#を介して読むことができます。\n　霊界からの情報を活かして村を勝利に導きましょう。",
    'delimiter' => array('|' => array('R' => 96, 'G' => 96, 'B' => 96),
			 '#' => array('R' => 160, 'G' => 160, 'B' => 0)));

  var $mind_scanner_target = array(
    'message' => "あなたが心を読んでいるのは以下の人たちです： ", 'R' => 0, 'G' => 0, 'B' => 0, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $mind_friend_list = array(
    'message' => "あなたと共鳴しているのは以下の人たちです： ", 'R' => 0, 'G' => 0, 'B' => 0, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $jealousy = array(
    'message' => "[役割] [村人陣営] [|橋姫|系]\n　あなたは|橋姫|です。あなたに投票してきた#恋人#たちに別れの種を植え付けることができます。\n　妬みの力で#恋人#たちを滅ぼすのです。",
    'R' => 0, 'G' => 204, 'B' => 0, 'R2' => 255, 'G2' => 51, 'B2' => 153);

  var $wolf = array(
    'message' => "[役割] [#人狼#陣営] [|人狼|系]\n　あなたは|人狼|です。夜の間に他の#人狼#と協力し村人一人を殺害できます。あなたはその強力な力で村人を喰い殺すのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $boss_wolf = array(
    'message' => "[役割] [|人狼|##陣営] [|人狼|系]\n　あなたは|白狼|です。もう#占い師#を恐れる必要はありません。全てを欺き通して村人たちを皆殺しにするのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 153, 'G2' => 51, 'B2' => 255);

  var $tongue_wolf = array(
    'message' => "[役割] [#人狼#陣営] [|人狼|系]\n　あなたは|舌禍狼|、噛んだ人の役職を知ることができます。ただし、村人を噛んだらその力を失ってしまいます。",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $wise_wolf = array(
    'message' => "[役割] [|人狼|##陣営] [|人狼|系]\n　あなたは|賢狼|です。##|人狼|の宿敵である#妖狐#の存在を感知することができます。",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 204, 'G2' => 0, 'B2' => 153);

  var $cursed_wolf = array(
    'message' => "[役割] [|人狼|##陣営] [|人狼|系]\n　あなたは|呪狼|です。あなたを占った#占い師#を呪い殺すことができます。村を絶望の底に叩き落してやるのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 153, 'G2' => 51, 'B2' => 255);

  var $possessed_wolf = array(
    'message' => "[役割] [|人狼|##陣営] [|人狼|系]\n　あなたは|憑狼|です。噛んだ人に憑依することができます。",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 0, 'G2' => 153, 'B2' => 102);

  var $poison_wolf = array(
    'message' => "[役割] [|人狼|##陣営] [|人狼|系]\n　あなたは|毒狼|です。たとえ処刑されても体内に流れる#猛毒#で村人一人を道連れにできます。強気に村をかく乱するのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 0, 'G2' => 153, 'B2' => 102);

  var $resist_wolf = array(
    'message' => "[役割] [|人狼|##陣営] [|人狼|系]\n　あなたは|抗毒狼|、一度だけ#毒#に耐えることができます。強気に村人を食らい尽くすのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 0, 'G2' => 153, 'B2' => 102);

  var $cute_wolf = array(
    'message' => "[役割] [#人狼#陣営] [|人狼|系]\n　あなたは|萌狼|です。ごくまれに発言が遠吠えになってしまいます。バレた時は笑ってごまかしましょう。",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $scarlet_wolf = array(
    'message' => "[役割] [|人狼|##陣営] [|人狼|系]\n　あなたは|紅狼|です。#妖狐#にはあなたが仲間であるように見えています。\n　これをどう活かすかはあなた次第です。",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 204, 'G2' => 0, 'B2' => 153,
    'R3' => 96, 'G3' => 96, 'B3' => 96);

  var $silver_wolf = array(
    'message' => "[役割] [#人狼#陣営] [|人狼|系]\n　あなたは|銀狼|、孤高の狼です。仲間が誰か分かりませんが、空気を読んで上手く立ち回りましょう。",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $mad = array(
    'message' => "[役割] [#人狼#陣営] [|狂人|系]\n　あなたは|狂人|です。#人狼#の勝利があなたの勝利となります。あなたはできる限り狂って場をかき乱すのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $fanatic_mad = array(
    'message' => "[役割] [#人狼#陣営] [|狂人|系]\n　あなたは|狂信者|です。仕えるべき#人狼#が誰なのかを知ることができます。あなたの持てる全てを||#人狼#に捧げ尽くすのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $whisper_mad = array(
    'message' => "[役割] [#人狼#陣営] [|狂人|系]\n　あなたは|囁き狂人|です。夜の#人狼#の相談に参加することができます。||#人狼#と完璧な連携を組んで村を殲滅するのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $jammer_mad = array(
    'message' => "[役割] [|人狼|##陣営] [|狂人|系]\n　あなたは|月兎|です。夜の間に誰か一人の占いを妨害することができます。\n　#占い師#を混乱させることで村から理性を奪ってやるのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 153, 'G2' => 51, 'B2' => 255);

  var $voodoo_mad = array(
    'message' => "[役割] [|人狼|##陣営] [|狂人|系]\n　あなたは|呪術師|です。夜の間に誰か一人に呪いをかけることができます。#占い師#を呪いで殲滅し、村を混乱に陥れるのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 153, 'G2' => 51, 'B2' => 255);

  var $corpse_courier_mad = array(
    'message' => "[役割] [|人狼|##陣営] [|狂人|系]\n　あなたは|火車|です。あなたが投票した人が処刑された場合に限り、その死体を持ち去ることができます。\n　#霊能者#の仕事を妨害して|人狼|の勝利に貢献するのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 0, 'G2' => 102, 'B2' => 153);

  var $dream_eater_mad = array(
    'message' => "[役割] [|人狼|##陣営] [|狂人|系]\n　あなたは|獏|です。夜の間に村人一人の夢を食べることで夢能力者を殺すことができます。\n　天敵の#夢守人#に注意しながら、村から夢も希望も奪ってやるのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 51, 'G2' => 153, 'B2' => 255);

  var $trap_mad = array(
    'message' => "[役割] [|人狼|##陣営] [|狂人|系]\n　あなたは|罠師|です。一度だけ夜に罠を仕掛けることができます。罠を仕掛けた人の元に訪れた能力者は全員死亡します。\n　#人狼#の襲撃先をかわしつつ_狩人_の護衛先を読みきるのです！",
    'R' => 255, 'G' => 0, 'B' => 0, 'R2' => 255, 'G2' => 0, 'B2' => 0,
    'R3' => 51, 'G3' => 153, 'B3' => 255);

  var $fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|妖狐|系]\n　あなたは|妖狐|、#人狼#に殺されることはありません。ただし占われると死んでしまいます。\n　村人を騙し、#人狼#を騙し、村を|妖狐|のものにするのです！",
    'R' => 204, 'G' => 0, 'B' => 153, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $white_fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|妖狐|系]\n　あなたは|白狐|です。_占い師_を騙すことができますが、^霊能者^には見抜かれてしまいます。\n　また、#人狼#に襲われると殺されてしまいます。巧みに||#人狼#を欺くのです。",
    'R' => 204, 'G' => 0, 'B' => 153,  'R2' => 255, 'G2' => 0, 'B2' => 0,
    'R3' => 153, 'G3' => 51, 'B3' => 255, 'R4' => 0, 'G4' => 102, 'B4' => 153);

  var $black_fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|妖狐|系]\n　あなたは|黒狐|です。占われても呪殺されませんが、#人狼#判定が出されるうえに、^霊能者^には見抜かれてしまいます。",
    'R' => 204, 'G' => 0, 'B' => 153,  'R2' => 255, 'G2' => 0, 'B2' => 0,
    'R3' => 153, 'G3' => 51, 'B3' => 255, 'R4' => 0, 'G4' => 102, 'B4' => 153);

  var $poison_fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|妖狐|系]\n　あなたは|管狐|、#毒#を持っています。あなたを亡き者にしようとする人に禍をもたらすのです！",
    'R' => 204, 'G' => 0, 'B' => 153, 'R2' => 0, 'G2' => 153, 'B2' => 102);

  var $voodoo_fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|妖狐|系]\n　あなたは|九尾|です。夜の間に誰か一人に呪いをかけることができます。##|妖狐|の天敵、#占い師#を呪返しで葬るのです！",
    'R' => 204, 'G' => 0, 'B' => 153, 'R2' => 153, 'G2' => 51, 'B2' => 255);

  var $revive_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|仙狐|です。死んだ人を誰か一人蘇らせる事ができます。\n　確実に成功しますが一度しかできないのでじっくり機をうかがいましょう。",
    'delimiter' => array('|' => array('R' => 204, 'G' => 0, 'B' => 153)));

  var $cursed_fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|妖狐|系]\n　あなたは|天狐|です。##|妖狐|の天敵、_占い師_を呪返しで殺すことができます。#人狼#の襲撃すら跳ね返すことができますが、^狩人^には殺されてしまいます。",
    'R' => 204, 'G' => 0, 'B' => 153, 'R2' => 255, 'G2' => 0, 'B2' => 0,
    'R3' => 153, 'G3' => 51, 'B3' => 255, 'R4' => 51, 'G4' => 153, 'B4' => 255);

  var $scarlet_fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|妖狐|系]\n　あなたは|紅狐|です。#人狼#にはあなたが_無意識_であるように見えています。\n　これをどう活かすかはあなた次第です。",
    'R' => 204, 'G' => 0, 'B' => 153, 'R2' => 255, 'G2' => 0, 'B2' => 0,
    'R3' => 96, 'G3' => 96, 'B3' => 96);

  var $cute_fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|妖狐|系]\n　あなたは|萌狐|です。ごくまれに発言が遠吠えになってしまいます。バレた時は笑ってごまかしましょう。",
    'R' => 204, 'G' => 0, 'B' => 153, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $silver_fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|妖狐|系]\n　あなたは|銀狐|です。仲間が誰か分かりませんが、元来##|妖狐|は孤高の存在です。頑張ってください。",
    'R' => 204, 'G' => 0, 'B' => 153, 'R2' => 204, 'G2' => 0, 'B2' => 153);

  var $child_fox = array(
    'message' => "[役割] [|妖狐|##陣営] [|子狐|系]\n　あなたは|子狐|です。占われても死にませんが、#人狼#に襲われると死んでしまいます。また、時々失敗しますが占いの真似事もできます。",
    'R' => 204, 'G' => 0, 'B' => 153, 'R2' => 255, 'G2' => 0, 'B2' => 0);

  var $cupid = array(
    'message' => "[役割] [|恋人|##陣営] [|キューピッド|系]\n　あなたは|キューピッド|です。初日の夜に誰か二人を##|恋人|同士にすることができます。##|恋人|たちを何としても生き延びさせるのです！",
    'R' => 255, 'G' => 51, 'B' => 153, 'R2' => 255, 'G2' => 51, 'B2' => 153);

  var $self_cupid = array(
    'message' => "[役割] [|恋人|##陣営] [|キューピッド|系]\n　あなたは|求愛者|です。初日の夜に自分と誰か一人を##|恋人|同士にすることができます。自分の幸せは自分で掴み取るのです！",
    'R' => 255, 'G' => 51, 'B' => 153, 'R2' => 255, 'G2' => 51, 'B2' => 153);

  var $mind_cupid = array(
    'message' => "[役割] [|恋人|##陣営] [|キューピッド|系]\n　あなたは|女神|です。初日の夜に誰か二人を会話ができる##|恋人|にすることができます。",
    'R' => 255, 'G' => 51, 'B' => 153, 'R2' => 255, 'G2' => 51, 'B2' => 153);

  var $cupid_pair = array(
    'message' => "あなたが愛の矢を放ったのは以下の人たちです： ", 'R' => 0, 'G' => 0, 'B' => 0, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $partner_header = array('message' => "あなたは");

  var $lovers_footer = array('message' => "と愛し合っています。妨害する者は誰であろうと消し、二人の愛の世界を築くのです！", 'R' => 0, 'G' => 0, 'B' => 0, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $quiz = array(
    'message' => "[役割] [#出題者#陣営] [|出題者|系]\n　あなたは|出題者|です。この村の難易度はあなたの口先三寸で決まります。頑張って皆を楽しませれば、それがあなたの勝利です。",
    'R' => 153, 'G' => 153, 'B' => 204, 'R2' => 153, 'G2' => 153, 'B2' => 204);

  var $quiz_chaos = array(
    'message' => "　闇鍋モードではあなたの最大の能力である噛み無効がありません。\n　はっきり言って無理ゲーなので好き勝手にクイズでも出して遊ぶと良いでしょう。",
    'R' => 153, 'G' => 153, 'B' => 204, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $chiroptera = array(
    'message' => "[役割] [#蝙蝠#陣営] [|蝙蝠|系]\n　あなたは|蝙蝠|です。生き残りましょう。ただそれだけで勝ちになります。",
    'R' => 136, 'G' => 136, 'B' => 136, 'R2' => 136, 'G2' => 136, 'B2' => 136);

  var $poison_chiroptera = array(
    'message' => "[役割] [|蝙蝠|##陣営] [|蝙蝠|系]\n　あなたは|毒蝙蝠|、#毒#を持っています。しかし、死んだら負けになってしまいます。頑張って生き残ってください。",
    'R' => 136, 'G' => 136, 'B' => 136, 'R2' => 0, 'G2' => 153, 'B2' => 102);

  var $cursed_chiroptera = array(
    'message' => "[役割] [|蝙蝠|##陣営] [|蝙蝠|系]\n　あなたは|呪蝙蝠|です。あなたを占った#占い師#を呪返しで殺すことができます。\n　この能力を活かしつつ、生き残りの道を探るのです。",
    'R' => 136, 'G' => 136, 'B' => 136, 'R2' => 153, 'G2' => 51, 'B2' => 255);

  var $mania = array(
    'message' => "[役割] [#村人#陣営] [|神話マニア|系]\n　あなたは|神話マニア|です。初日の夜に指定した人のメイン役職をコピーすることができます。",
    'R' => 192, 'G' => 160, 'B' => 96, 'R2' => 0, 'G2' => 0, 'B2' => 0);

  var $unknown_mania = array(
    'message' => "[役割] [#村人#陣営] [|神話マニア|系]\n　あなたは|鵺|です。初日の夜に指定した人と同じ陣営になり、二日目夜からお互いに会話できます。\n　相談しながらコピー先の陣営の勝利に貢献するのです。",
    'R' => 192, 'G' => 160, 'B' => 96, 'R2' => 0, 'G2' => 0, 'B2' => 0);
}

class SubRoleList{
  var $chicken = array('message' => "　あなたは|小心者|です。処刑投票時に一票でも貰うとショック死してしまいます。", 'R' => 51, 'G' => 204, 'B' => 255);

  var $rabbit = array('message' => "　あなたは|ウサギ|です。処刑投票時に一票も貰えないと寂しくて死んでしまいます。", 'R' => 51, 'G' => 204, 'B' => 255);

  var $perverseness = array('message' => "　あなたは|天邪鬼|です。処刑投票時に自分と同じ投票先の人がいるとショック死してしまいます。", 'R' => 51, 'G' => 204, 'B' => 255);

  var $flattery = array('message' => "　あなたは|ゴマすり|です。処刑投票時に自分と同じ投票先の人がいないとショック死してしまいます。", 'R' => 51, 'G' => 204, 'B' => 255);

  var $impatience = array('message' => "　あなたは|短気|です。決定力がありますが、再投票になるとショック死してしまいます。", 'R' => 51, 'G' => 204, 'B' => 255);

  var $celibacy = array('message' => "　あなたは|独身貴族|です。恋人に投票されるとショック死してしまいます。", 'R' => 51, 'G' => 204, 'B' => 255);

  var $panelist = array('message' => "　あなたは|解答者|です。不正解だったときは出題者に投票してください。",  'R' => 51, 'G' => 204, 'B' => 255);

  var $liar = array('message' => "　あなたは|狼少年|です。「人」と「狼」をわざと取り違えて発言してしまいます。", 'R' => 102, 'G' => 0, 'B' => 153);

  var $invisible = array('message' => "　あなたは|光学迷彩|を使っているので発言の一部が見えなくなります。",
'R' => 102, 'G' => 0, 'B' => 153);

  var $rainbow = array('message' => "　あなたは|虹色迷彩|を使っているので虹の順番に合わせて色を入れ替えて発言してしまいます。", 'R' => 102, 'G' => 0, 'B' => 153);

  var $weekly = array('message' => "　あなたは|七曜迷彩|を使っているので曜日の順番に合わせて曜日を入れ替えて発言してしまいます。", 'R' => 102, 'G' => 0, 'B' => 153);

  var $grassy = array('message' => "　あなたは|草原迷彩|を使っているので発言が草に埋もれてしまいます。", 'R' => 102, 'G' => 0, 'B' => 153);

  var $side_reverse = array('message' => "　あなたは|鏡面迷彩|を使っているので発言の左右が反転してしまいます。", 'R' => 102, 'G' => 0, 'B' => 153);

  var $line_reverse = array('message' => "　あなたは|天地迷彩|を使っているので発言の上下が反転してしまいます。", 'R' => 102, 'G' => 0, 'B' => 153);

  var $gentleman = array('message' => "　あなたは|紳士|です。時々紳士な発言をしてしまいます。", 'R' => 102, 'G' => 0, 'B' => 153);

  var $lady = array('message' => "　あなたは|淑女|です。時々淑女な発言をしてしまいます。", 'R' => 102, 'G' => 0, 'B' => 153);

  var $authority = array('message' => "　あなたは|権力者|です。あなたの投票は二票分の効果があります。",
			 'R' => 102, 'G' => 102, 'B' => 51);

  var $random_voter = array('message' => "　あなたは|気分屋|です。あなたの投票数はその時の気分次第です。",
			 'R' => 102, 'G' => 102, 'B' => 51);

  var $rebel = array('message' => "　あなたは|反逆者|です。権力者と同じ人に投票した場合、あなたと権力者の投票数が０になります。",
			 'R' => 102, 'G' => 102, 'B' => 51);

  var $watcher = array('message' => "　あなたは|傍観者|です。投票には参加するふりだけして村の行く末を眺めましょう。",
			 'R' => 102, 'G' => 102, 'B' => 51);

  var $upper_luck = array('message' => "　あなたは|雑草魂|の持ち主です。最初の処刑投票を切り抜けられれば運が向いてくるでしょう。",
			 'R' => 102, 'G' => 204, 'B' => 153);

  var $downer_luck = array('message' => "　あなたは|一発屋|です。最初の処刑投票以降は得票が増えます。派手に立ち回りましょう！",
			 'R' => 102, 'G' => 204, 'B' => 153);

  var $random_luck = array('message' => "　あなたは|波乱万丈|の運命を辿ります。翻弄されるか、乗り切れるかはあなた次第です。",
			 'R' => 102, 'G' => 204, 'B' => 153);

  var $star = array('message' => "　あなたは|人気者|なので得票数が１減ります。",
			 'R' => 102, 'G' => 204, 'B' => 153);

  var $disfavor = array('message' => "　あなたは|不人気|なので得票数が１増えます。めげずに頑張ってください。",
			 'R' => 102, 'G' => 204, 'B' => 153);

  var $strong_voice = array('message' => "　あなたは|大声|の持ち主です。上手に活用して皆を説得しましょう！",
			 'R' => 255, 'G' => 153, 'B' => 0);

  var $normal_voice = array('message' => "　あなたは|不器用|なので声の大きさを変えられません。",
			 'R' => 255, 'G' => 153, 'B' => 0);

  var $weak_voice = array('message' => "　あなたは|小声|の持ち主です。説得に苦労するかもしれませんが頑張ってください。",
			 'R' => 255, 'G' => 153, 'B' => 0);

  var $inside_voice = array('message' => "　あなたは|内弁慶|なので昼は小声に、夜は大声になります。", 'R' => 255, 'G' => 153, 'B' => 0);

  var $outside_voice = array('message' => "　あなたは|外弁慶|なので昼は大声に、夜は小声になります。", 'R' => 255, 'G' => 153, 'B' => 0);

  var $upper_voice = array('message' => "　あなたは|メガホン|を使っているので声が一段階大きくなります。大声は音割れしてしまいます。",
			 'R' => 255, 'G' => 153, 'B' => 0);

  var $downer_voice = array('message' => "　あなたは|マスク|をつけているので声が一段階小さくなります。小声は聞き取れなくなってしまいます。",
			 'R' => 255, 'G' => 153, 'B' => 0);

  var $random_voice = array('message' => "　あなたは|臆病者|です。この事態に混乱するあなたは声の大きさが安定しません。",
			 'R' => 255, 'G' => 153, 'B' => 0);

  var $no_last_words = array('message' => "　あなたは|筆不精|なので遺言を遺すことができません。言いたいことは全て昼間に言い切りましょう。",
			 'R' => 221, 'G' => 34, 'B' => 34);

  var $blinder = array('message' => "　あなたは|目隠し|をしているので発言者の名前が見えません。",
			 'R' => 221, 'G' => 34, 'B' => 34);

  var $earplug = array('message' => "　あなたは|耳栓|をつけているので声が一段階小さく聞こえます。小声は聞き取れません。",
			 'R' => 221, 'G' => 34, 'B' => 34);

  var $speaker = array('message' => "　あなたは|スピーカー|を使っているので声が一段階大きく聞こえます。大声は音割れしてしまいます。",
			 'R' => 221, 'G' => 34, 'B' => 34);

  var $silent = array('message' => "　あなたは|無口|なのであまり多くの言葉を話せません。",
			 'R' => 221, 'G' => 34, 'B' => 34);

  var $mower = array('message' => "　あなたは|草刈り|なので発言から草が刈り取られてしまいます。", 'R' => 221, 'G' => 34, 'B' => 34);

  var $mind_read = array('message' => "　あなたは|サトラレ|です。夜の発言がさとりに読まれてしまいます。",
			 'delimiter' => array('|' => array('R' => 160, 'G' => 160, 'B' => 0)));
  var $mind_receiver = array('message' => "　あなたは|受信者|です。夜の間だけ誰かの発言を読み取ることができます。",
			     'delimiter' => array('|' => array('R' => 160, 'G' => 160, 'B' => 0)));
  var $mind_friend = array('message' => "　あなたは|共鳴者|です。夜の間だけ共鳴者同士で会話することができます。",
			   'delimiter' => array('|' => array('R' => 160, 'G' => 160, 'B' => 0)));
  var $mind_open = array('message' => "　あなたは|公開者|です。夜の発言が全員に見えます。気をつけましょう。",
			 'delimiter' => array('|' => array('R' => 160, 'G' => 160, 'B' => 0)));
  var $mind_evoke = array('message' => "　あなたは|イタコ|に|口寄せ|されています。死んだ後に遺言を介して|イタコ|にメッセージを送ることができます。",
			  'delimiter' => array('|' => array('R' => 160, 'G' => 160, 'B' => 0)));
  var $ability_poison = array('message' => "　あなたは|毒|を持っています。処刑されたり、人狼に襲撃された時に誰か一人を道連れにします。",
		    'R' => 0, 'G' => 153, 'B' => 102);
  var $common_partner = array('message' => "同じ|共有者|の仲間は以下の人たちです： ", 'R' => 204, 'G' => 102, 'B' => 51);
  var $fox_partner = array('message' => "深遠なる|妖狐|の智を持つ同胞は以下の人たちです： ", 'R' => 204, 'G' => 0, 'B' => 153);
  var $child_fox_partner = array('message' => "|妖狐|に与する仲間は以下の人たちです： ", 'R' => 204, 'G' => 0, 'B' => 153);
  var $fox_targeted = array('message' => "昨晩、|人狼|に狙われたようです", 'R' => 255, 'G' => 0, 'B' => 0);
  var $guard_hunted = array('message' => "さんを狩ることに成功しました！", 'R' => 0, 'G' => 0, 'B' => 0);
  var $guard_success = array('message' => "さん護衛成功！", 'R' => 0, 'G' => 0, 'B' => 0);
  var $voodoo_killer_success = array('message' => "さんの解呪に成功しました！", 'R' => 0, 'G' => 0, 'B' => 0);
  var $anti_voodoo_success = array('message' => "さんの厄払いに成功しました！", 'R' => 0, 'G' => 0, 'B' => 0);
  var $lost_ability = array('message' => "　あなたは能力を失いました", 'R' => 0, 'G' => 0, 'B' => 0);
  var $mad_partner = array('message' => "|人狼|に仕える##|狂人|は以下の人たちです： ", 'R' => 255, 'G' => 0, 'B' => 0);
  var $mage_result = array('message' => "占い結果： ", 'R' => 0, 'G' => 0, 'B' => 0);
  var $medium_result = array('message' => "神託結果： ", 'R' => 0, 'G' => 0, 'B' => 0);
  var $priest_header = array('message' => "神託結果： 現在、生存している村人陣営は", 'R' => 0, 'G' => 0, 'B' => 0);
  var $priest_footer = array('message' => "人です", 'R' => 0, 'G' => 0, 'B' => 0);
  var $crisis_priest_result = array('message' => "陣営が勝利目前です", 'delimiter' => array());
  var $side_wolf = array('message' => "|人狼|",
			 'delimiter' => array('|' => array('R' => 255, 'G' => 0, 'B' => 0)));
  var $side_fox = array('message' => "|妖狐|",
			'delimiter' => array('|' => array('R' => 204, 'G' => 0, 'B' => 153)));
  var $side_lovers = array('message' => "|恋人|",
			   'delimiter' => array('|' => array('R' => 255, 'G' => 51, 'B' => 153)));
  var $necromancer_result = array('message' => "霊能結果： ", 'R' => 0, 'G' => 0, 'B' => 0);
  var $pharmacist_nothing = array('message' => "さんは毒を持っていません", 'R' => 0, 'G' => 0, 'B' => 0);
  var $pharmacist_poison = array('message' => "さんは|毒|を持っています", 'R' => 0, 'G' => 153, 'B' => 102);
  var $pharmacist_strong = array('message' => "さんは|強い毒|を持っています", 'R' => 0, 'G' => 153, 'B' => 102);
  var $pharmacist_limited = array('message' => "さんは|限定的な毒|を持っています", 'R' => 0, 'G' => 153, 'B' => 102);
  var $pharmacist_success = array('message' => "さんの|解毒|に成功しました", 'R' => 0, 'G' => 153, 'B' => 102);
  var $poison_cat_failed = array('message' => "さん蘇生失敗", 'R' => 0, 'G' => 0, 'B' => 0);
  var $poison_cat_success = array('message' => "さん蘇生成功！", 'R' => 0, 'G' => 0, 'B' => 0);
  var $result_mage_failed = array('message' => "さんの鑑定に失敗しました", 'R' => 0, 'G' => 0, 'B' => 0);
  var $result_sex_male = array('message' => "さんは|男性|でした", 'R' => 0, 'G' => 0, 'B' => 255);
  var $result_sex_female = array('message' => "さんは|女性|でした", 'R' => 255, 'G' => 51, 'B' => 153);
  var $result_psycho_mage_normal = array('message' => "さんは正常でした", 'R' => 0, 'G' => 0, 'B' => 0);
  var $result_psycho_mage_liar = array('message' => "さんは|嘘|をついています", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_stolen = array('message' => "さんの死体が盗まれました！", 'R' => 0, 'G' => 0, 'B' => 0);
  var $reporter_result_footer = array('message' => "さんに襲撃されました！", 'R' => 0, 'G' => 0, 'B' => 0);
  var $reporter_result_header = array('message' => "張り込み結果： ", 'R' => 0, 'G' => 0, 'B' => 0);
  var $wolf_partner = array('message' => "誇り高き|人狼|の血を引く仲間は以下の人たちです： ", 'R' => 255, 'G' => 0, 'B' => 0);
  var $wolf_result = array('message' => "襲撃結果： ", 'R' => 0, 'G' => 0, 'B' => 0);
  var $possessed_target = array('message' => "さんに|憑依|しています ", 'R' => 255, 'G' => 0, 'B' => 0);
}

class ResultList{
  var $result_human = array('message' => "さんは|村人|でした", 'R' => 0, 'G' => 0, 'B' => 0);
  var $result_mage = array('message' => "さんは|占い師|でした", 'R' => 153, 'G' => 51, 'B' => 255);
  var $result_soul_mage = array('message' => "さんは|魂の占い師|でした", 'R' => 153, 'G' => 51, 'B' => 255);
  var $result_psycho_mage = array('message' => "さんは|精神鑑定士|でした", 'R' => 153, 'G' => 51, 'B' => 255);
  var $result_sex_mage = array('message' => "さんは|ひよこ鑑定士|でした", 'R' => 153, 'G' => 51, 'B' => 255);
  var $result_voodoo_killer = array('message' => "さんは|陰陽師|でした", 'R' => 153, 'G' => 51, 'B' => 255);
  var $result_dummy_mage = array('message' => "さんは|夢見人|でした", 'R' => 153, 'G' => 51, 'B' => 255);
  var $result_necromancer = array('message' => "さんは|霊能者|でした", 'R' => 0, 'G' => 102, 'B' => 153);
  var $result_soul_necromancer = array('message' => "さんは|雲外鏡|でした", 'R' => 0, 'G' => 102, 'B' => 153);
  var $result_yama_necromancer = array('message' => "さんは|閻魔|でした", 'R' => 0, 'G' => 102, 'B' => 153);
  var $result_dummy_necromancer = array('message' => "さんは|夢枕人|でした", 'R' => 0, 'G' => 102, 'B' => 153);
  var $result_medium = array('message' => "さんは|巫女|でした", 'R' => 0, 'G' => 102, 'B' => 153);
  var $result_priest = array('message' => "さんは|司祭|でした", 'R' => 0, 'G' => 102, 'B' => 153);
  var $result_crisis_priest = array('message' => "さんは|預言者|でした",
				    'delimiter' => array('|' => array('R' => 0, 'G' => 102, 'B' => 153)));
  var $result_revive_priest = array('message' => "さんは|天人|でした",
				    'delimiter' => array('|' => array('R' => 0, 'G' => 102, 'B' => 153)));
  var $result_guard = array('message' => "さんは|狩人|でした", 'R' => 51, 'G' => 153, 'B' => 255);
  var $result_poison_guard = array('message' => "さんは|騎士|でした", 'R' => 51, 'G' => 153, 'B' => 255);
  var $result_reporter = array('message' => "さんは|ブン屋|でした", 'R' => 51, 'G' => 153, 'B' => 255);
  var $result_anti_voodoo = array('message' => "さんは|厄神|でした", 'R' => 51, 'G' => 153, 'B' => 255);
  var $result_dummy_guard = array('message' => "さんは|夢守人|でした", 'R' => 51, 'G' => 153, 'B' => 255);
  var $result_common = array('message' => "さんは|共有者|でした", 'R' => 204, 'G' => 102, 'B' => 51);
  var $result_dummy_common = array('message' => "さんは|夢共有者|でした", 'R' => 204, 'G' => 102, 'B' => 51);
  var $result_poison = array('message' => "さんは|埋毒者|でした", 'R' => 0, 'G' => 153, 'B' => 102);
  var $result_strong_poison = array('message' => "さんは|強毒者|でした", 'R' => 0, 'G' => 153, 'B' => 102);
  var $result_incubate_poison = array('message' => "さんは|潜毒者|でした", 'R' => 0, 'G' => 153, 'B' => 102);
  var $result_dummy_poison = array('message' => "さんは|夢毒者|でした", 'R' => 0, 'G' => 153, 'B' => 102);
  var $result_poison_cat = array('message' => "さんは|猫又|でした", 'R' => 0, 'G' => 153, 'B' => 102);
  var $result_revive_cat = array('message' => "さんは|仙狸|でした",
				 'delimiter' => array('|' => array('R' => 0, 'G' => 153, 'B' => 102)));
  var $result_pharmacist = array('message' => "さんは|薬師|でした", 'R' => 0, 'G' => 153, 'B' => 102);
  var $result_assassin = array('message' => "さんは|暗殺者|でした", 'R' => 144, 'G' => 64, 'B' => 64);
  var $result_mind_scanner = array('message' => "さんは|さとり|でした", 'R' => 160, 'G' => 160, 'B' => 0);
  var $result_evoke_scanner = array('message' => "さんは|イタコ|でした",
				    'delimiter' => array('|' => array('R' => 160, 'G' => 160, 'B' => 0)));
  var $result_jealousy = array('message' => "さんは|橋姫|でした", 'R' => 0, 'G' => 204, 'B' => 0);
  var $result_suspect = array('message' => "さんは|不審者|でした", 'R' => 0, 'G' => 0, 'B' => 0);
  var $result_unconscious = array('message' => "さんは|無意識|でした", 'R' => 0, 'G' => 0, 'B' => 0);
  var $result_wolf        = array('message' => "さんは|人狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_boss_wolf   = array('message' => "さんは|白狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_tongue_wolf = array('message' => "さんは|舌禍狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_wise_wolf   = array('message' => "さんは|賢狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_cursed_wolf = array('message' => "さんは|呪狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_possessed_wolf = array('message' => "さんは|憑狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_poison_wolf = array('message' => "さんは|毒狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_resist_wolf = array('message' => "さんは|抗毒狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_cute_wolf   = array('message' => "さんは|萌狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_scarlet_wolf = array('message' => "さんは|紅狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_silver_wolf = array('message' => "さんは|銀狼|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_mad = array('message' => "さんは|狂人|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_fanatic_mad = array('message' => "さんは|狂信者|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_whisper_mad = array('message' => "さんは|囁き狂人|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_jammer_mad = array('message' => "さんは|月兎|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_voodoo_mad = array('message' => "さんは|呪術師|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_corpse_courier_mad = array('message' => "さんは|火車|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_dream_eater_mad = array('message' => "さんは|獏|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_trap_mad = array('message' => "さんは|罠師|でした", 'R' => 255, 'G' => 0, 'B' => 0);
  var $result_fox = array('message' => "さんは|妖狐|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_white_fox = array('message' => "さんは|白狐|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_black_fox = array('message' => "さんは|黒狐|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_poison_fox = array('message' => "さんは|管狐|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_voodoo_fox = array('message' => "さんは|九尾|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_revive_fox = array('message' => "さんは|仙狐|でした",
				 'delimiter' => array('|' => array('R' => 204, 'G' => 0, 'B' => 153)));
  var $result_cursed_fox = array('message' => "さんは|天狐|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_scarlet_fox = array('message' => "さんは|紅狐|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_cute_fox = array('message' => "さんは|萌狐|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_silver_fox = array('message' => "さんは|銀狐|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_child_fox = array('message' => "さんは|子狐|でした", 'R' => 204, 'G' => 0, 'B' => 153);
  var $result_cupid = array('message' => "さんは|キューピッド|でした", 'R' => 255, 'G' => 51, 'B' => 153);
  var $result_self_cupid = array('message' => "さんは|求愛者|でした", 'R' => 255, 'G' => 51, 'B' => 153);
  var $result_mind_cupid = array('message' => "さんは|女神|でした", 'R' => 255, 'G' => 51, 'B' => 153);
  var $result_lovers = array('message' => "さんは|恋人|でした", 'R' => 255, 'G' => 51, 'B' => 153);
  var $result_quiz = array('message' => "さんは|出題者|でした", 'R' => 153, 'G' => 153, 'B' => 204);
  var $result_chiroptera = array('message' => "さんは|蝙蝠|でした", 'R' => 136, 'G' => 136, 'B' => 136);
  var $result_poison_chiroptera = array('message' => "さんは|毒蝙蝠|でした", 'R' => 136, 'G' => 136, 'B' => 136);
  var $result_cursed_chiroptera = array('message' => "さんは|呪蝙蝠|でした", 'R' => 136, 'G' => 136, 'B' => 136);
  var $result_dummy_chiroptera = array('message' => "さんは|夢求愛者|でした", 'R' => 136, 'G' => 136, 'B' => 136);
  var $result_mania = array('message' => "さんは|神話マニア|でした", 'R' => 192, 'G' => 160, 'B' => 96);
  var $result_unknown_mania = array('message' => "さんは|鵺|でした", 'R' => 192, 'G' => 160, 'B' => 96);
}

class WishRoleList {
  var $role_none = array('message' => "無し→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_human = array('message' => "村人→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_mage = array('message' => "占い師→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_necromancer = array('message' => "霊能者→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_medium = array('message' => "巫女→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_guard = array('message' => "狩人→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_common = array('message' => "共有者→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_poison = array('message' => "埋毒者→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_pharmacist = array('message' => "薬師→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_assassin = array('message' => "暗殺者→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_wolf = array('message' => "人狼→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_boss_wolf = array('message' => "白狼→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_poison_wolf = array('message' => "毒狼→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_mad = array('message' => "狂人→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_fanatic_mad = array('message' => "狂信者→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_trap_mad = array('message' => "罠師→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_fox = array('message' => "妖狐→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_cupid = array('message' => "キューピッド→", 'R' => 0, 'G' => 0, 'B' => 0);

  var $role_mania = array('message' => "神話マニア→", 'R' => 0, 'G' => 0, 'B' => 0);

  var $role_chiroptera = array('message' => "蝙蝠→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_quiz = array('message' => "出題者→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_priest = array('message' => "司祭→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_mind_scanner = array('message' => "さとり→", 'R' => 0, 'G' => 0, 'B' => 0);
  var $role_poison_cat = array('message' => "猫又→",
			       'delimiter' => array('|' => array('R' => 4, 'G' => 0, 'B' => 0)));
  var $role_jealousy = array('message' => "橋姫→", 'R' => 0, 'G' => 0, 'B' => 0);
}

//$image = $gen->GetImage("あなたは", 255, 0, 0);

//imagegif($image, "c:\\temp\\result.gif"); // ファイルに出力する場合
#$list =& new MainRoleList();
#$list =& new SubRoleList();
#$list =& new ResultList();

$gen = new MessageImageGenerator("C:\\WINDOWS\\Fonts\\" . $font_name, 12, 3, 3, true);
$list =& new WishRoleList();

//まとめて画像ファイル生成
/*
foreach($list as $name => $array){
  #echo "$name : "; print_r($a); echo '<br>';
  $image = MakeImage($gen, $array);
  imagegif($image, "./test/{$name}.gif");
  imagedestroy($image);
  echo "$name : {$array['message']} <br>";
}
*/
header('Content-Type: image/gif');
$image = MakeImage($gen, $list->role_poison_cat);
imagegif($image);
// imagegif($image, './test/test.gif');
// imagedestroy($image);
