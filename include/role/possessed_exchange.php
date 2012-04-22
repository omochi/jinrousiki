<?php
/*
  ◆交換憑依 (possessed_exchange)
  ○仕様
*/
class Role_possessed_exchange extends Role {
  function __construct(){ parent::__construct(); }

  protected function OutputImage(){ return; }

  protected function OutputResult(){
    if (! is_array($stack = $this->GetActor()->GetPartner($this->role))) return;
    if (is_null($target = DB::$USER->ByID(array_shift($stack))->handle_name)) return;
    DB::$ROOM->date < 3 ?
      OutputAbilityResult('exchange_header', $target, 'exchange_footer') :
      OutputAbilityResult('partner_header', $this->GetActor()->handle_name, 'possessed_target');
  }
}
