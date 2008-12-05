<?php
  // Classe per l'estrazione delle news e la visualizzazione delle stesse.
define("NEWS_CLASS","1");

if (!defined("DISK_IO_CLASS")) {
  include 'disk_io.php';
}
if (!defined("LAYERED_CONTENTS_CLASS")) {
  include 'layered_contents.php';
}

/** Nice handler for news visualization.
 */
class News extends LayeredContents {
  
  protected $theNews;
  
  function __construct (StdUsefulData $data) {
    $this->theNews = $data->ioResource->getRawContents("news");
    $this->template = $data->templateDir;
    $this->theHover = $data->hoverHandler;
    
    $this->initFocusEffect();
  }
  
  public function getCss() {
    print '    <link rel="stylesheet" type="text/css" href="' . $this->template . '/news.css" />' . "\n";
  }
  
  public function getLayerFunctions() {
?>
  <script type="text/javascript">
    
    function singleItem( title_n, time_n, cathegory_n, description_n) {
      this.title = title_n;
      this.time = time_n;
      this.cathegory = cathegory_n;
      this.description = description_n;
    }
    
    var itemIndex = 0;
    var itemsList = new Array (
<?php
    foreach ($this->theNews->unit as $thisItem) {
      print "      new singleItem('".parent::lightCooking($thisItem->title)."',\n\"".
            parent::lightCooking($thisItem->time)."\",\n\"".
            parent::lightCooking($thisItem->cathegory)."\",\n\"".
            parent::lightCooking($thisItem->description)."\"),\n";
    }  // ricorda che mentre php parte con indice 1, JS parte come il C da indice 0
      print "      new singleItem('Fine News','','','Avete raggiunto la fine delle notizie')\n";
?>
                                );
    function raiseEffectFocus() {
      var darkLayer = document.getElementById('darkLayer');
      var displayerFrame = document.getElementById('displayerFrame');
      var displayerFrameBG = document.getElementById('displayerFrameBackground');
      darkLayer.style.display = "inline";
      displayerFrame.style.display = "inline";
      displayerFrameBG.style.display = "inline";
    }
    function removeEffectFocus() {
      var darkLayer = document.getElementById('darkLayer');
      var displayerFrame = document.getElementById('displayerFrame');
      var displayerFrameBG = document.getElementById('displayerFrameBackground');
      darkLayer.style.display = "none";
      displayerFrame.style.display = "none";
      displayerFrameBG.style.display = "none";
    }
    function initEffectFocus( item_id ) {
      itemIndex = item_id;
      putItem();
      raiseEffectFocus();
    }
    function putItem() {
      var title_in_frame = document.getElementById('title_in_frame');
      var time_in_frame = document.getElementById('time_in_frame');
      var cathegory_in_frame = document.getElementById('cathegory_in_frame');
      var description_in_frame = document.getElementById('description_in_frame');
      title_in_frame.innerHTML = itemsList[itemIndex].title;
      time_in_frame.innerHTML = itemsList[itemIndex].time;
      cathegory_in_frame.innerHTML = itemsList[itemIndex].cathegory;
      description_in_frame.innerHTML = itemsList[itemIndex].description;
    }
    function nextItem() {
      if (itemIndex >= (itemsList.length - 1)) {
        itemIndex = 0;
      } else {
        itemIndex++;
      }
      putItem();
    }
    function previousItem() {
      if (itemIndex <= 0) {
        itemIndex = itemsList.length - 1;
      } else {
        itemIndex--;
      }
      putItem();
    }
  </script>

<?php
  }
  
  protected function initFocusEffect() {
    $closeButtonNormal = "{$this->template}/button_close_normal.png";
    $closeButtonHover = "{$this->template}/button_close_hover.png";
    $previousButtonNormal = "{$this->template}/button_previous_normal.png";
    $previousButtonHover = "{$this->template}/button_previous_hover.png";
    $nextButtonNormal = "{$this->template}/button_next_normal.png";
    $nextButtonHover = "{$this->template}/button_next_hover.png";
    
    $this->theHover->addHoveredImage($closeButtonNormal,$closeButtonHover,"close");
    $this->theHover->addHoveredImage($previousButtonNormal,$previousButtonHover,"previous");
    $this->theHover->addHoveredImage($nextButtonNormal,$nextButtonHover,"next");
  }
  
  public function getLayers() {
    $closeButtonNormal = "{$this->template}/button_close_normal.png";
    $closeButtonHover = "{$this->template}/button_close_hover.png";
    $previousButtonNormal = "{$this->template}/button_previous_normal.png";
    $previousButtonHover = "{$this->template}/button_previous_hover.png";
    $nextButtonNormal = "{$this->template}/button_next_normal.png";
    $nextButtonHover = "{$this->template}/button_next_hover.png";
    
    print '    <img id="darkLayer" class="dark_layer" src="' . $this->template . '/dark_layer.png" />' . "\n" .
          '    <img id="displayerFrameBackground" class="displayer_frame_background" src="' . $this->template .
          '/sfondo_news_frame.png" />' . "\n" .
          '    <div id="displayerFrame" class="displayer_frame">' . "\n" .
          '      <img id="close" class="close_button" src="'.$closeButtonNormal.'" onclick="removeEffectFocus()" '.
          'onmouseover="mouseOver(\'close\')" onmouseout="mouseOut(\'close\')" />'."\n".
          '      <div id="" class="item_frame"> ' . "\n" .
          '        <p><b>Title:</b> <i id="title_in_frame"> </i></p>'. "\n" .
          '        <p><b>Time:</b> <i id="time_in_frame"> </i></p>'. "\n" .
          '        <p><b>Cathegory:</b> <i id="cathegory_in_frame"> </i></p>'. "\n" .
          '        <p><b>Description:</b> <i id="description_in_frame"> </i></p>'. "\n" .
          '      </div>' . "\n" .
          '      <img id="previous" class="previous_button" src="'.$previousButtonNormal.
          '" onclick="previousItem()" onmouseover="mouseOver(\'previous\')" onmouseout="mouseOut(\'previous\')" />'.
          "\n" .
          '      <img id="next" class="next_button" src="' . $nextButtonNormal . '" onclick="nextItem()" '.
          'onmouseover="mouseOver(\'next\')" onmouseout="mouseOut(\'next\')" />' . "\n" .
          "    </div>\n";
  }
  
  public function getContents() {
    $count = 0;
    print '          <div class="title_news_frame">'."\n";
    foreach ($this->theNews->unit as $unit) {
      print "            <div class=\"title_news\" id=\"$count\" onclick=\"initEffectFocus('$count')\">".
            $this->getShortTimeStamp($unit->time)." ".htmlentities($unit->title,ENT_QUOTES,"utf-8")."</div>\n";
      $count++;
    }
    print "          </div>\n";
  }
  
  private function getShortTimeStamp($rawTS) {
    $tempTS = split(" ",$rawTS);
    return "$tempTS[2]/$tempTS[1]/$tempTS[5]";
  }
  
/*  function getContents() {
    $output = '          <div class="title_news_frame">'."\n";
    foreach ($this->theNews->unit as $unit) {
      $output .= "            <div class=\"title_news\">".$this->getShortTimeStamp($unit->time)." ".
            htmlentities($unit->title,ENT_QUOTES,"utf-8")."</div>\n";
      //print_r (split(" ",$unit->time));
    }
    $output "          </div>\n";
    
    return $output; 
  }*/
}

?>
