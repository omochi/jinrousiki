<?php
/*
  ◆不人気 (disfavor)
  ○仕様
  ・得票数が +1 される
*/
class Role_disfavor extends Role{
  function __construct(){ parent::__construct(); }

  function FilterVoted(&$voted_number){
    $voted_number++;
  }
}
