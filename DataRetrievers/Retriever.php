<?php

include_once '../DiskIO.php';

/**
 * Description of Retriever
 *
 * @author ben
 */
class Retriever {

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
        break;
    }
    return $output;
  }

  public static function returnRequest( $requestedCathegory , $requestedSubCathegory) {
    $handler = new Retriever();
    return $handler->handleRequest($requestedCathegory, $requestedSubCathegory);
  }
}

header('Content-type: text/xml');
print Retriever::returnRequest($_REQUEST["id"],$_REQUEST["subid"]);

?>
