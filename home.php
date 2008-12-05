<?php

define("HOME_CLASS","1");

if (!defined("DISK_IO_CLASS")) {
  include 'disk_io.php';
}
if (!defined("NON_LAYERED_CONTENTS_CLASS")) {
  include 'non_layered_contents.php';
}

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
    print "<h1>$this->title</h1>\n" .
          "<h2>$this->subtitle</h2>\n";
    parent::getContents();
  }
  
  public function getCss() {
  }
  
}

?>