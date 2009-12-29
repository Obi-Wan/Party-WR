<?php

define("GENERIC_NON_LAYERED_CONTENTS_CLASS","1");

include_once 'Structs/StdUsefulData.php';
include_once 'NonLayeredContents.php';

/** Generic class that displays generic text content, appling generic processing
 */
class GenericNonLayeredContents extends NonLayeredContents {

  private function __construct(StdUsefulData $data, $wantedContents) {
    $contents = $data->ioResource->getRawContents($wantedContents);

    $this->template = $data->templateDir;
    $this->theHover = $data->hoverHandler;
    
    $this->body = $contents->body;
    $this->images = array();
    foreach($contents->images->item as $item) {
      $this->images[] = $item;
    }

    parent::bodyCook();
  }

  static function getInstance(StdUsefulData $data , $wantedContents) {
    return new GenericNonLayeredContents($data, $wantedContents);
  }

  static function getErrorPageGeneric(StdUsefulData $data) {
    return new GenericNonLayeredContents($data, "error_page");
  }

  static function getErrorPageNoSuchGallery(StdUsefulData $data) {
    return new GenericNonLayeredContents($data, "wrong_gallery");
  }
}

?>