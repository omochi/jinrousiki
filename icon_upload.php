<?php
require_once('include/init.php');
if (Security::CheckValue($_FILES)) die;
Loader::LoadFile('icon_upload_class');
if (UserIconConfig::DISABLE) {
  HTML::OutputResult('ユーザアイコンアップロード', '現在アップロードは停止しています');
}
Loader::LoadRequest('RequestIconUpload');
isset(RQ::$get->command) ? IconUpload::Execute() : IconUpload::Output();
