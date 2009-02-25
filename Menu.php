<?php

define("MENU_CLASS","1");

include_once 'DiskIO.php';

/** This class is responsible for menu organization and visualization
 */
class Menu {
  
  protected $galleries;
  protected $siteStructure;
  
  function __construct ( StdUsefulData $data ) {
    $this->galleries = $data->ioResource->getGalleries ();
  }

  function setSiteStructure($siteStructure) {
    $this->siteStructure = $siteStructure;
  }
  
  public function getMenu() {
    foreach ($this->siteStructure as $name => $class_type) {
      if ($class_type == "Gallery") {
        $newRootItem["button_type"] = "button_expandable";
        $newRootItem["name"] = $name;
        foreach ($this->galleries as $subgallery) {
          $newLeafItem["button_type"] = "button_submenu";
          $newLeafItem["leaf"] = $subgallery;
          $newLeafItem["root"] = $name;
          $newRootItem["subitems"][] = $newLeafItem;
          unset ($newLeafItem);
        }
        $output[] = $newRootItem;
        unset ($newRootItem);
      } else {
        $newItem["button_type"] = "button_menu";
        $newItem["name"] = $name;
        $output[] = $newItem;
        unset ($newItem);
      }
    }
    return $output;
  }
}
?>

