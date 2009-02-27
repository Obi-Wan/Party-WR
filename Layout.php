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

  private $template;

  private $hoverHandler;
  private $contentsManager;

  public function __construct($layouts, DiskIO $ioResource,
          HoverEffect $hoverEffect) {
    $this->hoverHandler = $hoverEffect;
    $template = $_REQUEST["template"];
    /* We are now interested in finding which template was chosen */
    if ( /* If no template chosen, or it's not between the ones installed.. */
        $template == "" ||
        ( ! in_array( $template , $ioResource->getTemplates() ) )
       )
    { /* We do fallback to default */
      //$template = "default";
      $template = "summer2009";
      $this->template = "{$layouts}/{$template}";
    }
  }

  /* JavaScript Functions */

  public function  generateFoldingJS() {
    $output = <<<EOT
    <script type="text/javascript">
      var expandable = new Array();
      var folded = new Array();

EOT;
    foreach ($this->foldingMenus as $singleItem) {
      $output .= "      var ".$singleItem["name"]." = new Array();\n";
      if ($singleItem["leaves"]) {
        foreach ($singleItem["leaves"] as $subItem) {
          $output .= "      {$singleItem["name"]}.push('" . $subItem . "');\n";
        }
      }

      $output .= "      expandable['{$singleItem["name"]}'] = {$singleItem["name"]};\n";
      $output .= "      folded['{$singleItem["name"]}'] = true;\n";
    }
    $output .= <<<EOT
      function fold_unfold_expandable( branch, property ) {
        for (i = 0; i < expandable[branch].length; i++) {
          document.getElementById(expandable[branch][i]).style.display= property;
        }
      }

      function fold_unfold_expandable_animation( branch, property ) {
        for (i = 0; i < expandable[branch].length; i++) {
          var time = i * 100;
          setTimeout("applyPropertyToObject(\'" + branch + "\',\'" + i + "\',\'"
                      + property + "\')",time);
        }
      }

      function applyPropertyToObject( branch, i , property ) {
        document.getElementById(expandable[branch][i]).style.display= property;
      }

      function fold_unfold( idOgg ) {
        if (folded[idOgg]) {
          fold_unfold_expandable_animation(idOgg,"inline");
          folded[idOgg] = false;
        } else {
          fold_unfold_expandable_animation(idOgg,"none");
          folded[idOgg] = true;
        }
      }
    </script>

EOT;
    return $output;
  }

  /* Menu Functions */

  public function generateMenu($menuStructure) {
    $domDocument = new DOMDocument();
    $domDocument->load("{$this->template}/template.xml");
    $domMenu = $domDocument->getElementsByTagName("menu")->item(0);

    $menuData = $this->decodeDOMmenu($domMenu->childNodes);
    unset ($domDocument);
    unset ($domMenu);

    $output  = $menuData["initCode"];
    
    foreach ($menuStructure as $menuEntry) {
      switch ($menuEntry["button_type"]) {
        case "button_menu": {
          $output .= $this->generateButtMenu($menuEntry,
                                        $menuData["menuButton"]["code"]);
          break;
        }
        case "button_expandable": {
          $output .= $this->generateButtExpandable($menuEntry,
                                        $menuData["expandableButton"]["code"]);
          $newFoldingRoot["name"] = $menuEntry["name"];
          foreach ($menuEntry["subitems"] as $item) {
            $output .= $this->generateButtSubmenu($item,
                                        $menuData["submenuButton"]["code"]);
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

//    $output .= Banner::placeMenuBanner();
    $output .= $menuData["closeCode"];
    
    $this->builtMenu = $output; /* This will be in future the only admitted */
  }

  private function generateButtMenu($buttonData, $format) {
    $name = $buttonData["name"];
    $imageNormal = "{$this->template}/button_{$name}_normal.png";
    $imageHovered = "{$this->template}/button_{$name}_hover.png";
    $this->hoverHandler->
        addHoveredImage($imageNormal, $imageHovered, $name);

    $format = str_replace("[NAME]",$name, $format );
    $format = str_replace("[IMAGE-NORMAL]",$imageNormal, $format );

    return $format;
  }

  private function generateButtExpandable($buttonData, $format) {
    $name = $buttonData["name"];
    $imageNormal = "{$this->template}/button_{$name}_normal.png";
    $imageHovered = "{$this->template}/button_{$name}_hover.png";
    $this->hoverHandler->
        addHoveredImage($imageNormal, $imageHovered, $name);
    
    $format = str_replace("[NAME]",$name, $format );
    $format = str_replace("[IMAGE-NORMAL]",$imageNormal, $format );

    return $format;
  }

  private function generateButtSubmenu($buttonData, $format) {
    $root = $buttonData["root"];
    $leaf = $buttonData["leaf"];
    $name = "{$root}_{$leaf}";
    $imageNormal = "{$this->template}/button_{$name}_normal.png";
    $imageHovered = "{$this->template}/button_{$name}_hover.png";
    $this->hoverHandler->
        addHoveredImage($imageNormal, $imageHovered, $name);

    $format = str_replace("[NAME]",$name, $format );
    $format = str_replace("[LEAF]",$leaf, $format );
    $format = str_replace("[IMAGE-NORMAL]",$imageNormal, $format );

    return $format;
  }

  private function decodeDOMmenu($menuNodes) {
    $output = array();
    foreach ($menuNodes as $node) {
      switch ($node->nodeName) {
        case "initCode":
        case "closeCode":
          $output["{$node->nodeName}"] = $node->textContent;
          break;
        case "menuButton":
        case "submenuButton":
        case "expandableButton":
          foreach ($node->childNodes as $child) {
            switch ($child->nodeName) {
              case "code":
              case "type":
                $output["{$node->nodeName}"]["{$child->nodeName}"] =
                    $child->textContent;
            }
          }
          break;
      }
    }
    return $output;
  }

  public function getChosenTemplate() {
    return $this->template;
  }

  function setContentsManager($cntMgr) {
    $this->contentsManager = $cntMgr;
  }

  public function generateHead($title, $additionalCssFiles) {
    $output  = "  <head>\n";
    $output .= "    <title>{$title}</title>\n";
    $output .= "    <link rel=\"stylesheet\" type=\"text/css\" ".
               "href=\"{$this->template}/main.css\" />\n";
    $output .= "    <link rel=\"stylesheet\" type=\"text/css\" ".
               "href=\"{$this->template}/menu.css\" />\n";
    if (is_array($additionalCssFiles)) {
      foreach ($additionalCssFiles as $cssFile) {
        $output .= "    <link rel=\"stylesheet\" type=\"text/css\" ".
                   "href=\"{$this->template}/{$cssFile}\" />\n";
      }
    }

    $output .= $this->hoverHandler->initHoveredCache();
    $output .= $this->hoverHandler->printHoverFunctions();

    $output .= $this->generateFoldingJS();

    if ($this->contentsManager->hasLayers()) {
      $output .= $this->contentsManager->getLayerFunctions();
    }

    //Banner::initMenuBanner();
    //Banner::printSmallBanner();
    //Banner::initLowerBanner();
    $output .= "    <meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />\n";
    $output .= "  </head>\n";
    print $output;
  }

  public function generateBody() {
    $domDocument = new DOMDocument();
    $domDocument->load("{$this->template}/template.xml");
    $node = $domDocument->getElementsByTagName("structure")->item(0);
    $bodyContent = $node->textContent;

    $bodyContent = str_replace("[MENU]",$this->builtMenu, $bodyContent );
    $bodyContent = str_replace("[LAYERS]",
        $this->contentsManager->hasLayers() ?
            $this->contentsManager->getLayers() :
            "",
        $bodyContent );
    $bodyContent = str_replace("[TEMPLATE]","{$this->template}", $bodyContent );
    $bodyContent = str_replace("[CONTENTS]",
        $this->contentsManager->getContents(), $bodyContent );

    $bodyContent = str_replace("[BANNER-TOP]","", $bodyContent );
    $bodyContent = str_replace("[BANNER-BOTTOM]","", $bodyContent );

    print "$bodyContent";
  }
}
?>
