<?php
/*
  ◆夢語部
  ○仕様
  ・コピー：特殊
  ・変化：基本・劣化種
*/
class Role_dummy_mania extends Role{
  public $copied = 'copied_teller';
  public $copy_list = array(
      'human'        => 'suspect',
      'mage'         => 'dummy_mage',
      'necromancer'  => 'dummy_necromancer',
      'medium'       => 'medium',
      'priest'       => 'dummy_priest',
      'guard'        => 'dummy_guard',
      'common'       => 'dummy_common',
      'poison'       => 'dummy_poison',
      'poison_cat'   => 'eclipse_cat',
      'pharmacist'   => 'centaurus_pharmacist',
      'assassin'     => 'eclipse_assassin',
      'mind_scanner' => 'mind_scanner',
      'jealousy'     => 'jealousy',
      'brownie'      => 'brownie',
      'wizard'       => 'astray_wizard',
      'doll'         => 'silver_doll',
      'escaper'      => 'doom_escaper',
      'wolf'         => 'silver_wolf',
      'mad'          => 'immolate_mad',
      'fox'          => 'silver_fox',
      'child_fox'    => 'sex_fox',
      'cupid'        => 'self_cupid',
      'angel'        => 'angel',
      'quiz'         => 'quiz',
      'vampire'      => 'scarlet_vampire',
      'chiroptera'   => 'dummy_chiroptera',
      'fairy'        => 'mirror_fairy',
      'ogre'         => 'incubus_ogre',
      'yaksa'        => 'succubus_yaksa',
      'duelist'      => 'cowboy_duelist',
      'avenger'      => 'cute_avenger',
      'patron'       => 'critical_patron');

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