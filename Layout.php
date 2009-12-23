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

  // pre-built body contents
  private $builtMenu;
  private $builtLayers;

  /** Template Directory */
  private $template;

  /** Reference to the HoverEffect handler */
  private $hoverHandler;

  /** Reference to the Contents manager */
  private $contentsManager;

  /** DOMDocument Object which holds template data */
  private $domDocument;

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

      $this->domDocument = new DOMDocument();
      $this->domDocument->load("{$this->template}/template.xml");
    }
  }

  /* JavaScript Functions */

  public function  generateFoldingJS() {
    $output = <<<EOT
    <script type="text/javascript">
      var expandable = new Array();

EOT;
    foreach ($this->foldingMenus as $singleItem) {
      $output .= "      var ".$singleItem["name"]." = new Array();\n";
      if ($singleItem["leaves"]) {
        foreach ($singleItem["leaves"] as $subItem) {
          $output .= "      {$singleItem["name"]}.push('" . $subItem . "');\n";
        }
      }

      $output .= "      expandable['{$singleItem["name"]}'] = {$singleItem["name"]};\n";
    }
    $output .= <<<EOT
      function applyPropertyToObject( branch, i ) {
        $("#" + expandable[branch][i]).toggle('drop', '', 500);
      }
      function fold_unfold( idOgg ) {
        for (i = 0; i < expandable[idOgg].length; i++) {
          var time = i * 250;
          setTimeout("applyPropertyToObject(\'" + idOgg + "\',\'" + i + "\')",time);
        }
      }
    </script>

EOT;
    return $output;
  }

  /* Menu Functions */

  public function generateMenu($menuStructure) {
    $domMenu = $this->domDocument->getElementsByTagName("menu")->item(0);

    $menuData = $this->decodeDOMmenu($domMenu->childNodes);
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
  
  public function pre_generateHead() {
    if ($this->contentsManager->hasLayers()) {
      $this->builtLayers = $this->generateLayers();
    } else {
      $this->builtLayers = "";
    }
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

    $output .= "    <script type=\"text/javascript\" src=\"js/jquery-1.3.2.js\"></script>\n";
    $output .= "    <script type=\"text/javascript\" src=\"js/jquery-ui-1.7.2.custom.js\"></script>\n";
    $output .= $this->hoverHandler->initHoveredCache();
    $output .= $this->hoverHandler->printHoverFunctions();
    
    if ($this->contentsManager->hasAdditionalJS()) {
      $output .= $this->contentsManager->getAdditionalJS();
    }

    $output .= $this->generateFoldingJS();

    if ($this->builtLayers != "") {
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
    $node = $this->domDocument->getElementsByTagName("structure")->item(0);
    $bodyContent = $node->textContent;

    $bodyContent = str_replace("[TEMPLATE]", "{$this->template}", $bodyContent);
    $bodyContent = str_replace("[MENU]", $this->builtMenu, $bodyContent);
    $bodyContent = str_replace("[LAYERS]", $this->builtLayers, $bodyContent);
    $bodyContent = str_replace("[CONTENTS]",
        $this->contentsManager->getContents(), $bodyContent );

    $bodyContent = str_replace("[BANNER-TOP]", "", $bodyContent );
    $bodyContent = str_replace("[BANNER-BOTTOM]", "", $bodyContent );

    $bodyContent = str_replace("[REQUESTING-CODE]",
        $this->contentsManager->needsOnload() ?
          $this->contentsManager->getOnloadCode() :
          "",
        $bodyContent );

    print "$bodyContent";
  }

  public function generateLayers() {
    $layersProvidedData = $this->contentsManager->getLayers();
    $layersRequestedData = $this->domDocument->getElementsByTagName(
                                      "{$layersProvidedData["type"]}")->item(0);
    $layersCode = $layersRequestedData->getElementsByTagName(
                                                  "code")->item(0)->textContent;

    // not used yet
    $usedButtons = $layersRequestedData->getElementsByTagName(
                                        "{$layersProvidedData["type"]}-button");
    $usedBars = $layersRequestedData->getElementsByTagName(
                                           "{$layersProvidedData["type"]}-bar");
    
    $layersCode = str_replace("[TEMPLATE]","{$this->template}", $layersCode );

    foreach ($usedButtons as $button) {
      $name = $button->nodeValue;

      // setup image for the component
      $tempUrlNormal = "{$this->template}/button_{$name}_normal.png";
      $tempMark = "[" . strtoupper($name) . "-NORMAL]";
      $layersCode = str_replace($tempMark,$tempUrlNormal, $layersCode );

      // setup JS functions for the component
      $tempMark = "[" . strtoupper($name) . "-FUNCTION]";
      $layersCode = str_replace($tempMark,
                                $layersProvidedData["functions"][$name],
                                $layersCode );

      // setup HoverEffect for the component
      $tempUrlHover = "{$this->template}/button_{$name}_hover.png";
      $this->hoverHandler->addHoveredImage($tempUrlNormal,$tempUrlHover, $name);
    }

    foreach ($usedBars as $bar) {
      $name = "{$bar->nodeValue}_bar";

      // setup image for the component
      $tempUrlNormal = "{$this->template}/{$name}_normal.png";
      $tempMark = "[" . mb_strtoupper($name) . "-NORMAL]";
      $layersCode = str_replace($tempMark,$tempUrlNormal, $layersCode );

      // setup JS functions for the component
      $tempMark = "[" . mb_strtoupper($name) . "-FUNCTION]";
      $layersCode = str_replace($tempMark,
                                $layersProvidedData["functions"][$name],
                                $layersCode );

      // setup HoverEffect for the component
      $tempUrlHover = "{$this->template}/{$name}_hover.png";
      $this->hoverHandler->addHoveredImage($tempUrlNormal,$tempUrlHover, $name);
    }
    
    return "$layersCode";
  }
}
?>
