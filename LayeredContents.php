<?php

define("LAYERED_CONTENTS_CLASS","1");

include_once 'Contents.php';

/** Stub class that gives the structure of how will be organized classes that make avaible layers for 
 * data visualization.
 */
abstract class LayeredContents extends Contents {
  
  /** Implements the abstract method
   *
   * @return True since this class has Layers to show.
   */
  public function hasLayers() {
    return true;
  }
  
  /** This method will instantiate the layers.
   */
  abstract public function getLayers();
  
  /** Instantiates the JavaScript functions that the layer needs.
   */
  abstract public function getLayerFunctions();
}

?>