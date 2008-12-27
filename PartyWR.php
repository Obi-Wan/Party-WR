<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define("PARTYWR_CLASS","1");

include_once 'StdUsefulData.php';
include_once 'Menu.php';
include_once 'HoverEffect.php';
include_once 'DiskIO.php';


/**
 * Description of PartyWR
 *
 * @author ben
 */
class PartyWR {

  private $ioResource;
  private $hoverHandler;
  private $data;
  private $template;
  private $contentsManager;
  private $theMenu;
  private $builtMenu;
  private $title;
  private $siteStructure;
  private $features;

  public function __construct() {

    try {
      $this->initComponents();
    } catch (DirectoryStructureException $ex) {
      print "Errors happened while initializing contents Dirs:\n".
            "$ex->getMessage()";
    }
  }
  
  private function initComponents() {
    $this->ioResource = new DiskIO();
    
    $this->hoverHandler = new HoverEffect();

    $this->loadConfig();

    // TODO crea un oggetto che si occupi della gestione dei template
    
    /* We are now interested in finding which template was chosen */
    if ( /* If no template chosen, or it's not between the ones installed.. */
        $chosen_template == "" ||
        ( ! in_array( $chosen_template , $this->ioResource->getTemplates() ) )
       )
    { /* We do fallback to default */
      $chosen_template = "default";
    }
    $this->template = "templates/{$chosen_template}";

    $this->data = new StdUsefulData($this->ioResource,$this->template,$this->hoverHandler);
    
    $this->contentsManager = $this->getContentsManager();

    /* Menu instantiated and created (not shown) */
    $this->theMenu = new Menu($this->data);
    $this->theMenu->setSiteStructure($this->siteStructure);
    $this->builtMenu = $this->theMenu->getMenu(); // FIXME change this behavior

    include_once 'Banner.php';
  }

  private function loadConfig() {
    $rawConfig = $this->ioResource->getRawContents("config");
    $this->title = "$rawConfig->title";
    foreach ($rawConfig->sections->item as $section) {
      $this->siteStructure["{$section->name}"] = "{$section->class}";
    }
    foreach ($rawConfig->platform->has as $feature) {
      $this->features[] = "{$feature}";
    }
    unset($rawConfig);
  }

  private function isOfGalleries(&$anno,$gallery_years) { /*temporary fix to overcome Internet Exploder bullshitness*/
    foreach ($gallery_years as $thisYear) {
      if ($_REQUEST["{$thisYear}_x"]) {
        $anno = $thisYear;
        return true;
      }
    }
    return false;
  }

  private function getContentsManager() {
    foreach ($this->siteStructure as $name_section => $type_section) {
      if ($_REQUEST["{$name_section}_x"] == true) {
        return $this->getSelectedClass($name_section, $type_section);
      }
    }
    $gallery_years = $this->ioResource->getGalleries();
    if ($this->isOfGalleries($anno,$gallery_years)) {
      include_once 'Gallery.php';
      return new Gallery($this->data,$anno);
    }
    /*if no supplied, defaults to the first section*/
    reset($this->siteStructure);
    return $this->getSelectedClass(key($this->siteStructure), current($this->siteStructure));
  }
  
  private function getSelectedClass($name_section, $type_section) {
    include_once "{$type_section}.php";
    switch ($type_section) {
      case "Home": {
        return new Home($this->data);
      }
      case "News": {
        return new News($this->data);
      }
      case "GenericNonLayeredContents": {
        return new GenericNonLayeredContents($this->data,$name_section);
      }
    }
  }

  public function printHead() {
    print "  <head>\n";
    print "    <title>{$this->title}</title>\n";
    print "    <link rel=\"stylesheet\" type=\"text/css\" href=\"{$this->template}/main.css\" />\n";
    print "    <link rel=\"stylesheet\" type=\"text/css\" href=\"{$this->template}/menu.css\" />\n";
    $this->contentsManager->getCss();

    $this->hoverHandler->initHoveredCache();
    $this->hoverHandler->printHoverFunctions();

    $this->theMenu->printFoldingJS();
    if ($this->contentsManager->hasLayers()) {
      $this->contentsManager->getLayerFunctions();
    }

    //Banner::initMenuBanner();
    //Banner::printSmallBanner();
    //Banner::initLowerBanner();
    print "    <meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\" />\n";
    print "  </head>\n";
  }
  
  public function printBody() {
    print "  <body>\n";
    if ($this->contentsManager->hasLayers()) $this->contentsManager->getLayers();
    print "    <div class=\"mainBox\">\n";
    print "      <img src=\"images/PartyHardApproved.png\" class=\"approved\" alt=\"\" />\n"; /*this is just a mark*/
    //  print Banner::putSmallBanner();
    print "      <img src=\"{$this->template}/angoloDefWide.png\" class=\"logo\" alt=\"Logo\" />\n";
    print "      <div class=\"banner\">\n";
    print "        <img src=\"{$this->template}/titolo_pieno.png\" class=\"banner\" alt=\"banner\" />\n";
    print "      </div>\n";
    print "      <div class=\"blocco_centrale\">\n";
    print "$this->builtMenu";
    print "        <div class=\"corpo\">\n";
    $this->contentsManager->getContents();
    print "        </div>\n";
    // print Banner::placeLowerBanner();
    print "      </div>\n";
    print "    </div>\n";
    print "  </body>\n";
  }
}
?>
