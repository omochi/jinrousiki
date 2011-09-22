<?php
/*
  ◆預言者 (crisis_priest)
  ○仕様
  ・役職表示：村人
  ・司祭：人外勝利前日情報
  ・結果表示：2日目以降
*/
RoleManager::LoadFile('priest');
class Role_crisis_priest extends Role_priest{
  public $display_role = 'human';
  public $result_date  = 'second';

  function __construct(){ parent::__construct(); }

  function Priest($role_flag, $data){
    global $ROOM;
    if($data->crisis != '') $ROOM->SystemMessage($data->crisis, $this->GetEvent());
  }
}
