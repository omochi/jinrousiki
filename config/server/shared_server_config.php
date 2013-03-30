<?php
//-- 村情報共有サーバの設定 --//
class SharedServerConfig {
  const DISABLE = false; //無効設定 <表示を [true:無効 / false:有効] にする>

  //表示する他のサーバのリスト
  static public $server_list = array(
    'kaguya' => array('name' => '輝夜鯖',
		      'url' => 'http://www42.atpages.jp/houraisankaguya/',
		      'encode' => 'UTF-8',
		      'separator' => '<!-- atpages banner tag -->',
		      'footer' => '</a><br>',
		      'disable' => false),

    'eva' => array('name' => 'Eva 鯖',
		   'url' => 'http://jinrou.kuroienogu.net/',
		   'encode' => 'EUC-JP',
		   'separator' => '',
		   'footer' => '</a><br>',
		   'disable' => true),

    'sanae' => array('name' => '早苗鯖',
		     'url' => 'http://alicegame.dip.jp/sanae/',
		     'encode' => 'UTF-8',
		     'separator' => '',
		     'footer' => '',
		     'disable' => false),

    'momiji' => array('name' => '椛鯖',
		     'url' => 'http://tm010.luna.ddns.vc/',
		     'encode' => 'UTF-8',
		     'separator' => '',
		     'footer' => '',
		     'disable' => false),

    'suisei' => array('name' => '翠星石鯖',
		      'url' => 'http://alicegame.dip.jp/suisei/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'sousei' => array('name' => '蒼星石テスト鯖',
		      'url' => 'http://alicegame.dip.jp/sousei/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'shink' => array('name' => '真紅',
		      'url' => 'http://alicegame.dip.jp/shinku/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'hina' => array('name' => '雛苺テスト鯖',
		    'url' => 'http://alicegame.dip.jp/hina/',
		    'encode' => 'UTF-8',
		    'separator' => '',
		    'footer' => '',
		    'disable' => false),

    'bourbonhouse' => array('name' => 'バーボンハウス鯖',
			    'url' => 'http://bourbonhouse.xsrv.jp/jinro/',
			    'encode' => 'EUC-JP',
			    'separator' => '',
			    'footer' => '',
			    'disable' => false),

    'bourbon_chaos' => array('name' => '裏世界鯖',
			     'url' => 'http://dynamis.xsrv.jp/jinro/',
			     'encode' => 'UTF-8',
			     'separator' => '',
			     'footer' => '',
			     'disable' => false),

    'takane' => array('name' => '四条劇場',
		      'url' => 'http://takanegm.com/',
		      'encode' => 'UTF-8',
		      'separator' => '',
		      'footer' => '',
		      'disable' => false),

    'inaba' => array('name' => '因幡鯖',
		     'url' => 'http://jinro.usamimi.info/',
		     'encode' => 'UTF-8',
		     'separator' => '',
		     'footer' => '',
		     'disable' => false),

    'prg_i' => array('name' => 'Twitter鯖',
		     'url' => 'http://www28.atpages.jp/pururiru/jinrou/',
		     'encode' => 'UTF-8',
		     'separator' => '<!-- atpages banner tag -->',
		     'footer' => '</a><br>',
		     'disable' => false)
			      );
}
