<?php
require_once('init.php');

$disable = true; //false にすると使用可能になる
if ($disable) {
  HTML::OutputResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}

Loader::LoadFile('game_config', 'room_config', 'time_config', 'message', 'feedengine',
		 'image_class');

DB::Connect(); // DB 接続
$site_summary = FeedEngine::Initialize('site_summary.php');
echo $site_summary->Export();
