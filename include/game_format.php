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
  function BeginTalk($class){
    $this->cache .= "<table class=\"{$class}\">\n";
  }

  function AddTalk($userinfo, $sentence, $volume, $row_class = 'user-talk', $user_class = 'user-name'){
    $this->cache .= <<<WORDS
<tr class="{$row_class}">
<td class="{$user_class}">{$userinfo}</td>
<td class="say {$volume}">{$sentence}</td>
</tr>

WORDS;
  }

  function AddWhisper($useralias, $sentence, $volume = 'normal', $user_class = '', $say_class = ''){
    $this->cache .= <<<WORDS
<tr class="user-talk">
<td class="user-name {$user_class}">{$useralias}</td>
<td class="say {$volume} {$say_class}">{$sentence}</td>
</tr>

WORDS;
  }

  function AddSystemTalk($sentence, $class='system-user'){
    $this->cache .= <<<WORDS
<tr>
<td class="{$class}" colspan="2">{$sentence}</td>
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
    $this->cache = '';
  }
}
?>