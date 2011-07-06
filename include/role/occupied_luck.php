<?php
/*
  ◆ひんな持ち (occupied_luck)
  ○仕様
  ・得票数：付加者が誰か生きていれば +1 / 全員死んでいたら +3
*/
class Role_occupied_luck extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){ $voted_number += $this->IsLivePartner() ? 1 : 3; }
}
