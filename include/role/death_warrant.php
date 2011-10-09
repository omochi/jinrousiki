<?php
/*
  ◆死の宣告 (death_warrant)
  ○仕様
  ・ショック死：発動当日
*/
RoleManager::LoadFile('febris');
class Role_death_warrant extends Role_febris{
  public $sudden_death = 'WARRANT';
  function __construct(){ parent::__construct(); }
}
