<?php
class SiteSummary {
  var $rooms = array();

  function Import($url, $rss) {
    $summary = new SiteSummary();
    $summary->url = $url;
  }

  function Export() {
    $feen = new FeedEngine();
    $feen->SetChannel($SERVER_CONF->title, $SERVER_CONF->site_root, $SERVER_CONF->comment);
    $rooms = RoomDataSet::LoadOpeningRooms();
    foreach ($rooms->rows as $room) {
      $title = "{$room->room_name}¼ ��� {$room->room_comment}";
      $url = str_replace('//', '/', "{$SERVER_CONF->site_root}/game_view.php?room={$room->id}");
      $description = <<<XHTML
<p>{$SERVER_CONF->title}�ˤ�{$room->room_name}¼ ��� {$room->comment}[{$room->id}����]�������ޤ�����</p>
<h2>����</h2>
<ul>
<li></li>
</ul>
XHTML;
      $feen->AddItem($room->room_name, $url, $description);
    }
    return $feen->Pack(JINRO_ROOT.'/feed/site.rss');
  }
}
