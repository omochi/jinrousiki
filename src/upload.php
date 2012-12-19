<?php
define('JINRO_ROOT', '..');
require_once(JINRO_ROOT  . '/include/init.php');
if (Security::CheckValue($_FILES)) die;
Loader::LoadFile('src_class');
SrcHTML::Upload();
