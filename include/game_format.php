<?php
class DocumentBuilderBase {
  function BeginTalk(){ }
  function AddTalk($userinfo, $sentence, $volume) { }
  function AddWhisper($useralias, $sentence, $volume, $user_extension='', $say_extension='') { }
  function AddSystemTalk($sentence) { }
  function AddSystemMessage($class, $message, $additional='') { }
  function EndTalk() { }
}

class DocumentBuilder extends DocumentBuilderBase {
  var $cache;
  function BeginTalk(){
    $this->cache .= '<table class="talk">'."\n";
  }

  function AddTalk($userinfo, $sentence, $volume){
    $this->cache .= <<<WORDS
<tr class="user-talk">
<td class="user-name">{$userinfo}</td>
<td class="say {$volume}">{$sentence}</td>
</tr>

WORDS;
  }

  function AddWhisper($useralias, $sentence, $volume, $user_extension = '', $say_extension = ''){
    $this->cache .= <<<WORDS
<tr class="user-talk">
<td class="user-name {$user_extension}">{$useralias}</td>
<td class="say {$volume} {$say_extension}">{$sentence}</td>
</tr>

WORDS;
  }

  function AddSystemTalk($sentence){
    $this->cache .= <<<WORDS
<tr>
<td class="system-user" colspan="2">{$sentence}</td>
</tr>

WORDS;
  }

  function AddSystemMessage($class, $message, $additional=''){
    $this->cache .= <<<WORDS
<tr class="system-message {$additional}">
<td class="{$class}" colspan="2">{$message}</td>
</tr>

WORDS;
  }

  function EndTalk(){
    echo $this->cache.'</table>'."\n";
  }
}
?>