<?php
exit //̤�������դ����Ѥ��ʤ����ȡ�
require_once 'contenttype_set.php';  //�إå��������ܸ�EUC-JP����
require_once 'game_functions.php';


//MySQL����³
if( ($dbHandle = ConnectDatabase()) == NULL)
{
	exit;
}


print("<html><head>");
print("<meta http-equiv=\"Content-Type\" content=\"text/html; charset=EUC-JP\">");
print("<title>��Ͽ�ϵ�ʤ�䡩[�����Խ�]</title> \r\n");
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

	//�ѿ��δ��������Ԥ�
	//$keep_num:�ǡ����١����˻Ĥ�¼��
	//$base_url:��ϵ������ץȤ����֤��Ƥ��륢�ɥ쥹
	
	//$keep_num�ˤĤ���
	//user_entry�ơ��֥��¼���γ�ǧ�򤷤Ƥ���١�;�꾮�������ƤϤ����ʤ���
	//��ư���¼�Υơ��֥�ˤޤǼ��Ф��Ƥ��ޤ���ǽ��������ʸ����ϣ�������������������ȡˡ�
	$keep_num = 15;
	
	//�١������ɥ쥹������
	$base_url = "http://www12.atpages.jp/yaruo/jinro/";

	//���ߤ�DB��ˤ�����������򥫥���Ȥ���
	$res_oldlog_list = mysql_query("select room_no from room where status = 'finished'");
	$finished_room_count = mysql_num_rows($res_oldlog_list);
	print("���ߤ�¼����".$finished_room_count."<br>");

	//�Ǥ�Ť������Υʥ�С����������
	$res_oldlog_list = mysql_query("select room_no from user_entry WHERE room_no > 0 ORDER BY room_no");
	$oldest_room_no = mysql_result($res_oldlog_list,0,0);
	print("���ߤ�HTML�����줿¼��(�¿���-1����)��".$oldest_room_no."<br>");

	//�Ǥ⿷���������Υʥ�С����������
	$res_oldlog_list = mysql_query("select room_no from room where status = 'finished' ORDER BY room_no DESC");
	$latest_room_no = mysql_result($res_oldlog_list,0,0);
	$latest_room_no = $latest_room_no - $keep_num;
	$now_room_count = $finished_room_count - $oldest_room_no;


	//�ȣԣ̲ͣ�����Ƥ��ʤ�¼�ο���$keep_num����礭���ä���硢¼����$keep_num��Ʊ���ˤʤ�ޤǥ���¸�ȥơ��֥�����¹Ԥ���
	if($now_room_count >= $keep_num){
		for(;$oldest_room_no <=$latest_room_no ;$oldest_room_no++){
			$log_url = $base_url."old_log.php?log_mode=on&room_no=".$oldest_room_no."&heaven_talk=on";
			$logdata = file_get_contents($log_url);
			$error = file_put_contents("log/".$oldest_room_no.".html",$logdata);
			$log_url = $base_url."old_log.php?log_mode=on&room_no=".$oldest_room_no."&reverse_log=on&heaven_talk=on";
			$logdata = file_get_contents($log_url);
			$error_r = file_put_contents("log/".$oldest_room_no."_r.html",$logdata);
			$message = "�����ֹ�".$oldest_room_no."����¸���ޤ���<br>";
			echo $message;
			//�ơ��֥�ǡ����κ��
			if(($error == FALSE) || ($error_r == FALSE)){
				$message = "�ե�������ϥ��顼��ȯ�������١��ơ��֥�ǡ����κ���ϹԤ��ޤ���Ǥ�����<br>";
				echo $message;
			}
			else{
				mysql_query("DELETE FROM talk WHERE room_no = $oldest_room_no");
				mysql_query("DELETE FROM user_entry WHERE room_no = $oldest_room_no");
				mysql_query("DELETE FROM system_message WHERE room_no = $oldest_room_no");
				mysql_query("DELETE FROM vote WHERE room_no = $oldest_room_no");
				$message = "�����ֹ�".$oldest_room_no."�Υơ��֥�ǡ��������ƺ�����ޤ���<br>";
				echo $message;
			}
		}
	}
	else{
	print("���ߥơ��֥�ǡ����ϺǾ��¤Ǥ�������ʾ�������ɬ�פϤ���ޤ���");
	}


//MySQL�Ȥ���³���Ĥ���
DisconnectDatabase($dbHandle);

?>