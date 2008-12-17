<?php

define("BANNER_CLASS","1");

/** This class makes it possible to add ads to the site.
 */
class Banner {
  public static function printSmallBanner() {
?>
    <script type="text/javascript">
    function smallBanner() {
  /* Just an example of banner if you are hosted @ www.altervista.org */
  /*
      //<![CDATA[
      document.write('<s'+'cript type="text/javascript" src="http://ad.altervista.org/js.ad/size=468X60/r='+new Date().getTime()+'"><\/s'+'cript>');
      //]]>
    */
    }
    </script>
<?php
  }
  public static function initMenuBanner() {
?>
    <script type="text/javascript">
    function menuBanner() {
  /* Just an example of banner if you are hosted @ www.altervista.org */
  /*
      //<![CDATA[
      google_color_border = "000000";
      google_color_bg = "000000";
      google_color_link = "FFFFFF";
      google_color_url = "FFFFFF";
      google_color_text = "FFFFFF";
      document.write('<s'+'cript type="text/javascript" src="http://ad.altervista.org/js.ad/size=125X125/r='+new Date().getTime()+'"><\/s'+'cript>');
      //]]>
    */
    }
    </script>
<?php
  }
  public static function initLowerBanner() {
?>
    <script type="text/javascript">
    function lowerBanner() {
  /* Just an example of banner if you are hosted @ www.altervista.org */
  /*
      //<![CDATA[
      google_color_border = "FFFFFF";
      google_color_bg = "FFFFFF";
      google_color_link = "000000";
      google_color_url = "000000";
      google_color_text = "000000";
      document.write('<s'+'cript type="text/javascript" src="http://ad.altervista.org/js.ad/size=728X90/r='+new Date().getTime()+'"><\/s'+'cript>');
      //]]>
    */
    }
    </script>
<?php
  }
  public static function putSmallBanner() {
    $output = '      <div style="z-index:1; position:absolute; top:5px;">'."\n";
    $output .= '        <script type="text/javascript">smallBanner();</script>'."\n";
    $output .= "      </div>\n";
    return $output;
  }
  public static function placeMenuBanner() {
    $output = '      <div style="z-index:1; position:relative; right:35px; margin-top:30px;">'."\n";
    $output .= '        <script type="text/javascript">menuBanner();</script>'."\n";
    $output .= "      </div>\n";
    return $output;
  }
  public static function placeLowerBanner() {
    $output = '      <div style="z-index:1; position:absolute; bottom:-125px; margin:20px;">'."\n";
    $output .= '        <script type="text/javascript">lowerBanner();</script>'."\n";
    $output .= "      </div>\n";
    return $output;
  }
}

?>
