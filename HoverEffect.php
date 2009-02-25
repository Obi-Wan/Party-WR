<?php
  // Funzioni JS che danno l'effetto hover
define("HOVER_EFFECT_FUNCTIONS","1");

/** This Class is responsible for managing prefetching and applying of the HoverEffect.
 */
class HoverEffect {
  
  protected $hoveredImages;
  protected $normalImages;
  
  public function addHoveredImage( $imageNormal, $imageHovered , $id ) {
    $this->hoveredImages[$id] = $imageHovered;
    $this->normalImages[$id] = $imageNormal;
  }
  
  public function initHoveredCache() {
    $output  = '    <script type="text/javascript">'."\n";
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
    $output .= "    </script>\n";
    return $output;
  }
  
  public function printHoverFunctions() {
    return '    <script type="text/javascript" src="hoverFunctions.js"></script>'."\n";
  }
}
  // fine
?>
