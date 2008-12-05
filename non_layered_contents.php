<?php

define("NON_LAYERED_CONTENTS_CLASS","1");

if (!defined("CONTENTS_CLASS")) {
  include 'contents.php';
}

/** Class that gives the structure of classes that retrieve non layered contents.
 */
abstract class NonLayeredContents extends Contents {
  
  protected $body;
  protected $images;
  protected $bodyCooked;
  
  /** Completely cooks the body, applying every sort of transformation.
   */
  protected function bodyCook() {
    /* Applyies first simple cooking */
    $this->bodyCooked = parent::lightCooking($this->body->contents);
    
    /* Applyies bbcode formatting */
    $this->bodyCooked = parent::bbCodeCooking($this->bodyCooked);
    
    /* For every image it checks in the body if it's called and substitutes it. */
    if (is_array($this->images)) {
      for ($count = 0;$count <= max(array_keys($this->images));$count++) {
        if ($this->images[$count]->hover == "true") {
          $this->theHover->addHoveredImage($this->images[$count]->src,str_replace("normal","hover",
                                                                      $this->images[$count]->src),"img{$count}");
        }
        $this->bodyCooked = str_replace("[img id=$count]","<img id=\"img{$count}\" alt=\"img{$count}\" src=\"".
                    ( ($this->images[$count]->template == "true") ? $this->template : "").$this->images[$count]->src .
                    "\" style=\"".$this->images[$count]->style.'" '.
                    ( ($this->images[$count]->hover == "true") ? "onmouseover=\"mouseOver('img{$count}')\" onmouseout=\"mouseOut('img{$count}')\"" : "")." />",$this->bodyCooked);
      }
    }
  }
  
  /** Retrieves the already cooked and formetted text.
   */
  public function getContents() {
    print "<h3 style=\"{$this->body->style}\">{$this->bodyCooked}</h3>\n";
  }
  
  /** Implements the abstract method
   *
   * @return False since this class has not visualization layers.
   */
  public function hasLayers() {
    return false;
  }
}

?>