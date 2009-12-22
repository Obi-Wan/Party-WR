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
    return "    <script type=\"text/javascript\" src=\"js/newsLoader.js\"></script>\n".
           "    <script type=\"text/javascript\" src=\"js/jquery-1.3.2.js\"></script>\n".
           "    <script type=\"text/javascript\" src=\"js/jquery-ui-1.7.2.custom.js\"></script>\n".
           "    <link type=\"text/css\" href=\"$this->template/jquery-ui/jquery-ui-1.7.2.custom.css\" rel=\"stylesheet\" />\n".
           "    <script type=\"text/javascript\">\n".
           "      $(function() {\n".
           "          $(\"#news_frame\").accordion();\n".
           "        });\n".
           "    </script>\n";
  }
  
  public function getLayers() {
    $output["type"] = "news";

    $output["functions"] = $this->tableOfFunctions;

    return $output;
  }
  
  public function getContents() {
    $count = 0;
    $output = '          <div class="title_news_frame" id="news_frame">'."\n";
    foreach ($this->theNews->unit as $unit) {
      $output .= "            <h3><a href=\"#\">".$this->getShortTimeStamp($unit->time).
                 " ".htmlentities($unit->title,ENT_QUOTES,"utf-8")."</a></h3>\n".
                 "            <div id=\"news-$count\">".
                 "              <p><b>Time:</b> <i>". $unit->time ."</i></p>".
                 "              <p><b>Cathegory:</b> <i>". $unit->cathegory ."</i></p>".
                 "              <p><b>Description:</b> <i>". $unit->description ."</i></p> ".
                 "            </div>\n";
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
