<?php
require_once('MessageImageGenerator2.php');

/*
//画像出力関数 (旧版)
function MakeImage($generator, $class){
  return $generator->GetImage($class['message'], $class['R'], $class['G'], $class['B'],
			      $class['R2'], $class['G2'], $class['B2'],
			      $class['R3'], $class['G3'], $class['B3'],
			      $class['R4'], $class['G4'], $class['B4']);
}
*/

class MessageImageBuilder{
  var $font = 'azuki.ttf';
  #var $font = 'yutaCo2_ttc_027.ttc';
  #var $font = 'aquafont.ttf';

  var $generator;
  var $list;
  var $color_list = array(
    'human'		=> array('R' =>  96, 'G' =>  96, 'B' =>  96),
    'mage'		=> array('R' => 153, 'G' =>  51, 'B' => 255),
    'necromancer'	=> array('R' =>   0, 'G' => 102, 'B' => 153),
    'medium'		=> array('R' => 153, 'G' => 204, 'B' =>   0),
    'priest'		=> array('R' =>  77, 'G' =>  77, 'B' => 204),
    'guard'		=> array('R' =>  51, 'G' => 153, 'B' => 255),
    'common'		=> array('R' => 204, 'G' => 102, 'B' =>  51),
    'poison'		=> array('R' =>   0, 'G' => 153, 'B' => 102),
    'revive'		=> array('R' =>   0, 'G' => 153, 'B' => 102),
    'assassin'		=> array('R' => 144, 'G' =>  64, 'B' =>  64),
    'mind'		=> array('R' => 160, 'G' => 160, 'B' =>   0),
    'jealousy'		=> array('R' =>   0, 'G' => 204, 'B' =>   0),
    'doll'		=> array('R' =>  96, 'G' =>  96, 'B' => 255),
    'brownie'		=> array('R' => 144, 'G' => 192, 'B' => 160),
    'wolf'		=> array('R' => 255, 'G' =>   0, 'B' =>   0),
    'fox'		=> array('R' => 204, 'G' =>   0, 'B' => 153),
    'lovers'		=> array('R' => 255, 'G' =>  51, 'B' => 153),
    'quiz'		=> array('R' => 153, 'G' => 153, 'B' => 204),
    'vampire'		=> array('R' => 208, 'G' =>   0, 'B' => 208),
    'chiroptera'	=> array('R' => 136, 'G' => 136, 'B' => 136),
    'mania'		=> array('R' => 192, 'G' => 160, 'B' =>  96),
    'vote'		=> array('R' => 153, 'G' => 153, 'B' =>   0),
    'chicken'		=> array('R' =>  51, 'G' => 204, 'B' => 255),
    'liar'		=> array('R' => 102, 'G' =>   0, 'B' => 153),
    'authority'		=> array('R' => 102, 'G' => 102, 'B' =>  51),
    'luck'		=> array('R' => 102, 'G' => 204, 'B' => 153),
    'voice'		=> array('R' => 255, 'G' => 153, 'B' =>   0),
    'decide'		=> array('R' => 153, 'G' => 153, 'B' => 153),
    'no_last_words'	=> array('R' => 221, 'G' =>  34, 'B' =>  34),
    'sex_male'		=> array('R' =>   0, 'G' =>   0, 'B' => 255)
			  );

  function MessageImageBuilder($list){ $this->__construct($list); }
  function __construct($list){
    $font = "C:\\WINDOWS\\Fonts\\" . $this->font;
    $size = ($trans = $list == 'WishRoleList') ? 12 : 10;
    $this->generator = new MessageImageGenerator($font, $size, 3, 3, $trans);
    $this->list = new $list();
  }

  function LoadDelimiter($delimiter, $colors){
    if(! is_array($colors)) $colors = $this->color_list[$colors];
    return new Delimiter($delimiter, $colors['R'], $colors['G'], $colors['B']);
  }

  function AddDelimiter($list){
    foreach($list['delimiter'] as $delimiter => $colors){
      $this->generator->AddDelimiter($this->LoadDelimiter($delimiter, $colors));
    }
  }

  function SetDelimiter($list){
    if(isset($list['type'])) $this->SetDelimiter($this->list->{$list['type']});
    if(is_null($list['delimiter'])) $list['delimiter'] = array();
    $this->AddDelimiter($list);
  }

  function Generate($name){
    $this->SetDelimiter($this->list->$name);
    return $this->generator->GetImage($this->list->{$name}['message']);
  }

  function Output($name){
    header('Content-Type: image/gif');
    $image = $this->Generate($name);
    imagegif($image);
  }

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
}

class RoleColorList{
  var $human		= array('R' =>  96, 'G' =>  96, 'B' =>  96);
  var $mage		= array('R' => 153, 'G' =>  51, 'B' => 255);
  var $necromancer	= array('R' =>   0, 'G' => 102, 'B' => 153);
  var $medium		= array('R' => 153, 'G' => 204, 'B' =>   0);
  var $priest		= array('R' =>  77, 'G' =>  77, 'B' => 204);
  var $guard		= array('R' =>  51, 'G' => 153, 'B' => 255);
  var $common		= array('R' => 204, 'G' => 102, 'B' =>  51);
  var $poison		= array('R' =>   0, 'G' => 153, 'B' => 102);
  var $revive		= array('R' =>   0, 'G' => 153, 'B' => 102);
  var $assassin		= array('R' => 144, 'G' =>  64, 'B' =>  64);
  var $mind		= array('R' => 160, 'G' => 160, 'B' =>   0);
  var $jealousy		= array('R' =>   0, 'G' => 204, 'B' =>   0);
  var $doll		= array('R' =>  96, 'G' =>  96, 'B' => 255);
  var $brownie		= array('R' => 144, 'G' => 192, 'B' => 160);
  var $wolf		= array('R' => 255, 'G' =>   0, 'B' =>   0);
  var $fox		= array('R' => 204, 'G' =>   0, 'B' => 153);
  var $lovers		= array('R' => 255, 'G' =>  51, 'B' => 153);
  var $quiz		= array('R' => 153, 'G' => 153, 'B' => 204);
  var $vampire		= array('R' => 208, 'G' =>   0, 'B' => 208);
  var $chiroptera	= array('R' => 136, 'G' => 136, 'B' => 136);
  var $mania		= array('R' => 192, 'G' => 160, 'B' =>  96);
  var $vote		= array('R' => 153, 'G' => 153, 'B' =>   0);
  var $chicken		= array('R' =>  51, 'G' => 204, 'B' => 255);
  var $liar		= array('R' => 102, 'G' =>   0, 'B' => 153);
  var $authority	= array('R' => 102, 'G' => 102, 'B' =>  51);
  var $luck		= array('R' => 102, 'G' => 204, 'B' => 153);
  var $voice		= array('R' => 255, 'G' => 153, 'B' =>   0);
  var $decide		= array('R' => 153, 'G' => 153, 'B' => 153);
  var $no_last_words	= array('R' => 221, 'G' =>  34, 'B' =>  34);
  var $sex_male		= array('R' =>   0, 'G' =>   0, 'B' => 255);
}

class RoleMessageList{
  var $human = array(
    'message' => "[役割] [|村人|陣営] [|村人|系]\n　あなたは|村人|です。特殊な能力はありませんが、あなたの知恵と勇気で村を救えるはずです。",
    'delimiter' => array('|' => 'human'));

  var $elder = array(
    'message' => "[役割] [|村人|陣営] [|村人|系]\n　あなたは|長老|です。あなたの#処刑#投票には_二票_分の価値があります。年功者の知恵を活かして村を勝利に導くのです。",
    'type' => 'human', 'delimiter' => array('#' => 'vote', '_' => 'authority'));

  var $escaper = array(
    'message' => "[役割] [|村人|陣営] [|村人|系]\n　あなたは|逃亡者|です。臆病なあなたは夜の間、誰かの家の近くに隠れて夜をすごすことになります。\n　逃亡生活で培った直感と判断力を武器として、安住の地を取り戻すまで#人狼#から逃げ切るのです！",
    'type' => 'human', 'delimiter' => array('#' => 'wolf'));

  var $mage = array(
    'message' => "[役割] [|村人|陣営] [#占い師#系]\n　あなたは#占い師#、夜の間に村人一人を占うことで翌朝その人が「|人|」か「_狼_」か知ることができます。あなたが村人の勝利を握っています。",
    'type' => 'human', 'delimiter' => array('#' => 'mage', '_' => 'wolf'));

  var $soul_mage = array(
    'message' => "[役割] [|村人|陣営] [#占い師#系]\n　あなたは#魂の占い師#、役職を知ることができる#占い師#です。自らの運命をも、その魂で切り開くことができるはずです。",
    'type' => 'mage');

  var $psycho_mage = array(
    'message' => "[役割] [|村人|陣営] [#占い師#系]\n　あなたは#精神鑑定士#、心理を図ることができる#占い師#です。_嘘つき_や_夢_を見ている人を探し出して村の混乱を収めるのです！",
    'type' => 'mage');

  var $sex_mage = array(
    'message' => "[役割] [|村人|陣営] [#占い師#系]\n　あなたは#ひよこ鑑定士#、性別が分かる#占い師#です。特にメリットはありませんが楽しんでください。",
    'type' => 'mage');

  var $stargazer_mage = array(
    'message' => "[役割] [|村人|陣営] [#占い師#系]\n　あなたは#占星術師#です。占った人が夜に行動しているかどうかを知ることができます。\n　頭上に輝く星々は全ての夜を知っている。星々の視点からしか見えぬ事を知るのです。",
    'type' => 'mage');

  var $voodoo_killer = array(
    'message' => "[役割] [|村人|陣営] [#占い師#系]\n　あなたは#陰陽師#です。夜の間に村人一人を占うことでその人の呪いを祓うことができます。\n　呪いや憑依の力で村を陥れようとする人外たちを呪返しで祓い去り、村を清めるのです！",
    'type' => 'mage');

  var $necromancer = array(
    'message' => "[役割] [|村人|陣営] [#霊能者#系]\n　あなたは#霊能者#、その日の_処刑_者が「|人|」か「^狼^」か翌日の朝に知ることができます。\n　地味ですがあなたの努力次第で大きく貢献することも不可能ではありません。",
    'type' => 'human', 'delimiter' => array('#' => 'necromancer', '_' => 'vote', '^' => 'wolf'));

  var $soul_necromancer = array(
    'message' => "[役割] [|村人|陣営] [#霊能者#系]\n　あなたは#雲外鏡#、役職を知ることができる#霊能者#です。全てを見抜くその鏡で_処刑_者の正体を暴くのです！",
    'type' => 'necromancer');

  var $yama_necromancer = array(
    'message' => "[役割] [|村人|陣営] [#霊能者#系]\n　あなたは#閻魔#です。前日の死者の死因を知ることができます。魂に沙汰を下すその力で死者に白黒はっきりつけてやりましょう！",
    'type' => 'necromancer');

  var $medium = array(
    'message' => "[役割] [|村人|陣営] [#巫女#系]\n　あなたは#巫女#、_突然死_した人の所属陣営を知ることができます。不慮の死を遂げた人の正体を知らせ、村の推理に貢献するのです！",
    'type' => 'human', 'delimiter' => array('#' => 'medium', '_' => 'chicken'));

  var $seal_medium = array(
    'message' => "[役割] [|村人|陣営] [#巫女#系]\n　あなたは#封印師#です。_突然死_した人の所属陣営を知ることができます。また、^処刑^投票した人の限定能力を封じることができます。\n　あなたに与えられたのは破邪の聖印。数多の邪悪は、その輝きの前には無力です。その力を振るい、無辜なる村人を救うのです。",
    'type' => 'medium', 'delimiter' => array('^' => 'vote'));

