<?php
//-- クラス定義 --//
//時間設定表示用クラス
class TimeCalculation{
  var $spend_day;      //非リアルタイム制の発言で消費される時間 (昼)
  var $spend_night;    //非リアルタイム制の発言で消費される時間 (夜)
  var $silence_day;    //非リアルタイム制の沈黙で経過する時間 (昼)
  var $silence_night;  //非リアルタイム制の沈黙で経過する時間 (夜)
  var $silence;        //非リアルタイム制の沈黙になるまでの時間
  var $sudden_death;   //制限時間を消費後に突然死するまでの時間
  var $die_room;       //自動廃村になるまでの時間
  var $establish_wait; //次の村を立てられるまでの待ち時間

  function __construct(){
    global $ROOM_CONF, $TIME_CONF;

    $day_seconds   = floor(12 * 60 * 60 / $TIME_CONF->day);
    $night_seconds = floor( 6 * 60 * 60 / $TIME_CONF->night);

    $this->spend_day      = ConvertTime($day_seconds);
    $this->spend_night    = ConvertTime($night_seconds);
    $this->silence_day    = ConvertTime($TIME_CONF->silence_pass * $day_seconds);
    $this->silence_night  = ConvertTime($TIME_CONF->silence_pass * $night_seconds);
    $this->silence        = ConvertTime($TIME_CONF->silence);
    $this->sudden_death   = ConvertTime($TIME_CONF->sudden_death);
    $this->die_room       = ConvertTime($ROOM_CONF->die_room);
    $this->establish_wait = ConvertTime($ROOM_CONF->establish_wait);
  }
}

//-- 関数定義 --//
//情報一覧ページ HTML ヘッダ出力
function OutputInfoPageHeader($title, $level = 0, $css = 'info'){
  $top  = str_repeat('../', $level + 1);
  $info = $level == 0 ? './' : str_repeat('../', $level);
  OutputHTMLHeader('[' . $title . ']', $css);
  echo <<<EOF
</head>
<body>
<h1>{$title}</h1>
<p>
<a href="{$top}" target="_top">&lt;= TOP</a>
<a href="{$info}" target="_top">←情報一覧</a>
</p>

EOF;
}

//役職情報ページ HTML ヘッダ出力
function OutputRolePageHeader($title){
  OutputHTMLHeader('新役職情報 - ' . '[' . $title . ']', 'new_role');
  echo <<<EOF
</head>
<body>
<h1>{$title}</h1>
<p>
<a href="../" target="_top">&lt;=情報一覧</a>
<a href="./" target="_top">&lt;-メニュー</a>
<a href="summary.php">←一覧表</a>
</p>

EOF;
}
