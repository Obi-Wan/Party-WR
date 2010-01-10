<?php
  // Funzioni JS che danno l'effetto hover
define("HOVER_EFFECT_FUNCTIONS","1");

/** This Class is responsible for managing prefetching and applying of the HoverEffect.
 */
class HoverEffect {
  
  protected $hoveredImages;
  protected $normalImages;
  protected $shadowImages;

  protected $listOfObjects;

  public function  __construct() {
    $this->listOfObjects = array();
    
    $this->hoveredImages = array();
    $this->normalImages = array();
    $this->shadowImages = array();
  }
  
  public function addHoveredImage( $imageNormal, $imageHovered , $id ) {
    if ( !array_key_exists($id, $this->listOfObjects) ) {
      $this->listOfObjects[$id] = "hover";

      $this->hoveredImages[$id] = $imageHovered;
      $this->normalImages[$id] = $imageNormal;
    } else throw new Exception;
  }

  public function addShadowImage($imageName, $id) {
    if ( !array_key_exists($id, $this->listOfObjects) ) {
      $this->listOfObjects[$id] = "shadow";

      $this->shadowImages[$id] = $imageName;
    } else throw new Exception;
  }
  
  public function initHoveredCache() {
    $output  = '    <script type="text/javascript">'."\n";
    $output .= "      var listObjImages = new Array();\n";
    foreach ($this->listOfObjects as $id => $type) {
      $output .= "      listObjImages[\"$id\"] = \"$type\";\n";
    }
    $output .= "      var hoveredImages = new Array();\n";
    foreach ($this->hoveredImages as $id => $image) {
      $output .= "      hoveredImages[\"$id\"] = new Image();\n";
      $output .= "      hoveredImages[\"$id\"].src = \"$image\";\n";
    }
    $output .= "      var normalImages = new Array();\n";
    foreach ($this->normalImages as $id => $image) {
      $output .= "      normalImages[\"$id\"] = new Image();\n";
      $output .= "      normalImages[\"$id\"].src = \"$image\";\n";
    }
    $output .= "      var shadowImages = new Array();\n";
    foreach ($this->shadowImages as $id => $image) {
      $output .= "      shadowImages[\"$id\"] = \"$image\";\n";
    }
    $output .= "    </script>\n";
    return $output;
  }
  
  public function printHoverFunctions() {
    return '    <script type="text/javascript" src="js/hoverFunctions.js"></script>'."\n";
  }
}
?>