  var $revive_medium = array(
    'message' => "[役割] [|村人|陣営] [#巫女#系]\n　あなたは#風祝#です。_突然死_した人の所属陣営を知ることができます。また、死んだ人を誰か一人^蘇生^できます。\n　あなたが持ちしは奇跡の力。神の御力をもって死した村人の魂を呼び戻し、彼らの心に安寧をもたらすのです！",
    'type' => 'medium', 'delimiter' => array('^' => 'revive'));

  var $priest = array(
    'message' => "[役割] [|村人|陣営] [#司祭#系]\n　あなたは#司祭#です。一定日数おきに現在生きている|村人|陣営の総数を知ることができます。\n　神のお告げで清き村人達の人数を知り、「村人を導くべし」との神のご意志に適うのです！",
    'type' => 'human', 'delimiter' => array('#' => 'priest'));

  var $bishop_priest = array(
    'message' => "[役割] [|村人|陣営] [#司祭#系]\n　あなたは#司教#です。一定日数おきに死亡した|村人|陣営以外の総数を知ることができます。\n　神聖なるお告げにより死者達の真の姿を伝え、心清き村人達を正しき道へと導くのです。",
    'type' => 'priest');

  var $dowser_priest = array(
    'message' => "[役割] [|村人|陣営] [#司祭#系]\n　あなたは#探知師#です。一定日数おきに現在生きている人が所有しているサブ役職の総数を知ることができます。\n　あなたの探知能力と推理力次第では、水面下にて息を潜める特殊な陣営を見抜くことも不可能ではありません。",
    'type' => 'priest');

  var $border_priest = array(
    'message' => "[役割] [|村人|陣営] [#司祭#系]\n　あなたは#境界師#です。二日目以降、夜にあなたに投票した人の数を知ることができます。\n　夜に見た夢は幻ではない。あなたにしか持ち得ない夢と現の双方の視点を活かすのです。",
    'type' => 'priest');

  var $revive_priest = array(
    'message' => "[役割] [|村人|陣営] [#司祭#系]\n　あなたは#天人#です。初日に一度天に帰って下界の様子を眺める事になります。後で颯爽と降臨して華麗に村を勝利に導きましょう。",
    'type' => 'priest');

  var $guard = array(
    'message' => "[役割] [|村人|陣営] [#狩人#系]\n　あなたは#狩人#です。夜の間に村人一人を_人狼_から護ることができます。_人狼_のココロを読むのです。",
    'type' => 'human', 'delimiter' => array('#' => 'guard', '_' => 'wolf'));

  var $hunter_guard = array(
    'message' => "[役割] [|村人|陣営] [#狩人#系]\n　あなたは#猟師#です。#護衛#先が^妖狐^なら#狩る#ことができますが、_人狼_に襲撃された場合は殺されてしまいます。\n　あなたには二つの道が与えられました。身代わりの盾と、^妖狐^を討つ剣。あなたの選択が村を救うのです！",
    'type' => 'guard', 'delimiter' => array('^' => 'fox'));

  var $blind_guard = array(
    'message' => "[役割] [|村人|陣営] [#狩人#系]\n　あなたは#夜雀#です。#狩り#能力はありませんが、#護衛#先を襲撃した人外を^目隠し^にして撃退することができます。\n　静寂な夜の翼、舞うは守護の羽。その羽で大事な人を護り、_狼_に終わらぬ夜を、=吸血鬼=に迷いの夜の贈り物を。",
    'type' => 'guard', 'delimiter' => array('^' => 'no_last_words', '=' => 'vampire'));

  var $poison_guard = array(
    'message' => "[役割] [|村人|陣営] [#狩人#系]\n　あなたは#騎士#です。夜の間に村人一人を_人狼_から護ることができます。もし、あなたが_人狼_に襲われたら刺し違えてでも倒すのです！",
    'type' => 'guard');

  var $fend_guard = array(
    'message' => "[役割] [|村人|陣営] [#狩人#系]\n　あなたは#忍者#です。一度だけ_人狼_の襲撃に耐えることができます。三日月を背に_人狼_の襲撃を読み、忍びの力で村を勝利に導くのです！",
    'type' => 'guard');

  var $reporter = array(
    'message' => "[役割] [|村人|陣営] [#狩人#系]\n　あなたは#ブン屋#、尾行した人が襲撃されたらスクープを入手できます。_人狼_や^妖狐^に気づかれないよう、慎重かつ大胆に行動するのです！",
    'type' => 'guard', 'delimiter' => array('^' => 'fox'));

  var $anti_voodoo = array(
    'message' => "[役割] [|村人|陣営] [#狩人#系]\n　あなたは#厄神#です。夜の間に村人一人の災厄を祓うことができます。呪いから_占い師_を護り、村を浄化するのです！",
    'type' => 'guard', 'delimiter' => array('_' => 'mage'));

  var $common = array(
    'message' => "[役割] [|村人|陣営] [#共有者#系]\n　あなたは#共有者#、他の#共有者#が誰であるか知ることができます。生存期間が他と比べ永い能力です。\n　あなたは推理する時間が与えられたのです。悩みなさい！",
    'type' => 'human', 'delimiter' => array('#' => 'common'));

  var $detective_common = array(
    'message' => "[役割] [|村人|陣営] [#共有者#系]\n　あなたは#探偵#です。他の#共有者#が誰であるか知ることができます。また、_暗殺_や^毒^で死ぬことはありません。\n　あなたの推理と決断力が問われる事になります。この難事件を見事解決し、名実ともに名探偵になるのです。",
    'type' => 'common', 'delimiter' => array('_' => 'assassin', '^' => 'poison'));

  var $trap_common = array(
    'message' => "[役割] [|村人|陣営] [#共有者#系]\n　あなたは#策士#です。他の#共有者#が誰であるか知ることができます。また、村人以外の票を全て集めたらまとめて罠にかけることができます。\n　権謀術数が渦巻く村で、勝利を確信して数の暴威を振るう人外達に策とはどういうものか、その身の破滅と引き換えに教えてあげましょう。",
    'type' => 'common');

  var $ghost_common = array(
    'message' => "[役割] [|村人|陣営] [#共有者#系]\n　あなたは#亡霊嬢#です。他の#共有者#が誰であるか知ることができます。また、あなたを襲った_人狼_を^小心者^にしてしまいます。\n　あなたの魂魄は、黄泉への誘い水。^ショック死^の恐怖に怯える狼が因果の報いを受けるまで、冥府で幽雅に見守りましょう。",
    'type' => 'common', 'delimiter' => array('_' => 'wolf', '^' => 'chicken'));

  var $poison = array(
    'message' => "[役割] [|村人|陣営] [#埋毒者#系]\n　あなたは#埋毒者#です。_人狼_に襲われた場合は_人狼_の中から、^処刑^された場合は生きている村の人たちの中からランダムで一人道連れにします。",
    'type' => 'human', 'delimiter' => array('#' => 'poison', '_' => 'wolf', '^' => 'vote'));

  var $incubate_poison = array(
    'message' => "[役割] [|村人|陣営] [#埋毒者#系]\n　あなたは#潜毒者#です。時が経つにつれてあなたの体内に秘められた#毒#が顕在化してきます。まずは時間を稼ぐのです。",
    'type' => 'poison');

  var $guide_poison = array(
    'message' => "[役割] [|村人|陣営] [#埋毒者#系]\n　あなたは#誘毒者#です。あなたの#毒#は#毒#能力者にしか中りません。毒を以って毒を制すのです。",
    'type' => 'poison');

  var $poison_cat = array(
    'message' => "[役割] [|村人|陣営] [#猫又#系]\n　あなたは#猫又#、#毒#をもっています。また、死んだ人を誰か一人蘇らせる事ができます。\n　飼ってくれた家の為、また村人の喜ぶ笑顔を見る為に、忍び込んだ人外を滅ぼすのです！
",
    'type' => 'revive');

  var $revive_cat = array(
    'message' => "[役割] [|村人|陣営] [#猫又#系]\n　あなたは#仙狸#です。高い#蘇生#能力を持っていますが、成功するたびに蘇生率が下がります。\n　人の精気を頂戴しつつ、仙山の秘奥で身に付けたその神秘の力で人々に恩を返すのです！",
    'type' => 'poison_cat');

  var $sacrifice_cat = array(
    'message' => "[役割] [|村人|陣営] [#猫又#系]\n　あなたは#猫神#です。死んだ人を誰か一人、確実に蘇らせる事ができますが自分は死んでしまいます。\n　あなたが残せる最後の御業は「等価を以て魂を反す」事。死を以て、輪廻の輪へと魂を導くのです。",
    'type' => 'poison_cat');

  var $pharmacist = array(
    'message' => "[役割] [|村人|陣営] [#薬師#系]\n　あなたは#薬師#です。_処刑_投票した人を#解毒#するか、#毒#能力を知ることができます。|村人|への二次被害を未然に防ぐのです！",
    'type' => 'poison', 'delimiter' => array('_' => 'vote'));

  var $cure_pharmacist = array(
    'message' => "[役割] [|村人|陣営] [#薬師#系]\n　あなたは#河童#です。_処刑_投票した人を#解毒#しつつ^ショック死^を抑制することができます。\n　一族に伝わる膏薬は人の命を救う霊薬。苦しむ村人を救い、村に笑顔を取り戻すのです。",
    'type' => 'pharmacist', 'delimiter' => array('^' => 'chicken'));

  var $revive_pharmacist = array(
    'message' => "[役割] [|村人|陣営] [#薬師#系]\n　あなたは#仙人#です。_処刑_投票した人の^ショック死^を抑制することができます。また、一度だけ=人狼=に襲撃されても#蘇生#できます。\n　死を超越し、死に拒絶されたあなたは死を否定する力を持っています。その力で村を人狼という理不尽な死から救いだすのです！",
    'type' => 'cure_pharmacist', 'delimiter' => array('=' => 'wolf'));

  var $assassin = array(
    'message' => "[役割] [|村人|陣営] [#暗殺者#系]\n　あなたは#暗殺者#です。夜に村人一人を#暗殺#することができます。闇の内に人外を消し、村の平和の為に暗躍するのです！",
    'type' => 'human', 'delimiter' => array('#' => 'assassin'));

  var $doom_assassin = array(
    'message' => "[役割] [|村人|陣営] [#暗殺者#系]\n　あなたは#死神#です。夜に誰か一人に_死の宣告_を行うことができます。命の灯火を継ぐも絶つもあなた次第です。",
    'type' => 'assassin', 'delimiter' => array('_' => 'chicken'));

  var $reverse_assassin = array(
    'message' => "[役割] [|村人|陣営] [#暗殺者#系]\n　あなたは#反魂師#です。夜に選んだ人が生きていたら#暗殺#し、死んでいたら_蘇生_することができます。\n　あなたの秘術は生死を操る禁忌。夜陰にその力を振るい、村のための舞台を秘密裏に整えるのです！",
    'type' => 'assassin', 'delimiter' => array('_' => 'revive'));

  var $soul_assassin = array(
    'message' => "[役割] [|村人|陣営] [#暗殺者#系]\n　あなたは#辻斬り#です。#暗殺#した人の役職を知ることができますが、_毒_を持っていた場合は死んでしまいます。\n　その手に有りしは、闇に煌く必殺の剣。村を闊歩する悪を暴き出し、討ち果たす事こそがあなたの任務です。",
    'type' => 'assassin', 'delimiter' => array('_' => 'poison'));

  var $mind_scanner = array(
    'message' => "[役割] [|村人|陣営] [#さとり#系]\n　あなたは#さとり#です。誰か一人の心を読むことができます。興味を持った人間の心象を汲み、その本性を暴くのです。",
    'type' => 'human', 'delimiter' => array('#' => 'mind'));

  var $evoke_scanner = array(
    'message' => "[役割] [|村人|陣営] [#さとり#系]\n　あなたは#イタコ#です。誰か一人の心を#口寄せ#を介して読むことができます。先祖伝来の#口寄せ#の力で村を勝利に導くのです。",
    'type' => 'mind_scanner');

