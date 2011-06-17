<?php
/*
  ◆覚醒者
  ○仕様
  ・コピー：特殊
  ・変化：上位種
*/
class Role_soul_mania extends Role{
  public $copied = 'copied_soul';
  public $copy_list = array(
      'human'        => 'executor',
      'mage'         => 'soul_mage',
      'necromancer'  => 'soul_necromancer',
      'medium'       => 'revive_medium',
      'priest'       => 'high_priest',
      'guard'        => 'poison_guard',
      'common'       => 'ghost_common',
      'poison'       => 'strong_poison',
      'poison_cat'   => 'revive_cat',
      'pharmacist'   => 'alchemy_pharmacist',
      'assassin'     => 'soul_assassin',
      'mind_scanner' => 'clairvoyance_scanner',
      'jealousy'     => 'poison_jealousy',
      'brownie'      => 'history_brownie',
      'wizard'       => 'soul_wizard',
      'doll'         => 'doll_master',
      'escaper'      => 'escaper',
      'wolf'         => 'sirius_wolf',
      'mad'          => 'whisper_mad',
      'fox'          => 'cursed_fox',
      'child_fox'    => 'jammer_fox',
      'cupid'        => 'minstrel_cupid',
      'angel'        => 'sacrifice_angel',
      'quiz'         => 'quiz',
      'vampire'      => 'soul_vampire',
      'chiroptera'   => 'boss_chiroptera',
      'fairy'        => 'ice_fairy',
      'ogre'         => 'sacrifice_ogre',
      'yaksa'        => 'dowser_yaksa',
      'duelist'      => 'valkyrja_duelist',
      'avenger'      => 'avenger',
      'patron'       => 'patron');

  function __construct(){ parent::__construct(); }

  function Copy($user, $vote_data){
    $this->GetActor()->AddMainRole($user->user_no);
    return $user->IsRoleGroup('mania') ? 'human' : $user->DistinguishRoleGroup();
  }

  function GetRole($user){
    return $user->IsRoleGroup('mania', 'copied') ? 'human' :
      $this->copy_list[$user->DistinguishRoleGroup()];
  }
}
