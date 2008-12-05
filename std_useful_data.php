<?php

define("STD_USEFUL_DATA_STRUCT","1");

if (!defined("DISK_IO_CLASS")) {
  include 'disk_io.php';
}
if (!defined("HOVER_EFFECT_FUNCTIONS") ){
  include 'hover_effect.php';
}

/** This is just a data structure that defines a protocol on how to pass generic useful information
 * between classes, when instantiated.
 */
class StdUsefulData {
  public $ioResource;
  public $templateDir;
  public $hoverHandler;
  
  public function __construct (DiskIO $ioResource_n, $templateDir_n, HoverEffect $hoverHandler_n) {
  $this->ioResource = $ioResource_n;
  $this->templateDir = $templateDir_n;
  $this->hoverHandler = $hoverHandler_n;
  }
}

?>