  var $whisper_scanner = array(
    'message' => "[役割] [|村人|陣営] [#さとり#系]\n　あなたは#囁騒霊#です。二日目からあなたの夜の独り言が_共有者_にも聞こえるようになります。\n　死んでしまったのは誰？あなたの口ずさむ悲しみを、_共有者_たちにも知ってもらいましょう。",
    'type' => 'mind_scanner', 'delimiter' => array('_' => 'common'));

  var $howl_scanner = array(
    'message' => "[役割] [|村人|陣営] [#さとり#系]\n　あなたは#吠騒霊#です。二日目からあなたの夜の独り言が_人狼_にも聞こえるようになります。\n　生きているのは誰？あなたの紡ぐ言葉で_人狼_の心をざわつかせ、踊ってもらいましょう。",
    'type' => 'mind_scanner', 'delimiter' => array('_' => 'wolf'));

  var $telepath_scanner = array(
    'message' => "[役割] [|村人|陣営] [#さとり#系]\n　あなたは#念騒霊#です。二日目からあなたの夜の独り言が_妖狐_にも聞こえるようになります。\n　どちらでもないのは誰？あなたの知恵が奏でる幻想で、_妖狐_さえも騙して笑いましょう。",
    'type' => 'mind_scanner', 'delimiter' => array('_' => 'fox'));

  var $jealousy = array(
    'message' => "[役割] [|村人|陣営] [#橋姫#系]\n　あなたは#橋姫#です。_恋人_が揃ってあなたに^処刑^投票してきたら=ショック死=させることができます。妬みの力で_恋人_を滅ぼすのです。",
    'type' => 'human',
    'delimiter' => array('#' => 'jealousy', '_' => 'lovers', '^' => 'vote', '=' => 'chicken'));

  var $doll = array(
    'message' => "[役割] [|村人|陣営] [#上海人形#系]\n　あなたは#上海人形#です。あなたは#人形遣い#を倒し、|村人|を勝利に導く必要があります。自由を得るために立ち上がりましょう。",
    'type' => 'human', 'delimiter' => array('#' => 'doll'));

  var $friend_doll = array(
    'message' => "[役割] [|村人|陣営] [#上海人形#系]\n　あなたは#仏蘭西人形#です。同志の#人形#が誰か分かります。あなたは#人形遣い#を倒し、|村人|を勝利に導く必要があります。\n　仲間と協力して自由を勝ち取るのです。勝利は非常に厳しいですが、みんなで頑張れば決して不可能ではありません！",
    'type' => 'doll');

  var $poison_doll = array(
    'message' => "[役割] [|村人|陣営] [#上海人形#系]\n　あなたは#鈴蘭人形#、_毒_を持っています。あなたは#人形遣い#を倒し、|村人|を勝利に導く必要があります。\n　#人形遣い#の存在を凌駕し、遣われる存在ではない事を証明した上で、自らの存在意義を見出すのです。",
    'type' => 'doll', 'delimiter' => array('_' => 'poison'));

  var $doom_doll = array(
    'message' => "[役割] [|村人|陣営] [#上海人形#系]\n　あなたは#蓬莱人形#です。_処刑_されたらあなたに投票した人からランダムで一人に^死の宣告^を行います。\n　あなたは#人形遣い#を倒し、|村人|を勝利に導く必要があります。_処刑_された恨みを呪詛に変えるのです。",
    'type' => 'doll', 'delimiter' => array('_' => 'vote', '^' => 'chicken'));

  var $doll_master = array(
    'message' => "[役割] [|村人|陣営] [#上海人形#系]\n　あなたは#人形遣い#です。_人狼_に襲撃されても他の#人形#を犠牲にして生き延びることができます。\n　#人形#を盾にする力で長生きしやすい立場を活かし、あなたの手腕で村を勝利に導きましょう。",
    'type' => 'doll', 'delimiter' => array('_' => 'wolf'));

  var $brownie = array(
    'message' => "[役割] [|村人|陣営] [#座敷童子#系]\n　あなたは#座敷童子#です。|村人|の_処刑_投票数を +1 することができますが、あなたが_処刑_されたら誰か一人を^熱病^にしてしまいます。\n　その力で村を裕福にしてあげましょう。但しあなたが_処刑_されてしまうとたちまち村に不幸が訪れ、病に伏せる者がでてしまいます。",
    'type' => 'human', 'delimiter' => array('#' => 'brownie', '_' => 'vote', '^' => 'chicken'));

  var $history_brownie = array(
    'message' => "[役割] [|村人|陣営] [#座敷童子#系]\n　あなたは#白澤#です。_人狼_に襲撃されたら次の日の夜を飛ばしてしまいます。どんな悲惨な夜も歴史に残さなければ消えてしまうのです。",
    'type' => 'brownie', 'delimiter' => array('_' => 'wolf'));

