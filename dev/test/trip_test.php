<?php
define('JINRO_ROOT', '../..');
require_once(JINRO_ROOT . '/include/init.php');
$INIT_CONF->LoadClass('GAME_CONF');

OutputHTMLHeader('�ȥ�åץƥ��ȥġ���');
  echo <<<EOF
</head>
<body>
<form method="POST" action="trip_test.php">
<input type="hidden" name="command" value="on">
<label>�ȥ�åץ���</label><input type="text" name="key" size="20" value="">
</form>

EOF;
if($_POST['command'] == 'on'){
  PrintData(ConvertTrip($_POST['key']), '�Ѵ����');
}
OutputHTMLFooter();
