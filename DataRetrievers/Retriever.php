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
    $output  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    switch ($reqId) {
      case "gallery" :
        $photosPath = "gallery/{$reqSubId}";
        $listOfPhotos = $this->ioResource->getPhotosOfGallery($reqSubId);
        $output .= "<photos>\n";
        foreach ($listOfPhotos as $photo) {
          $output .= "  <photo>{$photosPath}/{$photo}</photo>\n";
        }
        $output .= "</photos>\n";
        break;
      case "news" :
        $output .= "<news>\n";
        $rawContents = $this->ioResource->getRawContents("news");
        foreach ($rawContents->unit as $thisItem) {
          $output .= "  <item>\n";
          $output .= "    <title><![CDATA[".
                     parent::lightCooking($thisItem->title)."]]></title>\n";
          $output .= "    <time><![CDATA[".
                     parent::lightCooking($thisItem->time)."]]></time>\n";
          $output .= "    <cathegory><![CDATA[".
                     parent::lightCooking($thisItem->cathegory)."]]></cathegory>\n";
          $output .= "    <description><![CDATA[".
                     parent::lightCooking($thisItem->description)."]]></description>\n";
          $output .= "  </item>\n";
        }
        $output .= "</news>\n";
        break;
    }
    return $output;
  }

  public static function returnRequest( $requestedCathegory , $requestedSubCathegory) {
    $handler = new Retriever();
    return $handler->handleRequest($requestedCathegory, $requestedSubCathegory);
  }

  /** Method that precisely says if the class has layers or not
   *
   * @return boolean
   */
  public function hasLayers() { return false; }

  /** Method that processes and returns the "cooked" contents to display
   *
   * @return the content to display.
   */
  public function getContents() { return ""; }
}

// Now we handle the answer :D
header('Content-type: text/xml');
print Retriever::returnRequest($_REQUEST["id"],$_REQUEST["subid"]);

?>
