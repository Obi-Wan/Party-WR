<?php

define("HOME_CLASS","1");

include_once 'DiskIO.php';
include_once 'NonLayeredContents.php';

/** Class similar the the GenericNonLayeredContents dut this handles title and subtitle, too.
 */
class Home extends NonLayeredContents {
  
  protected $title;
  protected $subtitle;
  
  function __construct (StdUsefulData $data) {
    $homeCont = $data->ioResource->getRawContents("home");
    
    $this->template = $data->templateDir;
    $this->theHover = $data->hoverHandler;
    
    $this->title = $homeCont->title;
    $this->subtitle = $homeCont->subtitle;
    $this->body = $homeCont->body;
    foreach($homeCont->images->item as $item) {
      $this->images[] = $item;
    }
    parent::bodyCook();
  }
  
  public function getContents() {
    $output = "<h1>$this->title</h1>\n" .
              "<h2>$this->subtitle</h2>\n";
    $output .= parent::getContents();
    return $output;
  }
  
  public function getCss() {
  }
  
}

?>