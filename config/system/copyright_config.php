<?php
//素材情報設定
class CopyrightConfig {
  //システム標準情報
  static $list = array(
    'システム' => array('PHP4 + MYSQLスクリプト' => 'http://f45.aaa.livedoor.jp/~netfilms/',
			'mbstringエミュレータ' => 'http://sourceforge.jp/projects/mbemulator/',
			'Twitter投稿モジュール' => 'https://github.com/abraham/twitteroauth'
			),
    '写真素材' => array('天の欠片' => 'http://keppen.web.infoseek.co.jp/'),
    'フォント素材' => array('あずきフォント' => 'http://azukifont.mints.ne.jp/')
		       );

  //追加情報
  static $add_list = array(
    '写真素材' => array('Le moineau - すずめのおやど -' => 'http://moineau.fc2web.com/'),
    /*
    'アイコン素材' => array('夏蛍' => 'http://natuhotaru.yukihotaru.com/',
                            'ジギザギのさいはて' => 'http://jigizagi.s57.xrea.com/')
    */
			   );
}
