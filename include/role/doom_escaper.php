<?php
/*
  ◆半鳥女
  ○仕様
  ・逃亡失敗：死の宣告を受けた人
*/
class Role_doom_escaper extends Role{
  function __construct(){ parent::__construct(); }

  function EscapeFailed($user){ return $user->IsRole('death_warrant'); }
}
