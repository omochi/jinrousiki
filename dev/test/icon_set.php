<?php
exit;
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');

if(! $DEBUG_MODE){
  OutputActionResult('認証エラー', 'このスクリプトは使用できない設定になっています。');
}
$INIT_CONF->LoadClass('ICON_CONF');

$DB_CONF->Connect(); //DB 接続
//mysql_query("UPDATE user_icon SET category = '初期設定' WHERE icon_no > 0 AND icon_no <= 10");
//mysql_query("UPDATE user_icon SET appearance = '東方靈異伝' WHERE icon_no = 11");
//mysql_query("UPDATE user_icon SET category = '東方Project' WHERE icon_no >= 12 AND icon_no < 78");
//mysql_query("UPDATE user_icon SET appearance = '東方妖々夢' WHERE icon_no >= 23 AND icon_no <= 34");
//mysql_query("UPDATE user_icon SET appearance = '東方萃夢想' WHERE icon_no = 35");
//mysql_query("UPDATE user_icon SET appearance = '東方永夜抄' WHERE icon_no >= 36 AND icon_no <= 43");
//mysql_query("UPDATE user_icon SET appearance = '東方花映塚' WHERE icon_no >= 44 AND icon_no <= 48");
//mysql_query("UPDATE user_icon SET appearance = '東方風神録' WHERE icon_no >= 49 AND icon_no <= 56");
//mysql_query("UPDATE user_icon SET appearance = '東方緋想天' WHERE icon_no >= 57 AND icon_no <= 58");
//mysql_query("UPDATE user_icon SET appearance = '東方地霊殿' WHERE icon_no >= 59 AND icon_no <= 66");
//mysql_query("UPDATE user_icon SET appearance = '東方香霖堂' WHERE icon_no >= 67 AND icon_no <= 68");
//mysql_query("UPDATE user_icon SET appearance = '東方三月精' WHERE icon_no >= 69 AND icon_no <= 71");
//mysql_query("UPDATE user_icon SET appearance = '東方求聞史紀' WHERE icon_no = 72");
//mysql_query("UPDATE user_icon SET appearance = '東方儚月抄' WHERE icon_no >= 73 AND icon_no <= 75");
//mysql_query("UPDATE user_icon SET appearance = '秘封倶楽部' WHERE icon_no >= 76 AND icon_no <= 77");
//mysql_query("UPDATE user_icon SET appearance = 'トランスフォーマーG1' WHERE icon_no = 78");
//mysql_query("UPDATE user_icon SET category = 'トランスフォーマー' WHERE icon_no = 78");
//mysql_query("UPDATE user_icon SET appearance = 'ネトゲ実況＠2ch掲示板' WHERE icon_no = 79 OR icon_no = 90");
//mysql_query("UPDATE user_icon SET appearance = '鋼鉄ジーグ' WHERE icon_no = 80");
//mysql_query("UPDATE user_icon SET appearance = 'ポケットモンスター 金・銀' WHERE icon_no = 81");
//mysql_query("UPDATE user_icon SET category = 'ポケットモンスター' WHERE icon_no = 81");
//mysql_query("UPDATE user_icon SET appearance = 'はじめ人間ギャートルズ' WHERE icon_no = 82");
//mysql_query("UPDATE user_icon SET appearance = 'わかめて' WHERE icon_no = 83");
//mysql_query("UPDATE user_icon SET category = '東方Project' WHERE icon_no >= 87 AND icon_no <= 89");
//mysql_query("UPDATE user_icon SET appearance = '東方星蓮船' WHERE icon_no >= 87 AND icon_no <= 89");
//mysql_query("UPDATE user_icon SET category = '東方Project' WHERE icon_no >= 91 AND icon_no <= 96");
//mysql_query("UPDATE user_icon SET appearance = '東方二次' WHERE icon_no = 91");
//mysql_query("UPDATE user_icon SET appearance = '東方星蓮船' WHERE icon_no >= 92 AND icon_no <= 96");
//mysql_query('COMMIT'); //一応コミット

//DB 接続解除は OutputActionResult() 経由
OutputActionResult('処理完了', '処理完了。');
