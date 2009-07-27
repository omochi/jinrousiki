<?php
class Role extends DocumentBuilder {
  var $actor;

  function Role($user){
    $this->actor = $user;
  }

  function __construct($user){
    $this->Role($user);
  }

  function Ignored(){
    global $day_night;
    return $day_night == 'beforegame' || $day_night == 'aftergame';
  }

  function SameUser($userinfo){
    $result = strpos($userinfo, $this->actor->handle_name);
    shot ("<p>{$userinfo}から{$this->actor->handle_name}を探します。-> 結果:{$result}</p>");
    return ($result !== false) && (0 < $result);
  }

  function Say($words, $volume){
    //TODO: 以下に発言用のコードを記述してください。
  }

  function WriteLastWords($content){
    //TODO: 以下に発言用のコードを記述してください。
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