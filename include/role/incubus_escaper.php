<?php
/*
  ◆一角獣
  ○仕様
  ・逃亡失敗：女性以外
*/
class Role_incubus_escaper extends Role{
  function __construct(){ parent::__construct(); }

  function EscapeFailed($user){ return $user->sex != 'female'; }
}
