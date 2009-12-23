<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define("PARTYWR_CLASS","1");

include_once 'Structs/StdUsefulData.php';
include_once 'Menu.php';
include_once 'HoverEffect.php';
include_once 'DiskIO.php';
include_once 'Layout.php';


/**
 * Description of PartyWR
 *
 * @author ben
 */
class PartyWR {

  /** It hanles all the output and prints it formatted */
  private $layoutMananger;

  /** It references all the important objects/data to pass to the content handlers */
  private $partyWResources;

  /** The object that manages the content */
  private $contentsManager;

  /** It generates the menu */
  private $menuManager;

  /** The title of the site */
  private $title;

  /** It holds the structure of the site */
  private $siteStructure;

//  Features not supported yet
//  private $features;

  public function __construct() {

    try {
      $this->initComponents();
    } catch (DirectoryStructureException $ex) {
      print "Errors happened while initializing contents Dirs:\n".
            "$ex->getMessage()";
    }
  }
  
  private function initComponents() {
    $ioResource = new DiskIO();
    $hoverHandler = new HoverEffect();

    $this->loadConfig( $ioResource );

    // TODO delega la gestione della formattazione tutta al layoutManager
    $this->layoutMananger = new Layout("templates", $ioResource, $hoverHandler);

    /* Ok now we save the refereces to the useful classes */
    $this->partyWResources = new StdUsefulData(
            $ioResource,
            $this->layoutMananger->getChosenTemplate(),
            $hoverHandler);
    
    $this->contentsManager = $this->getContentsManager( $ioResource );
    $this->layoutMananger->setContentsManager($this->contentsManager);

    /* Menu instantiated and created (not shown) */
    $this->menuManager = new Menu($this->partyWResources);
    $this->menuManager->setSiteStructure($this->siteStructure);
    $this->layoutMananger->generateMenu($this->menuManager->getMenu());

    // include_once 'Banner.php';
  }

  private function loadConfig( DiskIO $ioResource ) {
    $rawConfig = $ioResource->getRawContents("config");
    $this->title = "$rawConfig->title";
    foreach ($rawConfig->sections->item as $section) {
      $this->siteStructure["{$section->name}"] = "{$section->class}";
    }
//    not supported yet, so comment it out.
//    foreach ($rawConfig->platform->has as $feature) {
//      $this->features[] = "{$feature}";
//    }
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

  private function getContentsManager( DiskIO $ioResource ) {
    foreach ($this->siteStructure as $name_section => $type_section) {
      if ($_REQUEST["{$name_section}_x"] == true) {
        return $this->getSelectedClass($name_section, $type_section);
      }
    }
    $gallery_years = $ioResource->getGalleries();
    if ($this->isOfGalleries($anno,$gallery_years)) {
      include_once 'Gallery.php';
      return new Gallery($this->partyWResources,$anno);
    }
    /*if no supplied, defaults to the first section*/
    reset($this->siteStructure);
    return $this->getSelectedClass(key($this->siteStructure), current($this->siteStructure));
  }
  
  private function getSelectedClass($name_section, $type_section) {
    include_once "{$type_section}.php";
    switch ($type_section) {
      case "Home": {
        return new Home($this->partyWResources);
      }
      case "News": {
        return new News($this->partyWResources);
      }
      case "GenericNonLayeredContents": {
        return new GenericNonLayeredContents($this->partyWResources,$name_section);
      }
    }
  }

  public function printHead() {
    $this->layoutMananger->pre_generateHead();
    $this->layoutMananger->generateHead(
            $this->title, $this->contentsManager->getCss() );
  }
  
  public function printBody() {
    $this->layoutMananger->generateBody();
  }
}
?>
