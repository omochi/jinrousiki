<?php
/*
  ◆羊 (mind_sheep)
  ○仕様
  ・人狼襲撃：羊皮
*/
class Role_mind_sheep extends Role{
  function __construct(){ parent::__construct(); }

  function WolfEatCounter($user){ $user->AddDoom(1, 'sheep_wisp'); }
}