  var $wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|人狼|です。夜の間に他の|人狼|と協力し村人一人を殺害できます。あなたはその強力な力で村人を喰い殺すのです！",
    'delimiter' => array('|' => 'wolf'));

  var $boss_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|白狼|です。もう#占い師#を恐れる必要はありません。全てを欺き通して村人たちを皆殺しにするのです！",
    'type' => 'wolf', 'delimiter' => array('#' => 'mage'));

  var $gold_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|金狼|です。#ひよこ鑑定士#に占われると_蝙蝠_と判定されます。_蝙蝠_に疑惑の目を向けさせ、狼の勝利に貢献してもらうのです。",
    'type' => 'boss_wolf', 'delimiter' => array('_' => 'chiroptera'));

  var $phantom_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|幻狼|です。一度だけ#占い#を無効化することができます。幻の|月兎|を想起させ、村を惑わせるのです。",
    'type' => 'boss_wolf');

  var $cursed_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|呪狼|です。あなたを占った#占い師#を呪返しで殺すことができます。村を絶望の底に叩き落してやるのです！",
    'type' => 'boss_wolf');

  var $wise_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|賢狼|です。#妖狐#の念話を感知することができます。人の中に潜む#狐#の吐息を正しく聞き分け、仲間に知らせるのです！",
    'type' => 'wolf', 'delimiter' => array('#' => 'fox'));

  var $poison_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|毒狼|です。たとえ_処刑_されても体内に流れる#毒#で村人一人を道連れにできます。強気に村をかく乱するのです！",
    'type' => 'wolf', 'delimiter' => array('#' => 'poison', '_' => 'vote'));

  var $resist_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|抗毒狼|、一度だけ#毒#に耐えることができます。あなたへの最期の抵抗を凌ぎ、村人たちを恐怖に陥れるのです！",
    'type' => 'poison_wolf');

  var $blue_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|蒼狼|です。襲撃した人が#妖狐#だった場合は_はぐれ者_にすることができます。\n　あなたの牙で念話を噛み切り連携を切り崩し、#妖狐#を烏合の衆にしてしまうのです！",
    'type' => 'wise_wolf', 'delimiter' => array('_' => 'mind'));

  var $emerald_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|翠狼|です。|人狼|を襲撃した場合はあなたと_共鳴者_になります。孤立している仲間と思い通わす心の根を育てるのです！",
    'type' => 'blue_wolf');

  var $sex_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|雛狼|です。襲撃した人の性別を知ることができますが、殺すことはできません。\n　あなたの未熟な襲撃は、小賢しい大人どもの計算を大いに狂わすことができるはずです。",
    'type' => 'wolf');

  var $tongue_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|舌禍狼|です。襲撃した人の役職を知ることができますが、#村人#だった場合は能力を失ってしまいます。\n　殺した能力者の血肉から正体を暴き知ることができる舌の力で、村の全容を把握して全てを手に入れましょう！",
    'type' => 'wolf', 'delimiter' => array('#' => 'human'));

  var $possessed_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|憑狼|です。噛んだ人に憑依することができます。その幽幻の力で肉体を奪い、多くの魂を食い荒らすのです！",
    'type' => 'wolf');

  var $hungry_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|餓狼|です。仲間の|狼|や#妖狐#すら噛み殺せますが、村人は殺せません。強者のみがあなたの獲物なのです。",
    'type' => 'wise_wolf');

  var $doom_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|冥狼|です。襲撃した人に#死の宣告#を行うことができます。|狼|に抗う者に迫り来る死の恐怖を与えて屠るのです。",
    'type' => 'wolf', 'delimiter' => array('#' => 'chicken'));

  var $sirius_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|天狼|です。仲間が減ったら#暗殺#反射、|罠|・_毒_無効、^護衛^突破など、様々な能力が発現します。\n　最後の一人になった時、:処刑:以外であなたを止める手段はありません。孤高の生き様を魅せるのです！",
    'type' => 'wolf',
    'delimiter' => array('#' => 'assassin', '_' => 'poison', '^' => 'guard', ':' => 'vote'));

  var $elder_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|古狼|です。あなたの#処刑#投票には_二票_分の価値があります。老練の弁舌と素知らぬ村人の信頼を盾に、村人を食い殺すのです！",
    'type' => 'wolf', 'delimiter' => array('#' => 'vote', '_' => 'authority'));

  var $cute_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|萌狼|です。ごくまれに発言が遠吠えになってしまいます。バレた時は笑ってごまかしましょう。",
    'type' => 'wolf');

  var $scarlet_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|紅狼|です。#妖狐#にはあなたが仲間であるように見えています。#妖狐#を欺き、その鋭い牙で村を真紅に染め上げるのです！",
    'type' => 'wise_wolf');

  var $silver_wolf = array(
    'message' => "[役割] [|人狼|陣営] [|人狼|系]\n　あなたは|銀狼|です。仲間が誰か分かりませんが、遠吠えで仲間に存在を知らせることはできます。\n　天を灼く満月の下、銀色の毛並みを輝かせて、仲間の群れと共にこの村を狼のものにするのです。",
    'type' => 'wolf');

  var $mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|狂人|です。|人狼|の勝利があなたの勝利となります。あなたはできる限り狂って場をかき乱すのです！",
    'type' => 'wolf');

  var $fanatic_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|狂信者|です。仕えるべき|人狼|が誰なのかを知ることができます。あなたの持てる全てを|人狼|に捧げ尽くすのです！",
    'type' => 'mad');

  var $whisper_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|囁き狂人|です。夜の|人狼|の相談に参加することができます。|人狼|と完璧な連携を組んで村を殲滅するのです！",
    'type' => 'mad');

  var $jammer_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|月兎|です。夜の間に誰か一人の占いを妨害することができます。#占い師#を月の魔性に狂わせ、村を破滅へ導くのです！",
    'type' => 'mad', 'delimiter' => array('#' => 'mage'));

  var $voodoo_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|呪術師|です。夜の間に誰か一人に呪いをかけることができます。#占い師#を呪いで殲滅し、村を混乱に陥れるのです！",
    'type' => 'jammer_mad');

  var $corpse_courier_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|火車|です。あなたが投票した人が#処刑#された場合に限り、その死体を持ち去ることができます。\n　バレないように亡骸を持ち去ることで_霊能者_を無力化し、強敵の死体を収集して村を惑わせるのです！",
    'type' => 'mad', 'delimiter' => array('#' => 'vote', '_' => 'necromancer'));

  var $agitate_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|扇動者|です。#処刑#投票先が拮抗した場合に限り、まとめて死なせることができます。\n　「人外を吊り殺せ！村に平和を！」熱狂する村人の熱意を煽り、巧みに策略を繰るのです！",
    'type' => 'corpse_courier_mad');

  var $miasma_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|土蜘蛛|です。#処刑#投票先が死ななかった場合は_熱病_にさせることができます。\n　身に孕む怨念を悪疫へと変え、村を地獄の釜の底へ叩き込み、悪夢に悩ませるのです。",
    'type' => 'corpse_courier_mad', 'delimiter' => array('_' => 'chicken'));

  var $dream_eater_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|獏|です。夜の間に村人一人の夢を食べることで夢能力者を殺すことができます。\n　天敵たる#夢守人#に注意しながら、夢の世界にいる住人や_妖精_達を食らい尽くすのです！",
    'type' => 'mad', 'delimiter' => array('#' => 'guard', '_' => 'chiroptera'));

  var $trap_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|罠師|です。一度だけ夜に罠を仕掛けることができます。罠を仕掛けた人の元に訪れた能力者は全員死亡します。\n　あなたの魔手は鮮やかな悪夢の芸術を生み出す。|人狼|に害成す者共を狡猾なる罠におびき寄せ、地獄に陥れるのです。",
    'type' => 'mad');

  var $snow_trap_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|雪女|です。夜に、触れた人を#凍傷#にする罠を仕掛けることができます。美しいあなたの銀雪の息吹は数多を凍えさせるのです。",
    'type' => 'mad', 'delimiter' => array('#' => 'chicken'));

  var $possessed_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|犬神|です。一度だけ、死んだ人に憑依することができます。骸を傀儡人形と化し、その怨恨の赴くままに呪詛を撒き散らすのです！",
    'type' => 'mad');

  var $therian_mad = array(
    'message' => "[役割] [|人狼|陣営] [|狂人|系]\n　あなたは|獣人|です。|人狼|に襲撃されると|人狼|に変化します。その身に宿る気高き獣の血を覚醒させ、森羅万象全てを噛み殺すのです！",
    'type' => 'mad');

  var $fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|妖狐|、#人狼#に殺されることはありません。ただし占われると死んでしまいます。\n　村人を騙し、#人狼#を騙し、村を|妖狐|のものにするのです！",
    'delimiter' => array('|' => 'fox', '#' => 'wolf'));

  var $white_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|白狐|です。_占い師_を騙すことができますが、^霊能者^には見抜かれてしまいます。\n　また、#人狼#に襲撃されると殺されてしまいます。巧みに#狼#を欺き、勝利を奪うのです。",
    'type' => 'fox', 'delimiter' => array('_' => 'mage', '^' => 'necromancer'));

  var $black_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|黒狐|です。占われても呪殺されませんが、#人狼#判定が出されるうえに、^霊能者^には見抜かれてしまいます。\n　黒き体色は闇にまぎれ、鮮やかなる虚飾の世界を彩る。あなたの話術で狼をだまし、村に夜の帳を下ろすのです。",
    'type' => 'white_fox');

  var $gold_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|金狐|です。_ひよこ鑑定士_に占われると^蝙蝠^と判定されます。^蝙蝠^に村や狼の矛先を向けさせ、その隙に狐の勝利を頂くのです！",
    'type' => 'white_fox', 'delimiter' => array('^' => 'chiroptera'));

  var $phantom_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|幻狐|です。一度だけ_占い_を無効化することができます。_占い師_を煙に巻き、村を幻へと誘うのです。",
    'type' => 'white_fox');

  var $poison_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|管狐|、#毒#を持っています。身に蓄えし災いを以てあなたを亡き者にしようとする者共に禍をもたらすのです！",
    'type' => 'fox', 'delimiter' => array('#' => 'poison'));

  var $blue_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|蒼狐|です。あなたを襲撃した#人狼#を_はぐれ者_にすることができます。\n　返す刀で遠吠えを引き裂き仲間と切り離し、#人狼#の群れを瓦解させるのです！",
    'type' => 'fox', 'delimiter' => array('_' => 'mind'));

  var $emerald_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|翠狐|です。占った人が念話できない|妖狐|だった場合はあなたと_共鳴者_になります。\n　気持ちを通わす心の根を張り巡らせて、仲間と連携してこの村を妖狐のものとするのです！",
    'type' => 'blue_fox');

  var $voodoo_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|九尾|です。夜の間に誰か一人に呪いをかけることができます。|妖狐|の天敵、_占い師_を呪返しで葬るのです！",
    'type' => 'white_fox');

  var $revive_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|仙狐|です。一度だけ、死んだ人を誰か一人ほぼ確実に蘇らせる事ができます。\n　繁栄を司るその力で、今まで散々不敬を働いた村人たちに恐怖の鉄槌を下すのです！",
    'type' => 'fox');

  var $possessed_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|憑狐|です。一度だけ、死んだ人に憑依することができます。語らぬ骸を纏い、あなたの変化で村と狼を騙すのです！",
    'type' => 'fox');

  var $doom_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|冥狐|です。夜に誰か一人に#死の宣告#を行うことができます。村の支配者は誰なのか、死を以て教えてやるのです。",
    'type' => 'fox', 'delimiter' => array('#' => 'chicken'));

  var $cursed_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|天狐|です。_占い師_を呪返しで、#暗殺#を反射で撃退することができますが、^狩人^には殺されてしまいます。\n　偽りを身に纏い、話術を飾りに舞うように村を駆け巡り、網の目のように張り巡らせた策謀の上を踊るのです！",
    'type' => 'white_fox', 'delimiter' => array('#' => 'assassin', '^' => 'guard'));

  var $cute_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|萌狐|です。ごくまれに発言が遠吠えになってしまいます。バレた時は笑ってごまかしましょう。",
    'type' => 'fox');

  var $elder_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|古狐|です。あなたの#処刑#投票には_二票_分の価値があります。経年で得た神通力と村人の信頼を活かして、村を支配するのです。",
    'type' => 'fox', 'delimiter' => array('#' => 'vote', '_' => 'authority'));

  var $scarlet_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|紅狐|です。#人狼#にはあなたが_無意識_であるように見えています。無力な村人を装って#人狼#を欺くのです。",
    'type' => 'fox', 'delimiter' => array('_' => 'human'));

  var $silver_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|妖狐|系]\n　あなたは|銀狐|です。仲間を誰も知ることができず、仲間もあなたのことを知りません。\n　月下の雪原に煌めく銀の毛皮を身に纏い、孤独であっても村を狐のものにするのです。",
    'type' => 'fox');

  var $child_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|子狐|系]\n　あなたは|子狐|です。占われても死にませんが、#人狼#に襲われると死んでしまいます。また、時々失敗しますが_占い_の真似事もできます。\n　例え子供でも|狐|は|狐|。子供ならば子供としての戦い方をするだけです。父や母すら利用する、立派な|狐|になって村を支配しましょう。",
    'type' => 'fox', 'delimiter' => array('_' => 'mage'));

  var $sex_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|子狐|系]\n　あなたは|雛狐|です。占われても死にませんが、#人狼#に襲われると死んでしまいます。また、時々失敗しますが_ひよこ鑑定士_の真似事もできます。\n　^蝙蝠^を見抜く目は仲間の助けにならないかもしれません。しかし、奇妙にも大人たちの曇った瞳には無実な人があなたに見えるようなのです。",
    'type' => 'child_fox', 'delimiter' => array('^' => 'chiroptera'));

  var $stargazer_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|子狐|系]\n　あなたは|星狐|です。占われても死にませんが、#人狼#に襲われると死んでしまいます。また、時々失敗しますが_占星術師_の真似事もできます。\n　あなたは星に願いを込めた|狐|。たまに願わないことがあってもその想いを胸に刻みつつ、夜には静かに動き、村を動かす人を探るのです。",
    'type' => 'child_fox');

  var $jammer_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|子狐|系]\n　あなたは|月狐|です。占われても死にませんが、#人狼#に襲われると死んでしまいます。また、時々失敗しますが#月兎#の真似事もできます。\n　月の光は占いの効果を歪め覆い隠す力を持つ。|子狐|でありながら身に付けたその能力を用いて、呪殺の脅威から|狐|を守り抜くのです。",
    'type' => 'child_fox');

  var $miasma_fox = array(
    'message' => "[役割] [|妖狐|陣営] [|子狐|系]\n　あなたは|蟲狐|です。_処刑_されるか#人狼#に襲われたら、あなたを死なせた人からランダムで一人を^熱病^にさせることができます。\n　あなたは蟲毒をもった呪いの|狐|。幾百もの怨念が宿りしあなたを殺めた者は、死に至る無間の苦しみにのたうつことでしょう。",
    'type' => 'child_fox', 'delimiter' => array('_' => 'vote', '^' => 'chicken'));

  var $cupid = array(
    'message' => "[役割] [|恋人|陣営] [|キューピッド|系]\n　あなたは|キューピッド|です。初日の夜に誰か二人を|恋人|同士にすることができます。\n　愛しあう二人を影から支え、何物にも勝る愛の素晴らしさを村に知らしめるのです！",
    'delimiter' => array('|' => 'lovers'));

  var $self_cupid = array(
    'message' => "[役割] [|恋人|陣営] [|キューピッド|系]\n　あなたは|求愛者|です。初日の夜に自分と誰か一人を|恋人|同士にすることができます。自分の幸せは自分で掴み取るのです！",
    'type' => 'cupid');

  var $moon_cupid = array(
    'message' => "[役割] [|恋人|陣営] [|キューピッド|系]\n　あなたは|かぐや姫|です。初日の夜に自分と誰か一人を|恋人|同士にして、さらに|難題|を与えることができます。\n　月に戻ることを忘れる程の素敵な恋を見つけられることを祈ってます。|恋人|と|難題|を乗り越えるのです！",
    'type' => 'cupid');

  var $mind_cupid = array(
    'message' => "[役割] [|恋人|陣営] [|キューピッド|系]\n　あなたは|女神|です。初日の夜に誰か二人を#共鳴者#つきの|恋人|にすることができます。\n　夜に囁く愛の言葉をいつでも交わしあえる、愛に溢れた平和な村を創りだすのです！",
    'type' => 'cupid', 'delimiter' => array('#' => 'mind'));

  var $triangle_cupid = array(
    'message' => "[役割] [|恋人|陣営] [|キューピッド|系]\n　あなたは|小悪魔|です。初日の夜に誰か三人を|恋人|にしてしまいます。恋とはまた盲目。盲目なる|恋人|に、災いと嘲りあれ。",
    'type' => 'cupid');

  var $angel = array(
    'message' => "[役割] [|恋人|陣営] [|天使|系]\n　あなたは|天使|です。初日の夜に誰か二人を|恋人|にすることができます。その二人が_男_|女|だった場合はさらに#共感者#にすることができます。\n　始まりの園にありし男女の愛を繋ぐように、男女の魂に秘蹟を授けるのがあなたの使命なのです。二人の人生の先に神の祝福と喜びあれ。",
    'type' => 'cupid',
    'delimiter' => array('#' => 'mind', '_' => 'sex_male'));

  var $rose_angel = array(
    'message' => "[役割] [|恋人|陣営] [|天使|系]\n　あなたは|薔薇天使|です。初日の夜に誰か二人を|恋人|にすることができます。その二人が_男性_同士だった場合はさらに#共感者#にすることができます。\n　紅を帯びし薔薇の蕾を手に、戦地に有りし男たちの魂を繋ぐ事こそがあなたの存在意義。兜が置かれるその時こそ、平和の鐘が響く時なのです。",
    'type' => 'angel');

  var $lily_angel = array(
    'message' => "[役割] [|恋人|陣営] [|天使|系]\n　あなたは|百合天使|です。初日の夜に誰か二人を|恋人|にすることができます。その二人が|女性|同士だった場合はさらに#共感者#にすることができます。\n　黄の山百合を手に、純潔なる乙女たちの魂を繋ぐのがあなたの使命。失われし楽園への道を示し、ともに歩むのです。乙女たちに神の祝福あれ。",
    'type' => 'angel');

  var $exchange_angel = array(
    'message' => "[役割] [|恋人|陣営] [|天使|系]\n　あなたは|魂移使|です。初日の夜に誰か二人を|恋人|にすることができます。さらに、#共感者#にして二人の精神を入れ替えてしまいます。\n　|恋人|の強い絆は魂をも愛の奔流に飲み込みます。愛する人をゆっくりとみつめる機会を与えることで、真実の愛を教えるのです！",
    'type' => 'angel');

  var $ark_angel = array(
    'message' => "[役割] [|恋人|陣営] [|天使|系]\n　あなたは|大天使|です。初日の夜に誰か二人を|恋人|にすることができます。また、他の|天使|が作った#共感者#を知ることができます。\n　秘蹟を統べる者――神に授けられたその力で村に神の存在を知らしめるのです。神を認める者には祝福を、認めぬ者には制裁を。",
    'type' => 'angel');

  var $quiz = array(
    'message' => "[役割] [|出題者|陣営] [|出題者|系]\n　あなたは|出題者|です。この村の難易度はあなたの口先三寸で決まります。頑張って皆を楽しませれば、それがあなたの勝利です。",
    'delimiter' => array('|' => 'quiz'));

  var $vampire = array(
    'message' => "[役割] [|吸血鬼|陣営] [|吸血鬼|系]\n　あなたは|吸血鬼|です。夜に誰か一人を|感染者|にすることができます。生きている人全てをあなたの|感染者|にすると勝利できます。\n　夜の闇にまぎれ、誰にも知られぬまま血をすすり、眷属を増やすのです。真の支配者はあなただと言う事を村に知らしめましょう。",
    'delimiter' => array('|' => 'vampire'));

  var $chiroptera = array(
    'message' => "[役割] [|蝙蝠|陣営] [|蝙蝠|系]\n　あなたは|蝙蝠|です。生き残りましょう。ただそれだけで勝ちになります。\n　勝ち馬に乗り、いずれの存在からも疎まれながらも強く生き抜くのです。",
    'delimiter' => array('|' => 'chiroptera'));

  var $poison_chiroptera = array(
    'message' => "[役割] [|蝙蝠|陣営] [|蝙蝠|系]\n　あなたは|毒蝙蝠|、#毒#を持っています。この#毒#は村にとって便利な道具です。正体が知られたら道具として死体を晒すことでしょう。",
    'type' => 'chiroptera', 'delimiter' => array('#' => 'poison'));

  var $cursed_chiroptera = array(
    'message' => "[役割] [|蝙蝠|陣営] [|蝙蝠|系]\n　あなたは|呪蝙蝠|です。あなたを占った#占い師#を呪返しで殺すことができます。混乱する村を尻目にしたたかに生き延びるのです。",
    'type' => 'chiroptera', 'delimiter' => array('#' => 'mage'));

  var $boss_chiroptera = array(
    'message' => "[役割] [|蝙蝠|陣営] [|蝙蝠|系]\n　あなたは|大蝙蝠|です。#人狼#に襲撃されても他の|蝙蝠|を犠牲にして生き延びることができます。\n　誇りを捨て情を捨て、同族の命すら糧にして、ただただ生き残ることだけを考えるのです。",
    'type' => 'chiroptera', 'delimiter' => array('#' => 'wolf'));

  var $elder_chiroptera = array(
    'message' => "[役割] [|蝙蝠|陣営] [|蝙蝠|系]\n　あなたは|古蝙蝠|です。あなたの#処刑#投票には_二票_分の価値があります。若造たちに真理を教えてやりましょう。数は力なり、と。",
    'type' => 'chiroptera', 'delimiter' => array('#' => 'vote', '_' => 'authority'));

  var $fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|妖精|です。村人一人の発言に#共有者#の囁きを追加してしまいます。\n　仕事に忙しい四季の仲間達を尻目に、悪戯で村人をからかって遊ぶのです。",
    'type' => 'chiroptera', 'delimiter' => array('#' => 'common'));

  var $spring_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|春妖精|です。村人一人の発言に春を告げるメッセージを追加してしまいます。\n　青春―青き芽を付ける春の訪れを村人たちに告げ、|夏妖精|へとバトンを繋ぐのです。",
    'type' => 'fairy');

  var $summer_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|夏妖精|です。村人一人の発言に夏を告げるメッセージを追加してしまいます。\n　朱夏―情熱燃え上がる夏の訪れを村人たちに告げ、|秋妖精|へとバトンを繋ぎましょう。",
    'type' => 'fairy');

  var $autumn_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|秋妖精|です。村人一人の発言に秋を告げるメッセージを追加してしまいます。\n　白秋――落葉し木が休む秋の訪れを村人たちに告げ、|冬妖精|へとバトンを繋ぐのです。",
    'type' => 'fairy');

  var $winter_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|冬妖精|です。村人一人の発言に冬を告げるメッセージを追加してしまいます。\n　幻冬――全ての生き物が眠る冬の訪れを村人に告げ、|春妖精|へとバトンを繋ぐのです。",
    'type' => 'fairy');

  var $flower_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|花妖精|です。村人一人の頭の上に花を咲かせることができます。\n　特に効力は有りませんが、頭に花の咲いた者達をからかってやるのです！",
    'type' => 'fairy');

  var $star_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|星妖精|です。夜に村人一人を指定して、その人がどんな星座を見ていたか、みんなに知らせることができます。\n　ロマンチックな気分に浸る村人を笑ってやりましょう。え、笑いの種にならないだろうって？細かいことは気にしない。",
    'type' => 'fairy');

  var $sun_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|日妖精|です。夜に村人一人を指定して、その人が#人狼#に襲撃されたら次の日を全員_光学迷彩_にしてしまいます。\n　目が眩んで慌てふためいている村人達を笑ってやりましょう。自分の目も眩んでるので少し見え辛いかもしれませんが。",
    'type' => 'fairy', 'delimiter' => array('#' => 'wolf', '_' => 'liar'));

  var $moon_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|月妖精|です。夜に村人一人を指定して、その人が#人狼#に襲撃されたら次の日を全員_耳栓_にしてしまいます。\n　耳が遠くなって混乱している村人達を笑ってやりましょう。自分の耳も遠いので少し聞こえ辛いかもしれませんが。",
    'type' => 'sun_fairy', 'delimiter' => array('_' => 'no_last_words'));

  var $grass_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|草妖精|です。夜に村人一人を指定して、その人が#人狼#に襲撃されたら次の日を全員_草原迷彩_にしてしまいます。\n　どw うw しw てw だw ろw うw 、w 草w がw 生w えw るw だw けw でw みw んw なw 笑w 顔w にw なw れw るw んw だw",
    'type' => 'sun_fairy');

  var $light_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|光妖精|です。夜に村人一人を指定して、その人が#人狼#に襲撃されたら次の日を全員_公開者_にしてしまいます。\n　あなたがいる限り、村に本当の夜は来ません。思う存分夜更かしを楽しみましょう。人外には嫌われると思いますが。",
    'type' => 'sun_fairy', 'delimiter' => array('_' => 'mind'));

  var $dark_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|闇妖精|です。夜に村人一人を指定して、その人が#人狼#に襲撃されたら次の日を全員_目隠し_にしてしまいます。\n　誰が誰かも分からない、真っ暗闇の中。慌てふためく間抜けな村人たちを、心ゆくまでからかい倒してやりましょう！",
    'type' => 'moon_fairy');

  var $ice_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|氷妖精|です。夜に村人一人を指定して、その人を#凍傷#にしてしまいます。たまに自分に跳ね返ることがあります。\n　妖精として力強く生き残るために、あなたが敵だとみなした相手には、自由にさせないように悪戯して追い払いましょう。",
    'type' => 'fairy', 'delimiter' => array('#' => 'chicken'));

  var $mirror_fairy = array(
    'message' => "[役割] [|蝙蝠|陣営] [|妖精|系]\n　あなたは|鏡妖精|です。初日の夜に誰か二人を指名して、自分が吊られたら次の日の投票先をその二人に限定してしまいます。\n　鏡界――合わせ鏡の無限次元に佇みながら、鏡の世界を体に埋め込むその力。姿見に自らを写しつつ、村人は何を思うのか。",
    'type' => 'fairy');

  var $mania = array(
    'message' => "[役割] [|神話マニア|陣営] [|神話マニア|系]\n　あなたは|神話マニア|です。初日の夜に指定した人のメイン役職をコピーすることができます。\n　星の数ほどある神話。誰を相手取るかによって何が最も適切なのかを的確に選び取るのです。",
    'delimiter' => array('|' => 'mania'));

  var $trick_mania = array(
    'message' => "[役割] [|神話マニア|陣営] [|神話マニア|系]\n　あなたは|奇術師|です。初日の夜に指定した人が何もしていなければ役職を奪うことができます。\n　奪い取った相手の能力、その力を使いこなして魅せるのです。#村人#表示でも絶対に泣きません。",
    'type' => 'mania', 'delimiter' => array('#' => 'human'));

  var $soul_mania = array(
    'message' => "[役割] [|神話マニア|陣営] [|神話マニア|系]\n　あなたは|覚醒者|です。初日の夜に指定した人と関連した能力に後日、目覚める事になります。\n　数日間、自らの内でその能力を育み、より強き力を持ったものとして新たに君臨するのです！",
    'type' => 'mania');

  var $unknown_mania = array(
    'message' => "[役割] [|神話マニア|陣営] [|神話マニア|系]\n　あなたは|鵺|です。初日の夜に指定した人と同じ陣営になり、二日目夜からお互いに会話できます。\n　 ――鵺は二つの面を持っている。人側の側面と獣側の側面だ。正面から見ては？正体不明だ。",
    'type' => 'mania');

  var $chicken = array('message' => "　あなたは|小心者|です。#処刑#投票時に一票でも貰うと|ショック死|してしまいます。",
		       'delimiter' => array('|' => 'chicken', '#' => 'vote'));

  var $rabbit = array('message' => "　あなたは|ウサギ|です。#処刑#投票時に一票も貰えないと|ショック死|してしまいます。",
		      'type' => 'chicken');

  var $perverseness = array('message' => "　あなたは|天邪鬼|です。#処刑#投票時に自分と同じ投票先の人がいると|ショック死|してしまいます。",
			    'type' => 'chicken');

  var $flattery = array('message' => "　あなたは|ゴマすり|です。#処刑#投票時に自分と同じ投票先の人がいないと|ショック死|してしまいます。",
			'type' => 'chicken');

  var $impatience = array('message' => "　あなたは|短気|です。_決定_力がありますが、#再投票#になると|ショック死|してしまいます。",
			  'type' => 'chicken', 'delimiter' => array('_' => 'decide'));

  var $celibacy = array('message' => "　あなたは|独身貴族|です。_恋人_に#処刑#投票されると|ショック死|してしまいます。",
			'type' => 'chicken', 'delimiter' => array('_' => 'lovers'));

  var $nervy = array('message' => "　あなたは|自信家|です。自分と同じ陣営の人に#処刑#投票にすると|ショック死|してしまいます。",
		     'type' => 'chicken');

  var $androphobia = array('message' => "　あなたは|男性恐怖症|です。_男性_に#処刑#投票すると|ショック死|してしまいます。",
			   'type' => 'chicken', 'delimiter' => array('_' => 'sex_male'));

  var $gynophobia = array('message' => "　あなたは|女性恐怖症|です。_女性_に#処刑#投票すると|ショック死|してしまいます。",
			  'type' => 'chicken', 'delimiter' => array('_' => 'lovers'));

  var $panelist = array('message' => "　あなたは|解答者|です。不正解だったときは_出題者_に#処刑#投票してください。",
			'type' => 'chicken', 'delimiter' => array('_' => 'quiz'));

  var $febris_header = array('message' => "　あなたは|熱病|にかかっています。",
			     'type' => 'chicken');

  var $frostbite_header = array('message' => "　あなたは|凍傷|にかかっています。",
				'type' => 'chicken');

  var $frostbite_footer = array('message' => "日目の昼の#処刑#投票時に一票も貰えないと|ショック死|してしまいます。",
				'type' => 'chicken');

  var $death_warrant_header = array('message' => "　あなたは|死の宣告|を受けています。",
				    'type' => 'chicken');

  var $sudden_death_footer = array('message' => "日目の昼に|ショック死|してしまいます。",
				   'type' => 'chicken');

  var $liar = array('message' => "　あなたは|狼少年|です。「人」と「#狼#」をわざと取り違えて発言してしまいます。",
		    'delimiter' => array('|' => 'liar', '#' => 'wolf'));

  var $invisible = array('message' => "　あなたは|光学迷彩|を使っているので発言の一部が見えなくなります。",
			 'type' => 'liar');

  var $rainbow = array('message' => "　あなたは|虹色迷彩|を使っているので虹の順番に合わせて色を入れ替えて発言してしまいます。",
		       'type' => 'liar');

  var $weekly = array('message' => "　あなたは|七曜迷彩|を使っているので曜日の順番に合わせて曜日を入れ替えて発言してしまいます。",
		      'type' => 'liar');

  var $grassy = array('message' => "　あなたは|草原迷彩|を使っているので発言が草に埋もれてしまいます。",
		      'type' => 'liar');

  var $side_reverse = array('message' => "　あなたは|鏡面迷彩|を使っているので発言の左右が反転してしまいます。",
			    'type' => 'liar');

  var $line_reverse = array('message' => "　あなたは|天地迷彩|を使っているので発言の上下が反転してしまいます。",
			    'type' => 'liar');

  var $gentleman = array('message' => "　あなたは|紳士|です。時々紳士な発言をしてしまいます。",
			 'type' => 'liar');

  var $lady = array('message' => "　あなたは|淑女|です。時々淑女な発言をしてしまいます。",
		    'type' => 'liar');

  var $actor = array('message' => "　あなたは|役者|です。あらかじめ設定された RP を演じてもらうことになります。",
		     'type' => 'liar');

  var $authority = array('message' => "　あなたは|権力者|です。あなたの#処刑#投票は|二票|分の効果があります。",
			 'delimiter' => array('|' => 'authority', '#' => 'vote'));

  var $rebel = array('message' => "　あなたは|反逆者|です。|権力者|と同じ人に#処刑#投票した場合、あなたと|権力者|の投票数が０になります。",
		     'type' => 'authority');

  var $random_voter = array('message' => "　あなたは|気分屋|なので、#処刑#投票数にランダムで補正がかかります。",
			    'type' => 'authority');

  var $watcher = array('message' => "　あなたは|傍観者|です。#処刑#投票を行っても|０票|と扱われてしまいます。",
		       'type' => 'authority');

  var $upper_luck = array('message' => "　あなたは|雑草魂|の持ち主です。最初の#処刑#投票の#得票#が４増える代わりにそれ以降は２減ります。",
			  'delimiter' => array('|' => 'luck', '#' => 'vote'));

  var $downer_luck = array('message' => "　あなたは|一発屋|です。最初の#処刑#投票の#得票#が４減る代わりにそれ以降は２増えます。",
			   'type' => 'upper_luck');

  var $star = array('message' => "　あなたは|人気者|なので#得票#数が１減ります。",
		    'type' => 'upper_luck');

  var $disfavor = array('message' => "　あなたは|不人気|なので#得票#数が１増えます。",
			'type' => 'upper_luck');

  var $random_luck = array('message' => "　あなたは|波乱万丈|なので#得票#数にランダムで補正がかかります。",
			   'type' => 'upper_luck');

  var $strong_voice = array('message' => "　あなたは|大声|の持ち主です。上手に活用して皆を説得しましょう！",
			    'delimiter' => array('|' => 'voice'));

  var $normal_voice = array('message' => "　あなたは|不器用|なので声の大きさを変えられません。",
			    'type' => 'strong_voice');

  var $weak_voice = array('message' => "　あなたは|小声|の持ち主です。説得に苦労するかもしれませんが頑張ってください。",
			  'type' => 'strong_voice');

  var $inside_voice = array('message' => "　あなたは|内弁慶|なので昼は|小声|に、夜は|大声|になります。",
			    'type' => 'strong_voice');

  var $outside_voice = array('message' => "　あなたは|外弁慶|なので昼は|大声|に、夜は|小声|になります。",
			     'type' => 'strong_voice');

  var $upper_voice = array('message' => "　あなたは|メガホン|を使っているので声が一段階大きくなります。|大声|は音割れしてしまいます。",
			   'type' => 'strong_voice');

  var $downer_voice = array('message' => "　あなたは|マスク|をつけているので声が一段階小さくなります。|小声|は聞き取れなくなってしまいます。",
			    'type' => 'strong_voice');

  var $random_voice = array('message' => "　あなたは|臆病者|です。この事態に混乱するあなたは声の大きさが安定しません。",
			    'type' => 'strong_voice');

  var $no_last_words = array('message' => "　あなたは|筆不精|なので遺言を遺すことができません。",
			     'delimiter' => array('|' => 'no_last_words'));

  var $blinder = array('message' => "　あなたは|目隠し|をしているので発言者の名前が見えません。",
		       'type' => 'no_last_words');

  var $earplug = array('message' => "　あなたは|耳栓|をつけているので声が一段階小さく聞こえます。#小声#は聞き取れません。",
		       'type' => 'no_last_words', 'delimiter' => array('#' => 'voice'));

  var $speaker = array('message' => "　あなたは|スピーカー|を使っているので声が一段階大きく聞こえます。#大声#は音割れしてしまいます。",
		       'type' => 'earplug');

  var $whisper_ringing = array('message' => "　あなたは|囁耳鳴|なので他人の独り言が#共有者#の囁きに聞こえてしまいます。",
			       'type' => 'no_last_words', 'delimiter' => array('#' => 'common'));

  var $howl_ringing = array('message' => "　あなたは|吠耳鳴|なので他人の独り言が#人狼#の遠吠えに聞こえてしまいます。",
			    'type' => 'no_last_words', 'delimiter' => array('#' => 'wolf'));

  var $deep_sleep = array('message' => "　あなたは|爆睡者|なので#共有者#の囁きや、_人狼_の遠吠えが聞こえません。",
			  'type' => 'no_last_words',
			  'delimiter' => array('#' => 'common', '_' => 'wolf'));

  var $silent = array('message' => "　あなたは|無口|なのであまり多くの言葉を話せません。",
		      'type' => 'no_last_words');

  var $mower = array('message' => "　あなたは|草刈り|なので発言から草が刈り取られてしまいます。",
		     'type' => 'no_last_words');

  var $mind_read = array('message' => "　あなたは|サトラレ|です。夜の発言が|さとり|に読まれてしまいます。",
			 'delimiter' => array('|' => 'mind'));

  var $mind_receiver = array('message' => "　あなたは|受信者|です。夜の間だけ誰かの発言を読み取ることができます。",
			     'type' => 'mind_read');

  var $mind_friend = array('message' => "　あなたは|共鳴者|です。夜の間だけ|共鳴者|同士で会話することができます。",
			   'type' => 'mind_read');

  var $mind_sympathy = array('message' => "　あなたは|共感者|です。もう一人の|共感者|の役職を知ることができます",
			     'type' => 'mind_read');

  var $mind_open = array('message' => "　あなたは|公開者|です。二日目以降、夜の発言が全員に見えます。気をつけましょう。",
			 'type' => 'mind_read');

  var $mind_evoke = array('message' => "　あなたは|イタコ|に|口寄せ|されています。死んだ後に遺言を介して|イタコ|にメッセージを送ることができます。",
			  'type' => 'mind_read');

  var $mind_lonely = array('message' => "　あなたは|はぐれ者|なので仲間と会話できません。",
			   'type' => 'mind_read');

  var $ability_poison = array('message' => "　あなたは|毒|を持っています。#処刑#されたり、_人狼_に襲撃された時に誰か一人を道連れにします。",
			      'delimiter' => array('|' => 'poison', '#' => 'vote', '_' => 'wolf'));

  var $ability_sirius_wolf = array('message' => "　残りの|狼|が二人になりました。人の繰り出す業 (#暗殺#・|罠|) は、もはやあなたを貫けません。",
				   'type' => 'sirius_wolf');

  var $ability_full_sirius_wolf = array('message' => "　あなたが最後の|狼|です。今や天に輝く|狼|となったあなたに、噛めないものはあんまりない。",
					'type' => 'sirius_wolf');

  var $challenge_lovers = array('message' => "　あなたは|難題|に挑戦しています。5日目昼になるまでは#人狼#の襲撃・_暗殺_・^毒^などを無効化できますが\n　それ以降は能力を失う上に、|恋人|の相方と:処刑:投票先を合わせないと=ショック死=してしまいます。",
				'delimiter' => array('|' => 'lovers', '#' => 'wolf', '_' => 'assassin',
						     '^' => 'poison', ':' => 'vote', '=' => 'chicken'));

  var $lost_ability = array('message' => "　あなたは能力を失いました。");

  var $common_partner = array('message' => "同じ|共有者|の仲間は以下の人たちです： ",
			      'delimiter' => array('|' => 'common'));

  var $mind_scanner_target = array('message' => "あなたが|心を読んでいる|のは以下の人たちです： ",
				   'type' => 'mind_read');

  var $mind_friend_list = array('message' => "あなたと|共鳴|しているのは以下の人たちです： ",
				'type' => 'mind_read');

  var $doll_master_list = array('message' => "あなたを呪縛する|人形遣い|は以下の人たちです： ",
				'delimiter' => array('|' => 'doll'));

  var $doll_partner = array('message' => "|人形遣い|打倒を目指す同志は以下の人たちです： ",
			    'type' => 'doll_master_list');

  var $wolf_partner = array('message' => "誇り高き|人狼|の血を引く仲間は以下の人たちです： ",
			    'delimiter' => array('|' => 'wolf'));

  var $mad_partner = array('message' => "|人狼|に仕える|狂人|は以下の人たちです： ",
			   'type' => 'wolf_partner');

  var $unconscious_list = array('message' => "以下の人たちが|無意識|に歩き回っているようです： ",
				'delimiter' => array('|' => 'human'));

  var $fox_partner = array('message' => "深遠なる|妖狐|の智を持つ同胞は以下の人たちです： ",
			   'delimiter' => array('|' => 'fox'));

  var $child_fox_partner = array('message' => "|妖狐|に与する仲間は以下の人たちです： ",
				 'type' => 'fox_partner');

  var $cupid_pair = array('message' => "あなたが|愛の矢|を放ったのは以下の人たちです： ",
			  'delimiter' => array('|' => 'lovers'));

  var $partner_header = array('message' => "あなたは");

  var $lovers_footer = array('message' => "と|愛し合って|います。妨害する者は誰であろうと消し、二人の愛の世界を築くのです！",
			     'type' => 'cupid_pair');

  var $quiz_chaos = array('message' => "　闇鍋モードではあなたの最大の能力である噛み無効がありません。\n　はっきり言って無理ゲーなので好き勝手にクイズでも出して遊ぶと良いでしょう。",
			  'delimiter' => array());

  var $infected_list = array('message' => "あなたの血に|感染|したのは以下の人たちです： ",
			     'delimiter' => array('|' => 'vampire'));

  var $result_human = array('message' => "さんは|村人|でした", 'delimiter' => array('|' => 'human'));
  var $result_elder = array('message' => "さんは|長老|でした", 'type' => 'result_human');
  var $result_saint = array('message' => "さんは|聖女|でした", 'type' => 'result_human');
  var $result_executor = array('message' => "さんは|執行者|でした", 'type' => 'result_human');
  var $result_escaper = array('message' => "さんは|逃亡者|でした", 'type' => 'result_human');
  var $result_suspect = array('message' => "さんは|不審者|でした", 'type' => 'result_human');
  var $result_unconscious = array('message' => "さんは|無意識|でした", 'type' => 'result_human');
  var $result_mage = array('message' => "さんは|占い師|でした", 'delimiter' => array('|' => 'mage'));
  var $result_soul_mage = array('message' => "さんは|魂の占い師|でした", 'type' => 'result_mage');
  var $result_psycho_mage = array('message' => "さんは|精神鑑定士|でした", 'type' => 'result_mage');
  var $result_sex_mage = array('message' => "さんは|ひよこ鑑定士|でした", 'type' => 'result_mage');
  var $result_stargazer_mage = array('message' => "さんは|占星術師|でした", 'type' => 'result_mage');
  var $result_voodoo_killer = array('message' => "さんは|陰陽師|でした", 'type' => 'result_mage');
  var $result_dummy_mage = array('message' => "さんは|夢見人|でした", 'type' => 'result_mage');
  var $result_necromancer = array('message' => "さんは|霊能者|でした", 'delimiter' => array('|' => 'necromancer'));
  var $result_soul_necromancer = array('message' => "さんは|雲外鏡|でした", 'type' => 'result_necromancer');
  var $result_yama_necromancer = array('message' => "さんは|閻魔|でした", 'type' => 'result_necromancer');
  var $result_dummy_necromancer = array('message' => "さんは|夢枕人|でした", 'type' => 'result_necromancer');
  var $result_medium = array('message' => "さんは|巫女|でした", 'delimiter' => array('|' => 'medium'));
  var $result_seal_medium = array('message' => "さんは|封印師|でした", 'type' => 'result_medium');
  var $result_revive_medium = array('message' => "さんは|風祝|でした", 'type' => 'result_medium');
  var $result_priest = array('message' => "さんは|司祭|でした", 'delimiter' => array('|' => 'priest'));
  var $result_bishop_priest = array('message' => "さんは|司教|でした", 'type' => 'result_priest');
  var $result_dowser_priest = array('message' => "さんは|探知師|でした", 'type' => 'result_priest');
  var $result_border_priest = array('message' => "さんは|境界師|でした", 'type' => 'result_priest');
  var $result_crisis_priest = array('message' => "さんは|預言者|でした", 'type' => 'result_priest');
  var $result_revive_priest = array('message' => "さんは|天人|でした", 'type' => 'result_priest');
  var $result_dummy_priest = array('message' => "さんは|夢司祭|でした", 'type' => 'result_priest');
  var $result_guard = array('message' => "さんは|狩人|でした", 'delimiter' => array('|' => 'guard'));
  var $result_hunter_guard = array('message' => "さんは|猟師|でした", 'type' => 'result_guard');
  var $result_blind_guard = array('message' => "さんは|夜雀|でした", 'type' => 'result_guard');
  var $result_poison_guard = array('message' => "さんは|騎士|でした", 'type' => 'result_guard');
  var $result_fend_guard = array('message' => "さんは|忍者|でした", 'type' => 'result_guard');
  var $result_reporter = array('message' => "さんは|ブン屋|でした", 'type' => 'result_guard');
  var $result_anti_voodoo = array('message' => "さんは|厄神|でした", 'type' => 'result_guard');
  var $result_dummy_guard = array('message' => "さんは|夢守人|でした", 'type' => 'result_guard');
  var $result_common = array('message' => "さんは|共有者|でした", 'delimiter' => array('|' => 'common'));
  var $result_detective_common = array('message' => "さんは|探偵|でした", 'type' => 'result_common');
  var $result_trap_common = array('message' => "さんは|策士|でした", 'type' => 'result_common');
  var $result_ghost_common = array('message' => "さんは|亡霊嬢|でした", 'type' => 'result_common');
  var $result_dummy_common = array('message' => "さんは|夢共有者|でした", 'type' => 'result_common');
  var $result_poison = array('message' => "さんは|埋毒者|でした", 'delimiter' => array('|' => 'poison'));
  var $result_strong_poison = array('message' => "さんは|強毒者|でした", 'type' => 'result_poison');
  var $result_incubate_poison = array('message' => "さんは|潜毒者|でした", 'type' => 'result_poison');
  var $result_guide_poison = array('message' => "さんは|誘毒者|でした", 'type' => 'result_poison');
  var $result_chain_poison = array('message' => "さんは|連毒者|でした", 'type' => 'result_poison');
  var $result_dummy_poison = array('message' => "さんは|夢毒者|でした", 'type' => 'result_poison');
  var $result_poison_cat = array('message' => "さんは|猫又|でした", 'type' => 'result_poison');
  var $result_revive_cat = array('message' => "さんは|仙狸|でした", 'type' => 'result_poison_cat');
  var $result_sacrifice_cat = array('message' => "さんは|猫神|でした", 'type' => 'result_poison_cat');
  var $result_pharmacist = array('message' => "さんは|薬師|でした", 'type' => 'result_poison');
  var $result_cure_pharmacist = array('message' => "さんは|河童|でした", 'type' => 'result_pharmacist');
  var $result_revive_pharmacist = array('message' => "さんは|仙人|でした", 'type' => 'result_pharmacist');
  var $result_assassin = array('message' => "さんは|暗殺者|でした", 'delimiter' => array('|' => 'assassin'));
  var $result_doom_assassin = array('message' => "さんは|死神|でした", 'type' => 'assassin');
  var $result_reverse_assassin = array('message' => "さんは|反魂師|でした", 'type' => 'assassin');
  var $result_soul_assassin = array('message' => "さんは|辻斬り|でした", 'type' => 'assassin');
  var $result_eclipse_assassin = array('message' => "さんは|蝕暗殺者|でした", 'type' => 'assassin');
  var $result_mind_scanner = array('message' => "さんは|さとり|でした", 'delimiter' => array('|' => 'mind'));
  var $result_evoke_scanner = array('message' => "さんは|イタコ|でした", 'type' => 'result_mind_scanner');
  var $result_whisper_scanner = array('message' => "さんは|囁騒霊|でした", 'type' => 'result_mind_scanner');
  var $result_howl_scanner = array('message' => "さんは|吠騒霊|でした", 'type' => 'result_mind_scanner');
  var $result_telepath_scanner = array('message' => "さんは|念騒霊|でした", 'type' => 'result_mind_scanner');
  var $result_jealousy = array('message' => "さんは|橋姫|でした", 'delimiter' => array('|' => 'jealousy'));
  var $result_poison_jealousy = array('message' => "さんは|毒橋姫|でした", 'type' => 'result_jealousy');
  var $result_doll = array('message' => "さんは|上海人形|でした", 'delimiter' => array('|' => 'doll'));
  var $result_friend_doll = array('message' => "さんは|仏蘭西人形|でした", 'type' => 'result_doll');
  var $result_poison_doll = array('message' => "さんは|鈴蘭人形|でした", 'type' => 'result_doll');
  var $result_doom_doll = array('message' => "さんは|蓬莱人形|でした", 'type' => 'result_doll');
  var $result_doll_master = array('message' => "さんは|人形遣い|でした", 'type' => 'result_doll');
  var $result_brownie = array('message' => "さんは|座敷童子|でした", 'delimiter' => array('|' => 'brownie'));
  var $result_history_brownie = array('message' => "さんは|白澤|でした", 'type' => 'result_brownie');
  var $result_wolf = array('message' => "さんは|人狼|でした", 'delimiter' => array('|' => 'wolf'));
  var $result_boss_wolf = array('message' => "さんは|白狼|でした", 'type' => 'result_wolf');
  var $result_gold_wolf = array('message' => "さんは|金狼|でした", 'type' => 'result_wolf');
  var $result_phantom_wolf = array('message' => "さんは|幻狼|でした", 'type' => 'result_wolf');
  var $result_cursed_wolf = array('message' => "さんは|呪狼|でした", 'type' => 'result_wolf');
  var $result_wise_wolf = array('message' => "さんは|賢狼|でした", 'type' => 'result_wolf');
  var $result_poison_wolf = array('message' => "さんは|毒狼|でした", 'type' => 'result_wolf');
  var $result_resist_wolf = array('message' => "さんは|抗毒狼|でした", 'type' => 'result_wolf');
  var $result_blue_wolf = array('message' => "さんは|蒼狼|でした", 'type' => 'result_wolf');
  var $result_emerald_wolf = array('message' => "さんは|翠狼|でした", 'type' => 'result_wolf');
  var $result_sex_wolf = array('message' => "さんは|雛狼|でした", 'type' => 'result_wolf');
  var $result_tongue_wolf = array('message' => "さんは|舌禍狼|でした", 'type' => 'result_wolf');
  var $result_possessed_wolf = array('message' => "さんは|憑狼|でした", 'type' => 'result_wolf');
  var $result_hungry_wolf = array('message' => "さんは|餓狼|でした", 'type' => 'result_wolf');
  var $result_doom_wolf = array('message' => "さんは|冥狼|でした", 'type' => 'result_wolf');
  var $result_sirius_wolf = array('message' => "さんは|天狼|でした", 'type' => 'result_wolf');
  var $result_elder_wolf = array('message' => "さんは|古狼|でした", 'type' => 'result_wolf');
  var $result_cute_wolf   = array('message' => "さんは|萌狼|でした", 'type' => 'result_wolf');
  var $result_scarlet_wolf = array('message' => "さんは|紅狼|でした", 'type' => 'result_wolf');
  var $result_silver_wolf = array('message' => "さんは|銀狼|でした", 'type' => 'result_wolf');
  var $result_mad = array('message' => "さんは|狂人|でした", 'type' => 'result_wolf');
  var $result_fanatic_mad = array('message' => "さんは|狂信者|でした", 'type' => 'result_mad');
  var $result_whisper_mad = array('message' => "さんは|囁き狂人|でした", 'type' => 'result_mad');
  var $result_jammer_mad = array('message' => "さんは|月兎|でした", 'type' => 'result_mad');
  var $result_voodoo_mad = array('message' => "さんは|呪術師|でした", 'type' => 'result_mad');
  var $result_corpse_courier_mad = array('message' => "さんは|火車|でした", 'type' => 'result_mad');
  var $result_agitate_mad = array('message' => "さんは|扇動者|でした", 'type' => 'result_mad');
  var $result_miasma_mad = array('message' => "さんは|土蜘蛛|でした", 'type' => 'result_mad');
  var $result_dream_eater_mad = array('message' => "さんは|獏|でした", 'type' => 'result_mad');
  var $result_trap_mad = array('message' => "さんは|罠師|でした", 'type' => 'result_mad');
  var $result_snow_trap_mad = array('message' => "さんは|雪女|でした", 'type' => 'result_mad');
  var $result_possessed_mad = array('message' => "さんは|犬神|でした", 'type' => 'result_mad');
  var $result_therian_mad = array('message' => "さんは|獣人|でした", 'type' => 'result_mad');
  var $result_fox = array('message' => "さんは|妖狐|でした", 'delimiter' => array('|' => 'fox'));
  var $result_white_fox = array('message' => "さんは|白狐|でした", 'type' => 'result_fox');
  var $result_black_fox = array('message' => "さんは|黒狐|でした", 'type' => 'result_fox');
  var $result_gold_fox = array('message' => "さんは|金狐|でした", 'type' => 'result_fox');
  var $result_phantom_fox = array('message' => "さんは|幻狐|でした", 'type' => 'result_fox');
  var $result_poison_fox = array('message' => "さんは|管狐|でした", 'type' => 'result_fox');
  var $result_blue_fox = array('message' => "さんは|蒼狐|でした", 'type' => 'result_fox');
  var $result_emerald_fox = array('message' => "さんは|翠狐|でした", 'type' => 'result_fox');
  var $result_voodoo_fox = array('message' => "さんは|九尾|でした", 'type' => 'result_fox');
  var $result_revive_fox = array('message' => "さんは|仙狐|でした", 'type' => 'result_fox');
  var $result_possessed_fox = array('message' => "さんは|憑狐|でした", 'type' => 'result_fox');
  var $result_doom_fox = array('message' => "さんは|冥狐|でした", 'type' => 'result_fox');
  var $result_cursed_fox = array('message' => "さんは|天狐|でした", 'type' => 'result_fox');
  var $result_elder_fox = array('message' => "さんは|古狐|でした", 'type' => 'result_fox');
  var $result_cute_fox = array('message' => "さんは|萌狐|でした", 'type' => 'result_fox');
  var $result_scarlet_fox = array('message' => "さんは|紅狐|でした", 'type' => 'result_fox');
  var $result_silver_fox = array('message' => "さんは|銀狐|でした", 'type' => 'result_fox');
  var $result_child_fox = array('message' => "さんは|子狐|でした", 'type' => 'result_fox');
  var $result_sex_fox = array('message' => "さんは|雛狐|でした", 'type' => 'result_child_fox');
  var $result_stargazer_fox = array('message' => "さんは|星狐|でした", 'type' => 'result_child_fox');
  var $result_jammer_fox = array('message' => "さんは|月狐|でした", 'type' => 'result_child_fox');
  var $result_miasma_fox = array('message' => "さんは|蟲狐|でした", 'type' => 'result_child_fox');
  var $result_cupid = array('message' => "さんは|キューピッド|でした", 'delimiter' => array('|' => 'lovers'));
  var $result_self_cupid = array('message' => "さんは|求愛者|でした", 'type' => 'result_cupid');
  var $result_moon_cupid = array('message' => "さんは|かぐや姫|でした", 'type' => 'result_cupid');
  var $result_mind_cupid = array('message' => "さんは|女神|でした", 'type' => 'result_cupid');
  var $result_triangle_cupid = array('message' => "さんは|小悪魔|でした", 'type' => 'result_cupid');
  var $result_angel = array('message' => "さんは|天使|でした", 'type' => 'result_cupid');
  var $result_rose_angel = array('message' => "さんは|薔薇天使|でした", 'type' => 'result_angel');
  var $result_lily_angel = array('message' => "さんは|百合天使|でした", 'type' => 'result_angel');
  var $result_exchange_angel = array('message' => "さんは|魂移使|でした", 'type' => 'result_angel');
  var $result_ark_angel = array('message' => "さんは|大天使|でした", 'type' => 'result_angel');
  var $result_lovers = array('message' => "さんは|恋人|でした", 'type' => 'result_cupid');
  var $result_quiz = array('message' => "さんは|出題者|でした", 'delimiter' => array('|' => 'quiz'));
  var $result_vampire = array('message' => "さんは|吸血鬼|でした", 'delimiter' => array('|' => 'vampire'));
  var $result_chiroptera = array('message' => "さんは|蝙蝠|でした", 'delimiter' => array('|' => 'chiroptera'));
  var $result_poison_chiroptera = array('message' => "さんは|毒蝙蝠|でした", 'type' => 'result_chiroptera');
  var $result_cursed_chiroptera = array('message' => "さんは|呪蝙蝠|でした", 'type' => 'result_chiroptera');
  var $result_boss_chiroptera = array('message' => "さんは|大蝙蝠|でした", 'type' => 'result_chiroptera');
  var $result_elder_chiroptera = array('message' => "さんは|古蝙蝠|でした", 'type' => 'result_chiroptera');
  var $result_dummy_chiroptera = array('message' => "さんは|夢求愛者|でした", 'type' => 'result_chiroptera');
  var $result_fairy = array('message' => "さんは|妖精|でした", 'type' => 'result_chiroptera');
  var $result_spring_fairy = array('message' => "さんは|春妖精|でした", 'type' => 'result_fairy');
  var $result_summer_fairy = array('message' => "さんは|夏妖精|でした", 'type' => 'result_fairy');
  var $result_autumn_fairy = array('message' => "さんは|秋妖精|でした", 'type' => 'result_fairy');
  var $result_winter_fairy = array('message' => "さんは|冬妖精|でした", 'type' => 'result_fairy');
  var $result_flower_fairy = array('message' => "さんは|花妖精|でした", 'type' => 'result_fairy');
  var $result_star_fairy = array('message' => "さんは|星妖精|でした", 'type' => 'result_fairy');
  var $result_sun_fairy = array('message' => "さんは|日妖精|でした", 'type' => 'result_fairy');
  var $result_moon_fairy = array('message' => "さんは|月妖精|でした", 'type' => 'result_fairy');
  var $result_grass_fairy = array('message' => "さんは|草妖精|でした", 'type' => 'result_fairy');
  var $result_light_fairy = array('message' => "さんは|光妖精|でした", 'type' => 'result_fairy');
  var $result_dark_fairy = array('message' => "さんは|闇妖精|でした", 'type' => 'result_fairy');
  var $result_ice_fairy = array('message' => "さんは|氷妖精|でした", 'type' => 'result_fairy');
  var $result_mirror_fairy = array('message' => "さんは|鏡妖精|でした", 'type' => 'result_fairy');
  var $result_mania = array('message' => "さんは|神話マニア|でした", 'delimiter' => array('|' => 'mania'));
  var $result_trick_mania = array('message' => "さんは|奇術師|でした", 'type' => 'result_mania');
  var $result_soul_mania = array('message' => "さんは|覚醒者|でした", 'type' => 'result_mania');
  var $result_unknown_mania = array('message' => "さんは|鵺|でした", 'type' => 'result_mania');
  var $result_dummy_mania = array('message' => "さんは|夢語部|でした", 'type' => 'result_mania');

  var $result_mage_failed = array('message' => "さんの鑑定に失敗しました");
  var $result_stolen = array('message' => "さんの死体が盗まれました！");
  var $result_sex_male = array('message' => "さんは|男性|でした", 'delimiter' => array('|' => 'sex_male'));
  var $result_sex_female = array('message' => "さんは|女性|でした", 'delimiter' => array('|' => 'lovers'));
  var $result_psycho_mage_normal = array('message' => "さんは正常でした");
  var $result_psycho_mage_liar = array('message' => "さんは|嘘|をついています", 'type' => 'result_wolf');
  var $result_stargazer_mage_ability = array('message' => "さんは|投票能力|を持っています", 'type' => 'result_wolf');
  var $result_stargazer_mage_nothing = array('message' => "さんは投票能力を持っていません");

  var $mage_result = array('message' => "|占い|結果： ", 'type' => 'result_mage');
  var $voodoo_killer_success = array('message' => "さんの|解呪|に成功しました！", 'type' => 'result_mage');
  var $necromancer_result = array('message' => "|霊能|結果： ", 'type' => 'result_necromancer');
  var $medium_result = array('message' => "|神託|結果： ", 'type' => 'result_medium');
  var $priest_header = array('message' => "|神託|結果： 現在、生存している#村人#陣営は",
			     'delimiter' => array('|' => 'priest', '#' => 'human'));
  var $priest_footer = array('message' => "人です");
  var $bishop_priest_header = array('message' => "|神託|結果： 現在、死亡した#非村人#陣営は",
				    'type' => 'priest_header', 'delimiter' => array('#' => 'wolf'));
  var $dowser_priest_header = array('message' => "|神託|結果： 現在の生存者が所有しているサブ役職の合計は",
				    'type' => 'priest_header');
  var $dowser_priest_footer = array('message' => "個です");
  var $border_priest_header = array('message' => "|神託|結果： 昨夜、あなたの境界に触れた人数は",
				    'type' => 'priest_header');
  var $crisis_priest_result = array('message' => "陣営が勝利目前です");
  var $side_wolf = array('message' => "|人狼|", 'type' => 'result_wolf');
  var $side_fox = array('message' => "|妖狐|", 'type' => 'result_fox');
  var $side_lovers = array('message' => "|恋人|", 'type' => 'result_cupid');
  var $guard_hunted = array('message' => "さんを|狩る|ことに成功しました！", 'type' => 'result_guard');
  var $guard_success = array('message' => "さん|護衛|成功！", 'type' => 'result_guard');
  var $reporter_result_header = array('message' => "|張り込み|結果： ", 'type' => 'result_guard');
  var $reporter_result_footer = array('message' => "さんに|襲撃|されました！", 'type' => 'result_wolf');
  var $anti_voodoo_success = array('message' => "さんの|厄払い|に成功しました！", 'type' => 'result_guard');
  var $poison_cat_success = array('message' => "さん|蘇生|成功！", 'type' => 'result_poison_cat');
  var $poison_cat_failed = array('message' => "さん蘇生失敗");
  var $pharmacist_nothing = array('message' => "さんは毒を持っていません");
  var $pharmacist_poison = array('message' => "さんは|毒|を持っています", 'type' => 'result_pharmacist');
  var $pharmacist_strong = array('message' => "さんは|強い毒|を持っています", 'type' => 'result_pharmacist');
  var $pharmacist_limited = array('message' => "さんは|限定的な毒|を持っています", 'type' => 'result_pharmacist');
  var $pharmacist_success = array('message' => "さんの|解毒|に成功しました", 'type' => 'result_pharmacist');
  var $pharmacist_cured = array('message' => "さんの|治療|に成功しました", 'type' => 'result_pharmacist');
  var $assassin_result = array('message' => "|暗殺|結果： ", 'type' => 'result_assassin');
  var $wolf_result = array('message' => "|襲撃|結果： ", 'type' => 'result_wolf');
  var $possessed_target = array('message' => "さんに|憑依|しています ", 'type' => 'result_wolf');
  var $fox_targeted = array('message' => "昨晩、|人狼|に狙われたようです", 'type' => 'result_wolf');
  var $exchange_header = array('message' => "あなたは３日目に");
  var $exchange_footer = array('message' => "さんに|憑依|します", 'type' => 'result_wolf');
  var $sympathy_result = array('message' => "|共感|結果： ", 'type' => 'result_mind_scanner');
}

