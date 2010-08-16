<?php
if($DEBUG_MODE){
  //コメントとカテゴリを指定して、ログに新しい行を追加します。
  //引数
  //$comment : ログに追加するメッセージの本体を指定します。
  //$category : ログに追加するデータの分類名を指定します。この引数は省略可能です。
  //	指定しなかった場合、規定値として'general'が設定されます。
  function shot($comment, $category = 'general'){
    global $PAPARAZZI;
    return $PAPARAZZI->shot($comment, $category);
  }

  //テスト対象を起動してからこの関数が呼ばれるまでの時間を計測し、結果を挿入します。
  //引数
  //$label : 測定時間にラベルを付けます。この引数は省略可能です。指定しなかった場合、ラベルは表示されません。
  function InsertBenchResult($label = false){
    global $PAPARAZZI;
    $PAPARAZZI->InsertBenchResult($label);
  }

  //トレースログを挿入します。
  function InsertLog(){
    global $PAPARAZZI;
    $PAPARAZZI->InsertLog();
  }

  //トレースログの出力文字列を取得します。
  function CollectLog($force = false){
    global $PAPARAZZI;
    return $PAPARAZZI->CollectLog($force);
  }

  //トレースログをデータベースに書き込みます。
  function SaveLog($room_no, $uname, $action){
    global $PAPARAZZI;
    $PAPARAZZI->save($room_no, $uname, $action);
  }
}
else{
  //デバッグモードでない場合、空の関数定義が提供されます。
  function shot($comment, $category = 'general'){ return $comment; }
  function InsertBenchResult(){}
  function InsertLog(){}
  function CollectLog($force = false){}
  function SaveLog($room_no, $uname, $action){}
}
