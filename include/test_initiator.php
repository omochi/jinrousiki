<?php
if ($DEBUG_MODE){
  class TestParams extends RequestBase {
    function TestParams(){
      $this->GetItems(NULL, 'test_users', 'test_room');
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