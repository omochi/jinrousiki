<?php
/*
  ◆未亡人 (widow_priest)
  ○仕様
  ・役職表示：村人
  ・司祭：共感者 (身代わり君)
  ・結果表示：特殊 (共感者)
*/
RoleManager::LoadFile('priest');
class Role_widow_priest extends Role_priest{
  public $display_role = 'human';
  public $result_date  = NULL;

  function __construct(){ parent::__construct(); }

  function Priest($role_flag, $data){
    global $ROOM, $USERS;

    if($ROOM->date != 1 || ! $ROOM->IsDummyBoy()) return false;

    $dummy_boy = $USERS->ByID(1);
    $str = "\t" . $dummy_boy->handle_name . "\t" . $dummy_boy->main_role;
    foreach($role_flag->{$this->role} as $uname){
      $user = $USERS->ByUname($uname);
      if($user->IsDummyBoy()) continue;
      $user->AddRole('mind_sympathy');
      $ROOM->SystemMessage($user->handle_name . $str, 'SYMPATHY_RESULT');
    }
  }
}