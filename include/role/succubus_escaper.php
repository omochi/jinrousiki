<?php
/*
  ◆水妖姫
  ○仕様
  ・逃亡失敗：男性以外
*/
class Role_succubus_escaper extends Role{
  function __construct(){ parent::__construct(); }

  function EscapeFailed($user){ return $user->sex != 'male'; }
}
