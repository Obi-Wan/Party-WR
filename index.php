<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php

if (!defined("STD_USEFUL_DATA_STRUCT")) {
  include 'std_useful_data.php';
}
if (!defined("MENU_CLASS") ){
  include 'menu.php';
}

/* We do instantiate the first needed objects. */
$ioResource = new DiskIO();
$hoverHandler = new HoverEffect();

/* We are now interested in finding which template was chosen */
if ( /* If no template chosen, or it's not between the ones installed.. */
    $chosen_template == "" || 
    ( ! in_array( $chosen_template , $ioResource->getTemplates() ) )
   ) 
{ /* We do fallback to default */
  $chosen_template = "default";
}
$template = "templates/$chosen_template";

$data = new StdUsefulData($ioResource,$template,$hoverHandler);

/* we now find all the years in the gallery. */
$gallery_years = $ioResource->getGalleries();

function isOfGalleries(&$anno,$gallery_years) { /*temporary fix to overcome Internet Exploder bullshitness*/
  foreach ($gallery_years as $thisYear) {
    if ($_REQUEST["{$thisYear}_x"]) {
      $anno = $thisYear;
      return true;
    }
  }
  return false;
}

/* Now let's verify what page was selected. */
$contentsManager;

switch (true) {
  //Verify the home page was requested.
  case $_REQUEST['home_x']: { /* fallback for no selected page is on the last entry! */
    if (!defined("HOME_CLASS")) {
      include 'home.php';
    }
    $contentsManager = new Home($data);
    break;
  }
  case $_REQUEST['news_x']: {
    if (!defined("NEWS_CLASS")) {
      include 'news.php';
    }
    $contentsManager = new News($data);
    break;
  }
  // Verify was requested Contatti.
  case $_REQUEST['contatti_x']: {
    if (!defined("GENERIC_NON_LAYERED_CONTENTS_CLASS")) {
      include 'generic_non_layered_contents.php';
    }
    $contentsManager = new GenericNonLayeredContents($data,"contatti");
    break;
  }
  // Verify was requested Staff.
  case $_REQUEST['staff_x']: {
    if (!defined("GENERIC_NON_LAYERED_CONTENTS_CLASS")) {
      include 'generic_non_layered_contents.php';
    }
    $contentsManager = new GenericNonLayeredContents($data,"staff");
    break;
  }
  // Verify was requested Contatti.
  case $_REQUEST['tech_x']: {
    if (!defined("GENERIC_NON_LAYERED_CONTENTS_CLASS")) {
      include 'generic_non_layered_contents.php';
    }
    $contentsManager = new GenericNonLayeredContents($data,"tech");
    break;
  }
  default : {
    //verify we are requesting a gallery.
    $anno;
    if (isOfGalleries($anno,$gallery_years)) {
      if (!defined("GALLERY_CLASS") ){
        include 'gallery.php';
      }
      $contentsManager = new Gallery($data,$anno);
    } else {
      if (!defined("HOME_CLASS")) {
        include 'home.php';
      }
      $contentsManager = new Home($data);
    }
  }
}

/* Menu instantiated and created (not shown) */
$theMenu = new Menu($data);
$builtMenu = $theMenu->getMenu();

if (!defined("BANNER_CLASS") ){
  include 'banner.php';
}
?>

  <head>
    <title>Nobody^s_Party</title>
<?php
  print '    <link rel="stylesheet" type="text/css" href="' . $template . '/main.css" />' . "\n";
  print '    <link rel="stylesheet" type="text/css" href="' . $template . '/menu.css" />' . "\n";
  $contentsManager->getCss();
  
  $hoverHandler->initHoveredCache();
  $hoverHandler->printNewHoverFunctions();
  
  $theMenu->printFoldingJS();
  if ($contentsManager->hasLayers()) $contentsManager->getLayerFunctions();
  
  //Banner::initMenuBanner();
  //Banner::printSmallBanner();
  //Banner::initLowerBanner();
?>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  </head>

  <body>
<?php if ($contentsManager->hasLayers()) $contentsManager->getLayers(); ?>
    <div class="mainBox">
      <img src="images/PartyHardApproved.png" class="approved" alt="" /> <?php /*this is just a mark*/ ?>
<?php
//  print Banner::putSmallBanner();
  print '      <img src="' . $template . '/angoloDefWide.png" class="logo" alt="Logo" />';
?>
      <div class="banner">
<?php
  print '        <img src="' . $template . '/titolo_pieno.png" class="banner" alt="banner" />';
?>
      </div>
      <div class="blocco_centrale">
<?php print "$builtMenu"; ?>
        <div class="corpo">
<?php $contentsManager->getContents(); ?>
        </div>
<?php // print Banner::placeLowerBanner(); ?>	
      </div>
    </div>
  </body>
</html>
