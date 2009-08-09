<?php
class Role extends DocumentBuilderExtension{
  var $actor;

  function Role($user){
    $this->actor = $user;
  }

  function __construct($user){
    $this->Role($user);
  }

  function Ignored(){ //$live をここで参照してまずいのなら書き換え願います
    global $day_night, $live;
    return ($live == 'live') && ($day_night == 'beforegame' || $day_night == 'aftergame');
  }

  function SameUser($userinfo){
    $result = strpos($userinfo, $this->actor->handle_name);
    shot ("<p>{$userinfo}から{$this->actor->handle_name}を探します。-> 結果:{$result}</p>");
    return ($result !== false) && ($result >= 0);
  }

  function Say($words, $volume){
    //TODO: 以下に発言用のコードを記述してください。
  }

  function WriteLastWords($content){
    //TODO: 以下に発言用のコードを記述してください。 //EntryLastWords() じゃないの？
  }

  function Object(){
    //TODO: 以下に発言用のコードを記述してください。
  }

  function Vote($target){
    //TODO: 以下に投票用のコードを記述してください。
  }

  function Action($target){
    //TODO: 以下に投票用のコードを記述してください。
  }
}
?>