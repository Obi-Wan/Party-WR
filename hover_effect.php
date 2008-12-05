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
    print '  <script type="text/javascript">'."\n";
    print "    var hoveredImages = new Array();\n";
    foreach ($this->hoveredImages as $id => $image) {
      print "    hoveredImages[\"$id\"] = new Image();\n";
      print "    hoveredImages[\"$id\"].src = \"$image\";\n";
    }
    print "    var normalImages = new Array();\n";
    foreach ($this->normalImages as $id => $image) {
      print "    normalImages[\"$id\"] = new Image();\n";
      print "    normalImages[\"$id\"].src = \"$image\";\n";
    }
    print "  </script>\n";
  }
  
  static public function printHoverFunctions() {
?>
  <script type="text/javascript">
    function mouseOver( idOgg ) {
      var elem = document.getElementById( idOgg );
      var elemNewSrc = new String(elem.src);
      elem.src = elemNewSrc.replace("normal","hover");
    }
    function mouseOut( idOgg ) {
      var elem = document.getElementById( idOgg );
      var elemNewSrc = new String(elem.src);
      elem.src = elemNewSrc.replace("hover","normal");
    }
  </script>
<?php
  }
  
  public function printNewHoverFunctions() {
?>
  <script type="text/javascript">
    function mouseOver( idOgg ) {
      var elem = document.getElementById( idOgg );
      elem.src = hoveredImages[idOgg].src;
    }
    function mouseOut( idOgg ) {
      var elem = document.getElementById( idOgg );
      elem.src = normalImages[idOgg].src;
    }
  </script>
<?php
  }
}
  // fine
?>
