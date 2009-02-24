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

  function printFoldingJS() {
?>
    <script type="text/javascript">
      var galleriesFolded = true;
    
      function fold_unfold_galleries( property ) {
<?php
    foreach ($this->galleries as $gallery_year) {
      print "        document.getElementById('gallery_" . $gallery_year . '\').style.display= property;' . "\n";
    }
?>
      }
      function fold_unfold( idOgg ) {
        switch (idOgg) {
          case "gallery": {
            if (galleriesFolded) {
              fold_unfold_galleries("inline");
              galleriesFolded = false;
            } else {
              fold_unfold_galleries("none");
              galleriesFolded = true;
            }
            break;
          }
        }
      }
    </script>
<?php
  }
  
  public function getMenu() {
    foreach ($this->siteStructure as $name => $class_type) {
      if ($class_type == "Gallery") {
        $newItem["button_type"] = "button_expandable";
        $newItem["name"] = $name;
        $output[] = $newItem;
        unset ($newItem);
        foreach ($this->galleries as $subgallery) {
          $newItem["button_type"] = "button_submenu";
          $newItem["leaf"] = $subgallery;
          $newItem["root"] = $name;
          $output[] = $newItem;
          unset ($newItem);
        }
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

