<?php

define("GALLERY_CLASS","1");

include_once 'HoverEffect.php';
include_once 'DiskIO.php';
include_once 'LayeredContents.php';

/** Class that generates the content with the images thumbnails, and the layers where to display bigger images.
 */
class Gallery extends LayeredContents {
  
  protected $listOfPhotos;
  protected $photosPath;
  protected $subGallery;

  protected $tableOfFunctions = array (
                    "close" => "removePhotoFocus()",
                    "next" => "nextPhoto()",
                    "previous" => "previousPhoto()",
                    "first" => "firstPhoto()",
                    "last" => "lastPhoto()",
                    "buttons_bar" => "hide_expose_bar()"
  );
  
  function __construct( StdUsefulData $data , $subGallery) {
    $this->photosPath = "gallery/{$subGallery}";
    $this->template = $data->templateDir;
    $this->listOfPhotos = $data->ioResource->getPhotosOfGallery($subGallery);
    $this->subGallery = $subGallery;
  }
  
  public function getCss() {
    $css = parent::getCss();
    $css[] = "gallery.css";
    return $css;
  }

  public function getLayerFunctions() {
    return '    <script type="text/javascript" src="js/jq-galleryLoader.js"></script>'."\n";
  }
  
  public function getLayers( ) {
    $output["type"] = "gallery";

    $output["functions"] = $this->tableOfFunctions;

    return $output;
  }

  public function getContents() {
    $count = 0;
    foreach ($this->listOfPhotos as $thisPhoto) {
      $output .= '          <img class="gallery_photo_thumb" id="' . $count .
                 '" alt="' . $count . '" src="' .
                 "$this->photosPath/thumbs/$thisPhoto" .
                 '" onclick="initPhotoFocus(\'' . $count . '\')" />' . "\n";
      $count++;
    }
    return $output;
  }

  public function getOnloadCode() {
    return "onload=\"initGallery('{$this->subGallery}')\"";
  }

  public function needsOnload() {
    return true;
  }
} 
?>
