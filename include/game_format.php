<?php
class DocumentBuilderExtension {
  function OnAddTalk($user, $talk, &$user_info, &$volume, &$sentence){
  }
  function OnAddWhisper($role, $talk, &$user_info, &$volume, &$message){
  }
}


class DocumentBuilder {
  var $extensions = array();

  function Generate(){
    global $ROLES, $role, $handle_name;

    $result = new DocumentBuilder();
    if(strpos($role, 'blinder') !== false){
      $wrapper = $ROLES->Instantiate('blinder');
      #$wrapper->Wrap($result);
      $result->Extend('blinder', $wrapper);
    }
    if(strpos($role, 'earplug') !== false){
      $wrapper = $ROLES->Instantiate('earplug');
      #$wrapper->Wrap($result);
      $result->Extend('earplug', $wrapper);
    }
    if(strpos($role, 'speaker') !== false){
      $wrapper = $ROLES->Instantiate('speaker');
      #$wrapper->Wrap($result);
      $result->Extend('speaker', $wrapper);
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

  function Extend($name, $extension){
    $this->extensions[$name] = $extension;
  }

  function BeginTalk($class){
    # if ($this->Deligate('BeginTalk', $class)) return;
    $this->cache .= "<table class=\"{$class}\">\n";
  }

  function RawAddTalk($symbol, $user_info, $sentence, $volume,
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

  function AddTalk($user, $talk){
    # if ($this->Deligate('AddTalk', $user, $talk)){ return; }
    $user_info = '<font style="color:'.$user->color.'">¢¡</font>'.$user->handle_name;
    if (strpos($talk->location, 'self_talk') !== false) {
      $user_info.= '<span>¤ÎÆÈ¤ê¸À</span>';
    }
    $volume = $talk->font_type;
    $sentence = $talk->sentence;
    foreach ($this->extensions as $ext) {
      $ext->OnAddTalk($user, $talk, $user_info, $volume, $sentence);
    }
    $this->cache .= <<<WORDS
<tr class="user-talk">
<td class="user-name">{$user_info}</td>
<td class="say {$volume}">{$sentence}</td>
</tr>

WORDS;
  }

  function AddWhisper($role, $talk){
    global $ROLES;
    # if ($this->Deligate('AddWhisper', $user_info, $sentence, $volume, $user_class, $say_class)) return;
    if (($user_info = $ROLES->GetWhisperingUserInfo($role, $user_class)) !== false) {
      $volume = $talk->font_type;
      $message = $ROLES->GetWhisperingSound($role, $talk, $say_class);
      foreach ($this->extensions as $ext){
        $ext->OnAddWhisper($role, $talk, $user_info, $volume, $message);
      }
      $this->cache .= <<<WORDS
<tr class="user-talk">
<td class="user-name {$user_class}">{$user_info}</td>
<td class="say {$say_class} {$volume}">{$message}</td>
</tr>

WORDS;
    }
  }

  function AddSystemTalk($sentence, $class = 'system-user'){
    # if ($this->Deligate('AddSystemTalk', $sentence, $class)) return;
    $this->cache .= <<<WORDS
<tr>
<td class="{$class}" colspan="2">{$sentence}</td>
</tr>

WORDS;
  }

  function AddSystemMessage($class, $sentence, $additional_class=''){
    # if ($this->Deligate('AddSystemMessage', $class, $sentence, $additional_class)) return;
    $this->cache .= <<<WORDS
<tr class="system-message {$additional_class}">
<td class="{$class}" colspan="2">{$sentence}</td>
</tr>

WORDS;
  }

  function EndTalk(){
    # if ($this->Deligate('EndTalk')) return;
    echo $this->cache.'</table>'."\n";
    $this->cache = '';
  }
}
?>
