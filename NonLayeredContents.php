<?php

define("NON_LAYERED_CONTENTS_CLASS","1");

include_once 'Contents.php';

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
      for ($count = 0;$count <= count($this->images);$count++) {
        $isInTemplate = ($this->images[$count]->template == "true");
        $needsHover = ($this->images[$count]->hover == "true");

        $imageSrc = (($isInTemplate) ? $this->template : "") ;
        $imageSrc .= $this->images[$count]->src;
        if ($needsHover) {
          $this->theHover->addHoveredImage($imageSrc,
                         str_replace("normal","hover",$imageSrc),"img{$count}");
        }
        $this->bodyCooked = str_replace("[img id=$count]",
              "<img id=\"img{$count}\" alt=\"img{$count}\" src=\"$imageSrc".
              "\" style=\"".$this->images[$count]->style.'" '.
              ( ($needsHover) ? "onmouseover=\"mouseOver('img{$count}')\"".
              " onmouseout=\"mouseOut('img{$count}')\"" : "")." />",
              $this->bodyCooked);
      }
    }
  }
  
  /** Retrieves the already cooked and formetted text.
   */
  public function getContents() {
    return "<h3 style=\"{$this->body->style}\">{$this->bodyCooked}</h3>\n";
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