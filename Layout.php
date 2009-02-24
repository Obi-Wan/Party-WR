<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once 'HoverEffect.php';
include_once 'Banner.php';

/**
 * Description of Layout
 *
 * @author ben
 */
class Layout {
    private $layoutsDir;
    private $chosenTemplate;
    private $hoverHandler;

    public function __construct($layouts, DiskIO $ioResource,
            HoverEffect $hoverEffect) {
        $this->layoutsDir = $layouts;
        $this->hoverHandler = $hoverEffect;
        $this->chosenTemplate = $_REQUEST["template"];
        /* We are now interested in finding which template was chosen */
        if ( /* If no template chosen, or it's not between the ones installed.. */
            $this->chosenTemplate == "" ||
            ( ! in_array( $this->chosenTemplate , $ioResource->getTemplates() ) )
           )
        { /* We do fallback to default */
            $this->chosenTemplate = "default";
        }
    }

    public function generateMenu($menuStructure) {
        $output = '        <form method="post" action="index.php" '.
                  'class="menu"><div class="menu">' . "\n";
        foreach ($menuStructure as $menuEntry) {
            switch ($menuEntry["button_type"]) {
                case "button_menu": {
                    $output .= $this->generateButtMenu($menuEntry);
                    break;
                }
                case "button_submenu": {
                    $output .= $this->generateButtSubmenu($menuEntry);
                    break;
                }
                case "button_expandable": {
                    $output .= $this->generateButtExpandable($menuEntry);
                    break;
                }
                default: {
                    print "Elemento non classificato!!<br/>";
                    /* lancia eccezione! */
                }
            }
        };

//        $output .= Banner::placeMenuBanner();
        $output .= "        </div></form>\n";
        return $output;
    }

    private function generateButtMenu($buttonData) {
        $name = $buttonData["name"];
        $imageNormal = "{$this->layoutsDir}/{$this->chosenTemplate}/button_{$name}_normal.png";
        $imageHovered = "{$this->layoutsDir}/{$this->chosenTemplate}/button_{$name}_hover.png";
        $this->hoverHandler->
            addHoveredImage($imageNormal, $imageHovered, $name);

        return "          <input id=\"$name\" name=\"$name\" type=\"image\" ".
               "src=\"{$imageNormal}\" class=\"button_menu\"".
               " alt=\"$name\" onmouseover=\"mouseOver('$name')\" ".
               "onmouseout=\"mouseOut('$name')\" />\n";
    }

    private function generateButtExpandable($buttonData) {
        $name = $buttonData["name"];
        $imageNormal = "{$this->layoutsDir}/{$this->chosenTemplate}/button_{$name}_normal.png";
        $imageHovered = "{$this->layoutsDir}/{$this->chosenTemplate}/button_{$name}_hover.png";
        $this->hoverHandler->
            addHoveredImage($imageNormal, $imageHovered, $name);

        return "          <img id=\"$name\" src=\"{$imageNormal}\" ".
               "class=\"button_menu\" alt=\"$name\" ".
               "onmouseover=\"mouseOver('$name')\" ".
               "onmouseout=\"mouseOut('$name')\" ".
               "onclick=\"fold_unfold('$name')\"/>\n";
    }

    private function generateButtSubmenu($buttonData) {
        $root = $buttonData["root"];
        $leaf = $buttonData["leaf"];
        $name = "{$root}_{$leaf}";
        $imageNormal = "{$this->layoutsDir}/{$this->chosenTemplate}/button_{$name}_normal.png";
        $imageHovered = "{$this->layoutsDir}/{$this->chosenTemplate}/button_{$name}_hover.png";
        $this->hoverHandler->
            addHoveredImage($imageNormal, $imageHovered, $name);

        return '            <input id="'.$name.'" name="'.$leaf.'" value="'.
               $leaf.'" type="image" src="'.$imageNormal.
               '" class="button_submenu" alt="'.$leaf.
               '" onmouseover="mouseOver(\''.$name.'\')" '.
               'onmouseout="mouseOut(\''.$name."')\" />\n";
    }

    public function getChosenTemplate() {
        return "$this->layoutsDir/$this->chosenTemplate";
    }
}
?>
