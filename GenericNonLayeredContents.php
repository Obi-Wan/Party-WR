<?php

define("GENERIC_NON_LAYERED_CONTENTS_CLASS","1");

include_once 'Structs/StdUsefulData.php';
include_once 'NonLayeredContents.php';

/** Generic class that displays generic text content, appling generic processing
 */
class GenericNonLayeredContents extends NonLayeredContents {
  
  function __construct (StdUsefulData $data , $wantedContents) {
    $contatti = $data->ioResource->getRawContents($wantedContents);
    
    $this->template = $data->templateDir;
    $this->theHover = $data->hoverHandler;
    
    $this->body = $contatti->body;
    foreach($contatti->images->item as $item) {
      $this->images[] = $item;
    }
    parent::bodyCook();
  }
}

?>