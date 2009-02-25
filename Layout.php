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
    private $foldingMenus;
    private $builtMenu;

    private $layoutsDir;
    private $chosenTemplate;
    
    private $hoverHandler;
    private $contentsManager;

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

    /* JavaScript Functions */

    public function  generateFoldingJS() {
?>
    <script type="text/javascript">
      var expandable = new Array();
      var folded = new Array();
<?php
      foreach ($this->foldingMenus as $singleItem) {
        print "      var ".$singleItem["name"]." = new Array();\n";
        if ($singleItem["leaves"]) {
          foreach ($singleItem["leaves"] as $subItem) {
            print "      {$singleItem["name"]}.push('" . $subItem . "');\n";
          }
        }
        
        print "      expandable['{$singleItem["name"]}'] = {$singleItem["name"]};\n";
        print "      folded['{$singleItem["name"]}'] = true;\n";
      }
?>
    
      function fold_unfold_expandable( branch, property ) {
          for (i = 0; i < expandable[branch].length; i++) {
              document.getElementById(expandable[branch][i]).style.display= property;
          }
      }

      function fold_unfold( idOgg ) {
        if (folded[idOgg]) {
          fold_unfold_expandable(idOgg,"inline");
          folded[idOgg] = false;
        } else {
          fold_unfold_expandable(idOgg,"none");
          folded[idOgg] = true;
        }
      }
    </script>
<?php
    }

    /* Menu Functions */

    public function generateMenu($menuStructure) {
        $output = '        <form method="post" action="index.php" '.
                  'class="menu"><div class="menu">' . "\n";
        foreach ($menuStructure as $menuEntry) {
            switch ($menuEntry["button_type"]) {
                case "button_menu": {
                    $output .= $this->generateButtMenu($menuEntry);
                    break;
                }
                case "button_expandable": {
                    $output .= $this->generateButtExpandable($menuEntry);
                    $newFoldingRoot["name"] = $menuEntry["name"];
                    foreach ($menuEntry["subitems"] as $item) {
                        $output .= $this->generateButtSubmenu($item);
                        $newFoldingRoot["leaves"][] = "{$item["root"]}_{$item["leaf"]}";
                    }
                    $this->foldingMenus[] = $newFoldingRoot;
                    unset ($newFoldingRoot);
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
        $this->builtMenu = $output; /* This will be in future the only admitted */
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

    function setContentsManager($cntMgr) {
        $this->contentsManager = $cntMgr;
    }

    public function generateHead($title, $additionalCssFiles) {
        ;
    }

    public function generateBody() {
        $domDocument = new DOMDocument();
        $domDocument->load(
            "{$this->layoutsDir}/{$this->chosenTemplate}/{$this->chosenTemplate}.xml");
        $node = $domDocument->getElementsByTagName("structure")->item(0);
        $bodyContent = $node->textContent;

        $bodyContent = str_replace("[MENU]",$this->builtMenu, $bodyContent );
        $bodyContent = str_replace("[LAYERS]",
            $this->contentsManager->hasLayers() ? 
                $this->contentsManager->getLayers() :
                "", 
            $bodyContent );
        $bodyContent = str_replace("[TEMPLATE]",
            "{$this->layoutsDir}/{$this->chosenTemplate}", $bodyContent );
        $bodyContent = str_replace("[CONTENTS]",
            $this->contentsManager->getContents(), $bodyContent );

        $bodyContent = str_replace("[BANNER-TOP]","", $bodyContent );
        $bodyContent = str_replace("[BANNER-BOTTOM]","", $bodyContent );

        print "$bodyContent";
    }
}
?>
