<?php
require_once('init.php');

$disable = true; //false にすると使用可能になる
if ($disable) {
  HTML::OutputResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}

Loader::LoadFile('test_class', 'twitter_class');
TwitterTest::Output();
