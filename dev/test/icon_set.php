<?php
exit;
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');

if(! $DEBUG_MODE){
  OutputActionResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}
$INIT_CONF->LoadClass('ICON_CONF');

$DB_CONF->Connect(); //DB 接続
//UpdateIconInfo('category', '初期設定', 1, 10);
//UpdateIconInfo('category', '東方Project', 11, 78);
//UpdateIconInfo('appearance', '東方紅魔郷', 13, 21);
//UpdateIconInfo('appearance', '東方妖々夢', 22, 33);
//UpdateIconInfo('appearance', '東方萃夢想', 34);
//UpdateIconInfo('appearance', '東方永夜抄', 35, 42);
//UpdateIconInfo('appearance', '東方花映塚', 43, 47);
//UpdateIconInfo('appearance', '東方風神録', 48, 55);
//UpdateIconInfo('appearance', '東方緋想天', 56, 57);
//UpdateIconInfo('appearance', '東方地霊殿', 58, 65);
//UpdateIconInfo('appearance', '東方香霖堂', 66, 67);
//UpdateIconInfo('appearance', '東方三月精', 68, 70);
//UpdateIconInfo('appearance', '東方求聞史紀', 71);
//UpdateIconInfo('appearance', '東方儚月抄', 72);
//UpdateIconInfo('appearance', '秘封倶楽部', 76, 77);
//UpdateIconInfo('appearance', '東方靈異伝', 91, 92);
//UpdateIconInfo('appearance', '東方夢時空', 181);
//UpdateIconInfo('appearance', '東方怪綺談', 185, 186);
//UpdateIconInfo('appearance', '東方二次', 121);
//UpdateIconInfo('category', '東方二次', 121);
//UpdateIconInfo('category', 'ポケットモンスター', 96, 97);
//UpdateIconInfo('appearance', 'ポケットモンスター 金・銀', 96);
//UpdateIconInfo('appearance', 'はじめ人間ギャートルズ', 99);
//UpdateIconInfo('appearance', 'トランスフォーマーG1', 106);
//UpdateIconInfo('category', 'トランスフォーマー', 106);
//UpdateIconInfo('appearance', 'Rozen Maiden', 118);
//UpdateIconInfo('category', 'ローゼンメイデン', 118);
//UpdateIconInfo('appearance', 'らき☆すた', 144);
//UpdateIconInfo('author', '夏蛍', 12, 77);
//UpdateIconInfo('author', 'ジギザギのさいはて', 109, 111);
mysql_query('COMMIT'); //一応コミット

//DB 接続解除は OutputActionResult() 経由
OutputActionResult('処理完了', '処理完了。');

//-- 関数 --//
function UpdateIconInfo($type, $value, $from, $to = NULL){
  $query = isset($to) ? "{$from} <= icon_no AND icon_no <= {$to}" : "icon_no = {$from}";
  mysql_query("UPDATE user_icon SET {$type} = '{$value}' WHERE {$query}");
}