class WishRoleList{
  var $role_none              = array('message' => "無し→");
  var $role_human             = array('message' => "村人→");
  var $role_mage              = array('message' => "占い師→");
  var $role_necromancer       = array('message' => "霊能者→");
  var $role_medium            = array('message' => "巫女→");
  var $role_priest            = array('message' => "司祭→");
  var $role_guard             = array('message' => "狩人→");
  var $role_common            = array('message' => "共有者→");
  var $role_detective_common  = array('message' => "探偵→");
  var $role_poison            = array('message' => "埋毒者→");
  var $role_poison_cat        = array('message' => "猫又→");
  var $role_pharmacist        = array('message' => "薬師→");
  var $role_assassin          = array('message' => "暗殺者→");
  var $role_mind_scanner      = array('message' => "さとり→");
  var $role_jealousy          = array('message' => "橋姫→");
  var $role_doll              = array('message' => "上海人形→");
  var $role_brownie           = array('message' => "座敷童子→");
  var $role_wolf              = array('message' => "人狼→");
  var $role_boss_wolf         = array('message' => "白狼→");
  var $role_poison_wolf       = array('message' => "毒狼→");
  var $role_possessed_wolf    = array('message' => "憑狼→");
  var $role_sirius_wolf       = array('message' => "天狼→");
  var $role_mad               = array('message' => "狂人→");
  var $role_fanatic_mad       = array('message' => "狂信者→");
  var $role_trap_mad          = array('message' => "罠師→");
  var $role_fox               = array('message' => "妖狐→");
  var $role_child_fox         = array('message' => "子狐→");
  var $role_cupid             = array('message' => "キューピッド→");
  var $role_angel             = array('message' => "天使→");
  var $role_mind_cupid        = array('message' => "女神→");
  var $role_quiz              = array('message' => "出題者→");
  var $role_vampire           = array('message' => "吸血鬼→");
  var $role_chiroptera        = array('message' => "蝙蝠→");
  var $role_fairy             = array('message' => "妖精→");
  var $role_mania             = array('message' => "神話マニア→");
}

//imagegif($image, "c:\\temp\\result.gif"); // ファイルに出力する場合
#$builder = new MessageImageBuilder('WishRoleList'); $builder->Output('role_brownie');
$builder = new MessageImageBuilder('RoleMessageList');
#$builder->Output('poison');
$builder->Output('result_poison_jealousy');
