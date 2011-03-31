<?php
/*
  ◆夢語部
  ○仕様
  ・コピー先の基本・劣化種に変化する
*/
class Role_dummy_mania extends Role{
  var $copied = 'copied_teller';
  var $copy_list = array(
      'human'        => 'suspect',
      'mage'         => 'dummy_mage',
      'necromancer'  => 'dummy_necromancer',
      'medium'       => 'medium',
      'priest'       => 'dummy_priest',
      'guard'        => 'dummy_guard',
      'common'       => 'dummy_common',
      'poison'       => 'dummy_poison',
      'poison_cat'   => 'eclipse_cat',
      'pharmacist'   => 'cure_pharmacist',
      'assassin'     => 'eclipse_assassin',
      'mind_scanner' => 'mind_scanner',
      'jealousy'     => 'jealousy',
      'brownie'      => 'brownie',
      'wizard'       => 'wizard',
      'doll'         => 'silver_doll',
      'escaper'      => 'incubus_escaper',
      'wolf'         => 'silver_wolf',
      'mad'          => 'mad',
      'fox'          => 'silver_fox',
      'child_fox'    => 'sex_fox',
      'cupid'        => 'self_cupid',
      'angel'        => 'angel',
      'quiz'         => 'quiz',
      'vampire'      => 'vampire',
      'chiroptera'   => 'dummy_chiroptera',
      'fairy'        => 'mirror_fairy',
      'ogre'         => 'incubus_ogre',
      'yaksa'        => 'succubus_yaksa');

  function __construct(){ parent::__construct(); }

  function GetRole($user){
    return $user->IsRoleGroup('mania', 'copied') ? 'human' :
      $this->copy_list[$user->DistinguishRoleGroup()];
  }
}
