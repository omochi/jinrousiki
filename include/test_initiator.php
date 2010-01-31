<?php
/*
  このファイルは消去予定です。
  新しい情報を定義する場合は request_class.php を編集してください。
*/
if ($DEBUG_MODE){
  class TestParams extends RequestBase {
    function TestParams(){
      $this->GetItems(NULL, 'test_users', 'test_room', 'test_mode');
      $this->is_virtual_room = isset($this->test_users);
    }
    function __construct(){
      $this->TestParams();
    }
  }
}

function AttachTestParameters($request){
  global $DEBUG_MODE;
  if ($DEBUG_MODE && ($request instanceof RequestBase)){
    $request->TestItems = new TestParams();
  }
}
?>