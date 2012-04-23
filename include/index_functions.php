<?php
//-- クラス定義 --//
//-- メニューリンク表示用クラス --//
class MenuLinkBuilder extends MenuLinkConfig{
  //交流用サイト表示
  function Output(){
    //初期化処理
    $this->str = '';
    $this->header = '<li>';
    $this->footer = "</li>\n";

    $this->AddHeader('交流用サイト');
    $this->AddLink($this->list);
    $this->AddFooter();

    if(count($this->add_list) > 0){
      $this->AddHeader('外部リンク');
      foreach($this->add_list as $group => $list){
	$this->str .= $this->header . $group . $this->footer;
	$this->AddLink($list);
      }
      $this->AddFooter();
    }
    echo $this->str;
  }

  //ヘッダ追加
  private function AddHeader($title){
    $this->str .= '<div class="menu">' . $title . "</div>\n<ul>\n";
  }

  //リンク生成
  private function AddLink($list){
    $header = $this->header . '<a href="';
    $footer = '</a>' . $this->footer;
    foreach($list as $name => $url) $this->str .= $header . $url . '">' . $name . $footer;
  }

  //フッタ追加
  private function AddFooter(){ $this->str .= "</ul>\n"; }
}

//-- 関数定義 --//
//ヘッダー出力
function OutputIndexHeader(){
  OutputHTMLHeader(ServerConfig::$title . ServerConfig::$comment, 'index');
  echo "</head>\n<body>\n";
  if (ServerConfig::$back_page != '') {
    echo '<a href="' . ServerConfig::$back_page . '">←戻る</a><br>'."\n";
  }
}

//掲示板情報出力
function OutputBBSInfo(){
  global $BBS_CONF;

  if($BBS_CONF->disable) return;
  if(! $BBS_CONF->CheckConnection($BBS_CONF->raw_url)){
    $str = $BBS_CONF->host . ": Connection timed out ({$BBS_CONF->time} seconds)\n";
    echo $BBS_CONF->GenerateBBS($str);
    return;
  }

  //スレッド情報を取得
  $url = $BBS_CONF->raw_url . $BBS_CONF->thread . 'l' . $BBS_CONF->size . 'n';
  if(($data = @file_get_contents($url)) == '') return;
  if($BBS_CONF->encode != ServerConfig::$encode){
    $data = mb_convert_encoding($data, ServerConfig::$encode, $BBS_CONF->encode);
  }
  $str = '';
  $str_stack = explode("\n", $data);
  array_pop($str_stack);
  foreach($str_stack as $res){
    $res_stack = explode('<>', $res);
    $str .= '<dt>' . $res_stack[0] . ' : <font color="#008800"><b>' . $res_stack[1] .
      '</b></font> : ' . $res_stack[3] . ' ID : ' . $res_stack[6] . '</dt>' . "\n" .
      '</dt><dd>' . $res_stack[4] . '</dd>';
  }
  echo $BBS_CONF->GenerateBBS($str);
}

//バージョン情報出力
function OutputScriptInfo(){
  global $SCRIPT_INFO;

  echo "Powered by {$SCRIPT_INFO->package} {$SCRIPT_INFO->version} from {$SCRIPT_INFO->developer}";
  if(ServerConfig::$admin) echo '<br>Founded by: ' . ServerConfig::$admin;
}
