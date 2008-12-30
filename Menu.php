<?php

define("MENU_CLASS","1");

include_once 'HoverEffect.php';
include_once 'DiskIO.php';
include_once 'Banner.php';

/** This class is responsible for menu organization and visualization
 */
class Menu {
  
  protected $galleries;
  protected $template;
  protected $theHover;
  protected $siteStructure;
  
  function __construct ( StdUsefulData $data ) {
    $this->galleries = $data->ioResource->getGalleries ();
    $this->template = $data->templateDir;
    $this->theHover = $data->hoverHandler;
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

  function getMenuOld() {
    $output = '        <form method="post" action="index.php" class="menu"><div class="menu">' . "\n";
    
    $output .= $this->printLeafButtonMenu("home");
    $output .= $this->printLeafButtonMenu("news");
    
    $output .= $this->printRootButtonMenu("gallery");
    
    foreach ($this->galleries as $gallery_year) {
      $output .= $this->printLeafButtonSubmenu("gallery",$gallery_year);
    }
    
    $output .= $this->printLeafButtonMenu("contatti");
    $output .= $this->printLeafButtonMenu("staff");
    $output .= $this->printLeafButtonMenu("tech");
    
//    $output .= Banner::placeMenuBanner();
    $output .= "        </div></form>\n";
    return $output;
  }
  
  public function getMenu() {
    $output = '        <form method="post" action="index.php" class="menu"><div class="menu">' . "\n";
    
    foreach ($this->siteStructure as $name => $class_type) {
      if ($class_type == "Gallery") {
        $output .= $this->printRootButtonMenu($name);
        foreach ($this->galleries as $subgallery) {
          $output .= $this->printLeafButtonSubmenu($name,$subgallery);
        }
      } else {
        $output .= $this->printLeafButtonMenu($name);
      }
    }
    
//    $output .= Banner::placeMenuBanner();
    $output .= "        </div></form>\n";
    return $output;
  }
  
  private function printLeafButtonMenu($name) {
    $imageNormal = "{$this->template}/button_{$name}_normal.png";
    $imageHovered = "{$this->template}/button_{$name}_hover.png";
    $this->theHover->addHoveredImage($imageNormal, $imageHovered, $name);
    
    return "          <input id=\"$name\" name=\"$name\" type=\"image\" src=\"{$imageNormal}\" ".
           "class=\"button_menu\""." alt=\"$name\" onmouseover=\"mouseOver('$name')\" ".
           "onmouseout=\"mouseOut('$name')\" />\n";
  }
  
  private function printRootButtonMenu($name) {
    $imageNormal = "{$this->template}/button_{$name}_normal.png";
    $imageHovered = "{$this->template}/button_{$name}_hover.png";
    $this->theHover->addHoveredImage($imageNormal, $imageHovered, $name);
    
    return "          <img id=\"$name\" src=\"{$imageNormal}\" class=\"button_menu\" alt=\"$name\" ".
          "onmouseover=\"mouseOver('$name')\" onmouseout=\"mouseOut('$name')\" onclick=\"fold_unfold('$name')\"/>\n";
  }
  
  private function printLeafButtonSubmenu($root,$leaf) {
    $name = "{$root}_{$leaf}";
    $imageNormal = "{$this->template}/button_{$name}_normal.png";
    $imageHovered = "{$this->template}/button_{$name}_hover.png";
    $this->theHover->addHoveredImage($imageNormal, $imageHovered, $name);
    
    return '            <input id="'.$name.'" name="'.$leaf.'" value="'.$leaf.'" type="image" src="'.$imageNormal.
           '" class="button_submenu" alt="'.$leaf.'" onmouseover="mouseOver(\''.$name.'\')" '.
           'onmouseout="mouseOut(\''.$name."')\" />\n";
  }
}
?>

