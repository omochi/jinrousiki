<?php
class DocumentBuilder {
  function Generate(){
    global $ROLES, $role, $handle_name;

    $result = new DocumentBuilder();
    if(strpos($role, 'blinder') !== false){
      $wrapper = $ROLES->Instantiate('blinder');
      $wrapper->Wrap($result);
      $result = $wrapper;
    }
    if(strpos($role, 'earplug') !== false){
      $wrapper = $ROLES->Instantiate('earplug');
      $wrapper->Wrap($result);
      $result = $wrapper;
    }
    return $result;
  }

  function Wrap(&$implementor){
    $this->implementor = $implementor;
  }

  function Deligate($function){
    if (isset($this->implementor)){
      $args = func_get_args();
      call_user_func_array(array($this->implementor, $function), array_slice($args, 1));
      return true;
    }
    return false;
  }

  function BeginTalk($class){
    if ($this->Deligate('BeginTalk', $class))
      return;
    $this->cache .= "<table class=\"{$class}\">\n";
  }

  function AddTalk($symbol, $user_info, $sentence, $volume,
		   $row_class = 'user-talk', $user_class = 'user-name'){
    if ($this->Deligate('AddTalk', $symbol, $user_info, $sentence, $volume, $row_class, $user_class))
      return;
    $this->cache .= <<<WORDS
<tr class="{$row_class}">
<td class="{$user_class}">{$symbol}{$user_info}</td>
<td class="say {$volume}">{$sentence}</td>
</tr>

WORDS;
  }

  function AddWhisper($user_info, $sentence, $volume = 'normal', $user_class = '', $say_class = ''){
    global $MESSAGE;
    if ($this->Deligate('AddWhisper', $user_info, $sentence, $volume, $user_class, $say_class))
      return;
    $this->cache .= <<<WORDS
<tr class="user-talk">
<td class="user-name {$user_class}">{$user_info}</td>
<td class="say {$volume} {$say_class}">{$MESSAGE->$sentence}</td>
</tr>

WORDS;
  }

  function AddSystemTalk($sentence, $class = 'system-user'){
    if ($this->Deligate('AddSystemTalk', $sentence, $class))
      return;
    $this->cache .= <<<WORDS
<tr>
<td class="{$class}" colspan="2">{$sentence}</td>
</tr>

WORDS;
  }

  function AddSystemMessage($class, $sentence, $additional_class=''){
    if ($this->Deligate('AddSystemMessage', $class, $sentence, $additional_class))
      return;
    $this->cache .= <<<WORDS
<tr class="system-message {$additional_class}">
<td class="{$class}" colspan="2">{$sentence}</td>
</tr>

WORDS;
  }

  function EndTalk(){
    if ($this->Deligate('EndTalk'))
      return;
    echo $this->cache.'</table>'."\n";
    $this->cache = '';
  }
}
?>
