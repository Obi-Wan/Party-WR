<?php

include_once '../DiskIO.php';
include_once '../Contents.php';

/**
 * Description of Retriever
 *
 * @author ben
 */
class Retriever extends Contents {

  private $ioResource;

  public function __construct() {
    $this->ioResource = new DiskIO("..");
  }

  public function handleRequest($reqId, $reqSubId) {
    switch ($reqId) {
      case "gallery" :
        $photosPath = "gallery/{$reqSubId}";
        $listOfPhotos = $this->ioResource->getPhotosOfGallery($reqSubId);
        $output  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $output .= "<photos>\n";
        foreach ($listOfPhotos as $photo) {
          $output .= "  <photo>{$photosPath}/{$photo}</photo>\n";
        }
        $output .= "</photos>\n";
        break;
      case "news" :
        $output = $this->ioResource->getRawContents("news");
        break;
    }
    return $output;
  }

  public static function returnRequest( $requestedCathegory , $requestedSubCathegory) {
    $handler = new Retriever();
    return $handler->handleRequest($requestedCathegory, $requestedSubCathegory);
  }

  /** Abstract method that precisely says if the class has layers or not
   *
   * @return a boolean
   */
  public function hasLayers() { return false; }

  /** Abstract method that processes and returns the "cooked" contents to display
   *
   * @return the content to display.
   */
  public function getContents() { return ""; }
}

header('Content-type: text/xml');
print Retriever::returnRequest($_REQUEST["id"],$_REQUEST["subid"]);

?>
