<?php
require_once(dirname(__FILE__) . '/../include/game_functions.php');


//MySQLに接続
if( ($dbHandle = ConnectDatabase()) == NULL)
{
	exit;
}

print("<html><head>");
print("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=EUC-JP\">");
print("<title>汝は人狼なりや？[過去ログ編集]</title> \r\n");
print("<style type=\"text/css\"><!--\r\n");

$background_color = $background_color_aftergame;
$text_color = $text_color_aftergame;
$a_color = 'blue';
$a_vcolor = 'blue';
$a_acolor = 'red';

	print("body{background-color:white;background-image: url(\"img/old_log_bg.jpg\");background-repeat: no-repeat;");
	print("background-position: 100% 100%;background-attachment: fixed }");
	print("table{filter:alpha(opacity=80,enabled=80)}");
	print("body{background-color:$background_color;color:$text_color;}\r\n");

print("A:link{ color: $a_color; } A:visited{ color: $a_vcolor; } A:active{ color: $a_acolor; } A:hover{ color: red; } \r\n");
print(".day{  background-color:$background_color_day; color : $text_color_day;} \r\n");
print(".night{background-color:$background_color_night; color: $text_color_night;} \r\n");
print(".beforegame{background-color:$background_color_beforegame; color : $text_color_beforegame;} \r\n");
print(".aftergame{ background-color:$background_color_aftergame; color : $text_color_aftergame;} \r\n");
print(".heaven{ background-color:#cccccc; color : black;} \r\n");
print(".column { MARGIN: 0px; BORDER-LEFT:#ffffff 1px solid; PADDING-LEFT:6px; BORDER-TOP:#ffffff 1px solid; PADDING-TOP:3px;
	BORDER-RIGHT:  #ffffff 0px solid; PADDING-RIGHT:  3px; BORDER-BOTTOM: #ffffff 0px solid; PADDING-BOTTOM: 3px; 
	COLOR: #ffffff; BACKGROUND-COLOR: #526CD6; } \r\n");
	
print(".row { MARGIN: 0px; BORDER-LEFT:#ffffff 1px solid; PADDING-LEFT:6px; BORDER-TOP:#ffffff 1px solid; PADDING-TOP:3px; 
	BORDER-RIGHT:  #ffffff 0px solid; PADDING-RIGHT:  3px; BORDER-BOTTOM: #ffffff 0px solid; PADDING-BOTTOM: 3px; 
	COLOR: #333333; BACKGROUND-COLOR: #F2EACE; } \r\n");
print("--></style>\r\n");

print("</head><body>\r\n");


OldLogListOutput;



print("</body></html> \r\n");

//変数の基本設定を行う
//$keep_num:データベースに残す村数
//$base_url:人狼スクリプトを設置してあるアドレス

//$keep_numについて
//user_entryテーブルで村数の確認をしている為、余り小さくしてはいけない。
//稼動中の村のテーブルにまで手を出してしまう可能性がある（現状は１０〜１５がよろしいかと）。
$keep_num = 15;

//ベースアドレスの設定
$base_url = "http://www12.atpages.jp/yaruo/jinro/";

//現在のDB中にある部屋総数をカウントする
$res_room_stat = mysql_query("
	SELECT COUNT(room_no) AS rooms 
	FROM room 
	WHERE status = 'finished'");
$room_count = mysql_result($res_room_stat, 0, 'rooms');
printf('現在の村数：%s<br>', $room_count ? $room_count : 0);

//ＨＴＭＬ化されていない村の数が$keep_numより大きかった場合、村数が$keep_numと同じになるまでログ保存とテーブル削除を実行する
if ($keep_num < $room_count){
	//オプションを指定してログを保存する。
	function SaveRoomLog($room_no, $option, $surfix = ''){
		$log_url = "{$base_url}old_log.php?room_no={$room_no}&log_mode=on{$option}";
		$logdata = file_get_contents($log_url);
		return file_put_contents(dirname(__FILE__) . "/../log/{$room_no}_{$surfix}.html", $logdata) !== false;
	}
	
	//削除する村番号の最小値を取得する
	$res_delete_tail = mysql_query("
		SELECT MIN(kept.room_no) - 1 AS tail
		FROM (SELECT room_no FROM room
      WHERE status = 'finished'
      ORDER BY room_no DESC
      LIMIT $keep_num) AS kept
		");
	
	$delete_range = sprintf('room_no BETWEEN 1 AND %d', mysql_result($res_delete_tail, 0, 'tail'));
	$errors = array();
	$res_deleted = mysql_unbuffered_query("SELECT room_no FROM room WHERE {$delete_range}");
	while (($deleted = mysql_fetch_assoc($res_deleted)) !== false) {
		$deleted_no = $deleted['room_no'];
		//テーブルデータの削除
		if (SaveRoomLog($deleted_no, '&heaven_talk=on')
      && SaveRoomLog($deleted_no, '&heaven_talk=on&reverse_log=on', 'rev')){
			echo "部屋番号 {$deleted_no} を保存しました。この部屋は削除されます。<br>";
		}
		else {
			$errors[] = $deleted_no;
			echo "ファイル出力エラーが発生しました。部屋番号 $deleted_no の削除はスキップされます。<br>";
		}
	}
  printf('condition : range=%s / except=%s', $delete_range, $except);
	mysql_query("DELETE FROM talk WHERE $except $delete_range");
	mysql_query("DELETE FROM user_entry WHERE $except $delete_range");
	mysql_query("DELETE FROM system_message WHERE $except $delete_range");
	mysql_query("DELETE FROM vote WHERE $except $delete_range");
  mysql_query("UPDATE room SET status='log' WHERE $except $delete_range");
}
else { 
	echo "現在テーブルデータは最小限です。これ以上削除する必要はありません。";
}

//MySQLとの接続を閉じる
DisconnectDatabase($dbHandle);

?>
