<?php
define("CONTENTS_CLASS","1");

/** Class that give the structure of classes that should then manage contents and display them.
 */
abstract class Contents {
  
  /** Abstract method that precisely says if the class has layers or not
   *
   * @return a boolean
   */
  abstract public function hasLayers();
  
  /** Abstract method that processes and returns the "cooked" contents to display
   *
   * @return the content to display.
   */
  abstract public function getContents();
  
  /** Abstract method that returns the CSS file
   *
   * @return the entry in the html header that says where to get the CSS of this class
   */
  abstract public function getCss();
  
  protected $theHover;
  protected $template;
  
  /** First sort of "cooking": makes characters html compliant and removes \n.
   *
   * @param $rawString Is the completely uncooked content.
   * @return semi cooked content.
   */
  public function lightCooking( $rawString ) {
    return str_replace("\n","<br />", htmlentities($rawString,$ENT_QUOTES,"utf-8") );
  }
  
  /** Applies the bbCode formatting
   *
   * @param $rawString The unformatted text (with markers)
   * @return formatted text
   */
  public function bbCodeCooking( $rawString ) {
    $temp = str_replace("[b]","<span style=\"font-weight: bold;\">", $rawString );
    $temp = str_replace("[/b]","</span>", $temp );
    $temp = str_replace("[i]","<span style=\"font-style: italic;\">", $temp );
    $temp = str_replace("[/i]","</span>", $temp );
    $temp = str_replace("[u]","<span style=\"text-decoration: underline;\">", $temp );
    return str_replace("[/u]","</span>", $temp );
  }
}

?>
