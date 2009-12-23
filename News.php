<?php
  // Classe per l'estrazione delle news e la visualizzazione delle stesse.
define("NEWS_CLASS","1");

include_once 'DiskIO.php';
include_once 'NonLayeredContents.php';

/** Nice handler for news visualization.
 */
class News extends NonLayeredContents {
  
  protected $theNews;
  
  function __construct (StdUsefulData $data) {
    $this->theNews = $data->ioResource->getRawContents("news");
    $this->template = $data->templateDir;
  }
  
  public function getCss() {
    $css = parent::getCss();
    $css[] = "news.css";
    $css[] = "jquery-ui/jquery-ui-1.7.2.custom.css";
    return $css;
  }

  public function hasAdditionalJS() {
    return true;
  }

  public function getAdditionalJS() {
    return "    <script type=\"text/javascript\" src=\"js/jquery-1.3.2.js\"></script>\n".
           "    <script type=\"text/javascript\" src=\"js/jquery-ui-1.7.2.custom.js\"></script>\n".
           "    <script type=\"text/javascript\">\n".
           "      $(function() {\n".
           "          $(\"#news_frame\").accordion();\n".
           "        });\n".
           "    </script>\n";
  }
  
  public function getContents() {
    $count = 0;
    $output = '          <div class="title_news_frame" id="news_frame">'."\n";
    foreach ($this->theNews->unit as $unit) {
      $output .= "            <h3><a href=\"#\">".$this->getShortTimeStamp($unit->time).
                 " - ".htmlentities($unit->title,ENT_QUOTES,"utf-8")."</a></h3>\n".
                 "            <div id=\"news-$count\">".
                 "              <p><span class=\"bold\">Time:</span>".
                  " <span class=\"italic\">". $unit->time ."</span></p>".
                 "              <p><span class=\"bold\">Cathegory:</span>".
                  " <span class=\"italic\">". $unit->cathegory ."</span></p>".
                 "              <p><span class=\"bold\">Description:</span>".
                  " <span class=\"italic\">". $unit->description ."</span></p> ".
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
}

?>
