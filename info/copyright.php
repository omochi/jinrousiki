<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT . '/include/init.php');
Loader::LoadFile('copyright_config');
OutputInfoPageHeader('謝辞・素材', 0, 'info');
OutputCopyright();
HTML::OutputFooter();

//-- 関数 --//
//謝辞・素材情報出力
function OutputCopyright(){
  $stack = CopyrightConfig::$list;
  foreach (CopyrightConfig::$add_list as $class => $list) {
    $stack[$class] = array_key_exists($class, $stack) ?
      array_merge($stack[$class], $list) : $list;
  }

  foreach ($stack as $class => $list) {
    $str = '<h2>' . $class . "</h2>\n<ul>\n";
    foreach ($list as $name => $url) {
      $str .= '<li><a href="' . $url . '">' . $name . "</a></li>\n";
    }
    echo $str . "</ul>\n";
  }

  $str = <<<EOF
<h2>パッケージ情報</h2>
<ul>
<li>PHP Ver. %s</li>
<li>%s %s (Rev. %d)</li>
<li>LastUpdate: %s</li>
</ul>

EOF;

  printf($str, PHP_VERSION, ScriptInfo::PACKAGE, ScriptInfo::VERSION, ScriptInfo::REVISION,
	 ScriptInfo::LAST_UPDATE);
}
