<?php
class FeedEngine {
  var $url;
  var $title;
  var $description;
  var $items = array();

  function Initialize($filename){
    if (include(JINRO_INC . "/feedengine/$filename")) {
      $segments = explode('_', substr($filename, 0, -4));
      foreach($segments as $segment){
        $class .= ucfirst($segment);
      }
      return new $class();
    }
    return false;
  }

  function SetChannel($title, $url, $description='') {
    if (empty($this->url) && !empty($title)) {
      $this->title = $title;
      $this->url = $url;
      $this->description = $description;
    }
  }

  function AddItem($title, $url, $description) {
    $this->items[] = array(
      'title'=>$title,
      'url'=>$url,
      'description'=>$description
    );
  }

  function Pack($filename) {
    $list_items = '';
    $item_contents = '';
    foreach($this->items as $item) {
      extract($item, EXTR_PREFIX_ALL, 'item');
      $list_items .= '<rdf:li rdf:resource="'.$item_url.'"/>';
      $item_contents .= <<<XML_RDF
<item rdf:about="{$item_url}">
<title>{$item_title}</title>
<link>{$item_url}</link>
<description>{$item_description}</description>
</item>
XML_RDF;
    }

    return <<<XML_RDF
<?xml version="1.0" charset="UTF-8"?>
<rdf:RDF xml:lang="ja" xmlns="http://purl.org/rss/1.0/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<channel about="{$this->url}feed/{$filename}">
<title>{$this->title}</title>
<link>{$this->url}</link>
<description>{$this->description}</description>
<items>
<rdf:Seq>
{$list_items}
</rdf:Seq>
</items>
</channel>
{$item_contents}
</rdf:RDF>
XML_RDF;
  }
}
