<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
Loader::LoadFile('chaos_config', 'cast_class', 'room_option_class', 'test_class');

//-- 仮想村データをセット --//
Loader::LoadRequest('RequestBaseGame', true);
RQ::$get->room_no = 1;
RQ::$get->TestItems = new StdClass();
RQ::GetTest()->test_room = array(
  'id' => RQ::$get->room_no, 'name' => '配役テスト村', 'comment' => '',
  'date' => 0, 'scene' => 'beforegame', 'status' => 'waiting',
  'game_option' => 'dummy_boy real_time:6:4 wish_role',
  'option_role' => '',
);
#RQ::AddTestRoom('game_option', 'quiz');
#RQ::AddTestRoom('game_option', 'chaosfull');
RQ::AddTestRoom('game_option', 'chaos_hyper');
#RQ::AddTestRoom('game_option', 'blinder');
#RQ::AddTestRoom('option_role', 'gerd');
#RQ::AddTestRoom('option_role', 'poison cupid medium mania');
RQ::AddTestRoom('option_role', 'decide');
#RQ::AddTestRoom('option_role', 'detective');
RQ::AddTestRoom('option_role', 'joker');
#RQ::AddTestRoom('option_role', 'gentleman');
#RQ::AddTestRoom('option_role', 'sudden_death');
#RQ::AddTestRoom('option_role', 'replace_human');
#RQ::AddTestRoom('option_role', 'full_mania');
RQ::AddTestRoom('option_role', 'chaos_open_cast');
#RQ::AddTestRoom('option_role', 'chaos_open_cast_role');
#RQ::AddTestRoom('option_role', 'chaos_open_cast_camp');
#RQ::AddTestRoom('option_role', 'sub_role_limit_easy');
#RQ::AddTestRoom('option_role', 'sub_role_limit_normal');
#RQ::AddTestRoom('option_role', 'sub_role_limit_hard');
RQ::GetTest()->is_virtual_room = true;
RQ::$get->vote_times = 1;

Dev::InitializeUser(22,
  array( 1 => '',
	 2 => 'human',
	 3 => 'fox',
	 4 => 'mage',
	 5 => 'cupid',
	 6 => 'assassin',
	 7 => 'guard',
	 8 => 'possessed_wolf',
	 9 => 'mad',
	10 => 'duelist',
	11 => 'fox',
	12 => '',
	13 => 'wizard',
	14 => 'mage',
	15 => 'mad',
	16 => 'wolf',
	17 => 'medium',
	18 => 'guard',
	19 => 'poison',
	20 => 'vampire',
	21 => 'ogre',
	22 => '',));
Dev::ComplementUser();
//Text::p(RQ::GetTest()->test_users[22]);

//-- 設定調整 --//
#CastConfig::$decide = 11;
#RQ::GetTest()->test_users[3]->live = 'kick';

//-- データ収集 --//
//DB::Connect(); //DB接続 (必要なときだけ設定する)
DB::$ROOM = new Room(RQ::$get); //村情報を取得
DB::$ROOM->test_mode = true;
DB::$ROOM->log_mode  = true;
DB::$ROOM->scene = 'beforegame';
DB::$ROOM->vote  = array();

DB::$USER = new UserDataSet(RQ::$get);
DB::$SELF = DB::$USER->ByID(1);

//-- データ出力 --//
HTML::OutputHeader('配役テスト', 'game_play', true);
GameHTML::OutputPlayer();
Vote::AggregateGameStart();
DB::$ROOM->date++;
DB::$ROOM->scene = 'night';
foreach (DB::$USER->rows as $user) $user->Reparse();
GameHTML::OutputPlayer();
HTML::OutputFooter();
