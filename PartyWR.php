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

  private $ioResource;
  private $hoverHandler;
  private $layoutMananger;
  private $data;
  private $template;
  private $contentsManager;
  private $theMenu;
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

    // TODO delega la gestione della formattazione tutta al layoutManager
    $this->layoutMananger = new Layout("templates",$this->ioResource,$this->hoverHandler);
    $this->template = $this->layoutMananger->getChosenTemplate();

    $this->data = new StdUsefulData($this->ioResource,$this->template,$this->hoverHandler);
    
    $this->contentsManager = $this->getContentsManager();
    $this->layoutMananger->setContentsManager($this->contentsManager);

    /* Menu instantiated and created (not shown) */
    $this->theMenu = new Menu($this->data);
    $this->theMenu->setSiteStructure($this->siteStructure);
    $this->layoutMananger->generateMenu($this->theMenu->getMenu());

    // include_once 'Banner.php';
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
    $additionalCssFiles = array();
    $cssFile = $this->contentsManager->getCss();
    if ($cssFile != "") {
      $additionalCssFiles[] = $cssFile;
    }
    $this->layoutMananger->pre_generateHead();
    $this->layoutMananger->generateHead($this->title, $additionalCssFiles);
  }
  
  public function printBody() {
    $this->layoutMananger->generateBody();
  }
}
?>
