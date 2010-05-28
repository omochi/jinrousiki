<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('ROOM_CONF', 'GAME_CONF', 'CAST_CONF');
$INIT_CONF->LoadFile('game_vote_functions');
OutputHTMLHeader('Åý·×');
$DB_CONF->Connect(); //DB ÀÜÂ³

FetchStatus();
OutputHTMLFooter(true);

function FetchStatus(){
  $query = 'SELECT victory_role, COUNT(victory_role) AS count FROM room ';
  $query .= 'WHERE ! (game_option LIKE "%chaos%") AND ! (option_role LIKE "%duel%")';
  $query .=' GROUP BY victory_role';
  $list = FetchAssoc($query);
  foreach($list as $stack){
    PrintData($stack['count'], $stack['victory_role']);
  }
}