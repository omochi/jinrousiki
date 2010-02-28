<?php
/*
  このファイルはデータベース書き換え作業支援関数を集めたものです
  管理者が必要に応じて編集する→アップロード→ブラウザでアクセス
  という使い方を想定しています。

  開発者のテスト用コードそのままなので要注意！
 */
#exit;
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
//SendCommit();
//ReconstructEstablishTime();
//ReconstructStartTime();
//ReconstructFinishTime();
OutputActionResult('処理完了', '処理完了。');

//-- 関数 --//
/*
  Ver. 1.4.0 β3 より実装されたユーザアイコンテーブルの追加情報入力支援関数
  type:[appearance / category / author] (出典 / カテゴリ / 作者)
  value: 入力内容
  from / to: 入力対象アイコン (from 〜 to まで)
*/
function UpdateIconInfo($type, $value, $from, $to = NULL){
  $query = isset($to) ? "{$from} <= icon_no AND icon_no <= {$to}" : "icon_no = {$from}";
  mysql_query("UPDATE user_icon SET {$type} = '{$value}' WHERE {$query}");
}

function ReconstructEstablishTime($test = false){
  $room_list = FetchArray("SELECT room_no FROM room WHERE establish_time IS NULL ORDER BY room_no");
  //PrintData($room_list);
  $keyword = '村作成：';
  foreach($room_list as $room_no){
    #if($room_no == 434) return;
    $query = "SELECT sentence, talk_id FROM talk WHERE room_no = {$room_no} AND sentence LIKE '%{$keyword}%'";
    $talk = FetchAssoc($query, true);
    if(count($talk) > 0){
      $str = array_pop(explode($keyword, $talk['sentence']));
      if($test){
	$time = FetchResult("SELECT STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s')");
	PrintData($time, $room_no . ': ' . $str);
      }
      else{
	$query = "UPDATE room SET establish_time = STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s') " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
	SendQuery("DELETE FROM talk WHERE talk_id = " . $talk['talk_id']);
	SendQuery("OPTIMIZE TABLE talk", true);
      }
    }
    else{
      continue;
      $query = "SELECT time FROM talk WHERE room_no = {$room_no} ORDER BY talk_id";
      $talk = FetchResult($query);
      if($test){
	$time = gmdate('Y/m/d (D) H:i:s', $talk);
	$date = FetchResult('SELECT establish_time FROM room WHERE room_no = ' . $room_no); //FetchResult("SELECT FROM_UNIXTIME('{$talk}' - 32400)");
	//$time = date('Y/m/d (D) H:i:s', $talk);
	//$date = FetchResult("SELECT FROM_UNIXTIME('{$talk}')");
	PrintData($date, $room_no . ': ' . $time);
      }
      else{
	$query = "UPDATE room SET establish_time = FROM_UNIXTIME('{$talk}' - 32400) " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
      }
    }
  }
}

function ReconstructStartTime($test = false){
  $room_list = FetchArray("SELECT room_no FROM room WHERE start_time IS NULL ORDER BY room_no");
  $keyword = 'ゲーム開始：';
  //PrintData($room_list);
  foreach($room_list as $room_no){
    #if($room_no == 434) return;
    $query = "SELECT sentence, talk_id FROM talk WHERE room_no = {$room_no} AND sentence LIKE '%{$keyword}%'";
    $talk = FetchAssoc($query, true);
    if(count($talk) > 0){
      $str = array_pop(explode($keyword, $talk['sentence']));
      if($test){
	$time = FetchResult("SELECT STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s')");
	PrintData($time, $room_no . ': ' . $str);
      }
      else{
	$query = "UPDATE room SET start_time = STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s') " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
	SendQuery("DELETE FROM talk WHERE talk_id = " . $talk['talk_id']);
	SendQuery("OPTIMIZE TABLE talk", true);
      }
    }
    else{
      continue;
      $query = "SELECT time FROM talk WHERE room_no = {$room_no} AND date = 1 ORDER BY talk_id";
      $talk = FetchResult($query);
      if($test){
	$time = gmdate('Y/m/d (D) H:i:s', $talk);
	$date = FetchResult("SELECT FROM_UNIXTIME('{$talk}' - 32400)");
	PrintData($date, $room_no . ': ' . $time);
      }
      else{
	$query = "UPDATE room SET start_time = FROM_UNIXTIME('{$talk}' - 32400) " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
      }
    }
  }
}

function ReconstructFinishTime($test = false){
  $room_list = FetchArray("SELECT room_no FROM room WHERE finish_time IS NULL ORDER BY room_no");
  //PrintData($room_list);
  $keyword = 'ゲーム終了：';
  foreach($room_list as $room_no){
    #if($room_no == 434) return;
    $query = "SELECT sentence, talk_id FROM talk WHERE room_no = {$room_no} AND sentence LIKE '%{$keyword}%'";
    $talk = FetchAssoc($query, true);
    if(count($talk) > 0){
      $str = array_pop(explode($keyword, $talk['sentence']));
      if($test){
	$time = FetchResult("SELECT STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s')");
	PrintData($time, $room_no . ': ' . $str);
      }
      else{
	$query = "UPDATE room SET finish_time = STR_TO_DATE('{$str}', '%Y/%m/%d (%a) %H:%i:%s') " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
	SendQuery("DELETE FROM talk WHERE talk_id = " . $talk['talk_id']);
	SendQuery("OPTIMIZE TABLE talk", true);
      }
    }
    else{
      continue;
      $query = "SELECT time FROM talk WHERE room_no = {$room_no} AND ! (location LIKE '%aftergame%') ORDER BY talk_id DESC";
      $talk = FetchResult($query);
      if($test){
	$time = gmdate('Y/m/d (D) H:i:s', $talk);
	$date = FetchResult("SELECT FROM_UNIXTIME('{$talk}' - 32400)");
	PrintData($date, $room_no . ': ' . $time);
      }
      else{
	$query = "UPDATE room SET finish_time = FROM_UNIXTIME('{$talk}' - 32400) " .
	  "WHERE room_no = {$room_no}";
	SendQuery($query);
      }
    }
  }
}