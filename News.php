<?php
  // Classe per l'estrazione delle news e la visualizzazione delle stesse.
define("NEWS_CLASS","1");

include_once 'DiskIO.php';
include_once 'LayeredContents.php';

/** Nice handler for news visualization.
 */
class News extends LayeredContents {
  
  protected $theNews;
  
  protected $tableOfFunctions = array (
                    "close" => "removeEffectFocus()",
                    "next" => "nextItem()",
                    "previous" => "previousItem()"
  );
  
  function __construct (StdUsefulData $data) {
    $this->theNews = $data->ioResource->getRawContents("news");
    $this->template = $data->templateDir;
  }
  
  public function getCss() {
    return "news.css";
  }

  public function getLayerFunctions() {
    return "<script type=\"text/javascript\" src=\"newsLoader.js\"></script>\n";
  }
  
  public function getLayers() {
    $output["type"] = "news";

    $output["functions"] = $this->tableOfFunctions;

    return $output;
  }
  
  public function getContents() {
    $count = 0;
    $output = '          <div class="title_news_frame">'."\n";
    foreach ($this->theNews->unit as $unit) {
      $output .= "            <div class=\"title_news\" id=\"$count\" onclick=\"initEffectFocus('$count')\">".
            $this->getShortTimeStamp($unit->time)." ".htmlentities($unit->title,ENT_QUOTES,"utf-8")."</div>\n";
      $count++;
    }
    $output .= "          </div>\n";
    return $output;
  }
  
  private function getShortTimeStamp($rawTS) {
    $tempTS = split(" ",$rawTS);
    return "$tempTS[2]/$tempTS[1]/$tempTS[5]";
  }

  public function getOnloadCode() {
    return "onload=\"doRequest()\"";
  }

  public function needsOnload() {
    return true;
  }
}

?>
