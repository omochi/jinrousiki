<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');

$DISABLE_TWITTER_TEST = true; //false にすると使用可能になる
if ($DISABLE_TWITTER_TEST) {
  HTML::OutputResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}
Loader::LoadFile('twitter_class');

//-- 投稿テスト用データ --//
$room_no      = '1';
$room_name    = 'Twitter 投稿テスト';
$room_comment = 'Twitter 投稿テストです';

//-- 表示 --//
HTML::OutputHeader('Twitter 投稿テストツール', 'game', true);
if (JinroTwitter::Send($room_no, $room_name, $room_comment)) echo "Twitter 投稿成功<br>\n";
HTML::OutputFooter();
