<?php

define("GALLERY_CLASS","1");

if (!defined("HOVER_EFFECT_FUNCTIONS") ){
  include 'hover_effect.php';
}
if (!defined("DISK_IO_CLASS")) {
  include 'disk_io.php';
}
if (!defined("LAYERED_CONTENTS_CLASS")) {
  include 'layered_contents.php';
}

/** Class that generates the content with the images thumbnails, and the layers where to display bigger images.
 */
class Gallery extends LayeredContents {
  
  protected $listOfPhotos;
  protected $photosPath;
  
  function __construct( StdUsefulData $data , $anno) {
    $this->photosPath = "gallery/$anno";
    $this->template = $data->templateDir;
    $this->listOfPhotos = $data->ioResource->getPhotosOfGallery($anno);
    $this->theHover = $data->hoverHandler;
    
    $this->initFocusEffect();
  }
  
  public function getCss() {
    print '    <link rel="stylesheet" type="text/css" href="' . $this->template . '/gallery.css" />' . "\n";
  }
  
  public function getLayerFunctions() {
?>
  <script type="text/javascript">
    var photoIndex = 0;
    var photosList = new Array (
<?php
    foreach ($this->listOfPhotos as $thisPhoto) {
      print "                            '$this->photosPath/$thisPhoto',\n";
    }  // ricorda che mentre php parte con indice 1, JS parte come il C da indice 0
      print "                            '$this->templateDir/blank.png'\n";
?>
                                );
    function raisePhotoFocus() {
      var darkLayer = document.getElementById('darkLayer');
      var displayerFrame = document.getElementById('displayerFrame');
      var displayerFrameBG = document.getElementById('displayerFrameBackground');
      darkLayer.style.display = "inline";
      displayerFrame.style.display = "inline";
      displayerFrameBG.style.display = "inline";
    }
    function removePhotoFocus() {
      var darkLayer = document.getElementById('darkLayer');
      var displayerFrame = document.getElementById('displayerFrame');
      var displayerFrameBG = document.getElementById('displayerFrameBackground');
      darkLayer.style.display = "none";
      displayerFrame.style.display = "none";
      displayerFrameBG.style.display = "none";
    }
    function initPhotoFocus( photo_id ) {
      photoIndex = photo_id;
      putPhoto();
      raisePhotoFocus();
    }
    function putPhoto() {
      var photo_element = document.getElementById('photoObject');
      photo_element.src = photosList[photoIndex];
    }
    function nextPhoto() {
      if (photoIndex >= (photosList.length - 1)) {
        photoIndex = 0;
      } else {
        photoIndex++;
      }
      putPhoto();
    }
    function previousPhoto() {
      if (photoIndex <= 0) {
        photoIndex = photosList.length - 1;
      } else {
        photoIndex--;
      }
      putPhoto();
    }
  </script>

<?php
  }
  
  protected function initFocusEffect() {
    $closeButtonNormal = "{$this->template}/button_close_normal.png";
    $closeButtonHover = "{$this->template}/button_close_hover.png";
    $previousButtonNormal = "{$this->template}/button_previous_normal.png";
    $previousButtonHover = "{$this->template}/button_previous_hover.png";
    $nextButtonNormal = "{$this->template}/button_next_normal.png";
    $nextButtonHover = "{$this->template}/button_next_hover.png";
    $this->theHover->addHoveredImage($closeButtonNormal,$closeButtonHover,"close");
    $this->theHover->addHoveredImage($previousButtonNormal,$previousButtonHover,"previous");
    $this->theHover->addHoveredImage($nextButtonNormal,$nextButtonHover,"next");
  }
  
  public function getLayers( ) {
    $closeButtonNormal = "{$this->template}/button_close_normal.png";
    $closeButtonHover = "{$this->template}/button_close_hover.png";
    $previousButtonNormal = "{$this->template}/button_previous_normal.png";
    $previousButtonHover = "{$this->template}/button_previous_hover.png";
    $nextButtonNormal = "{$this->template}/button_next_normal.png";
    $nextButtonHover = "{$this->template}/button_next_hover.png";
    print '    <img id="darkLayer" class="dark_layer" src="' . $this->template . '/dark_layer.png" />' . "\n" .
          '    <img id="displayerFrameBackground" class="displayer_frame_background" src="' . $this->template .
          '/sfondo_photo_frame.png" />' . "\n" .
          '    <div id="displayerFrame" class="displayer_frame">' . "\n" .
          '      <img id="close" class="close_button" src="' . $closeButtonNormal . '" ' .
          'onclick="removePhotoFocus()" onmouseover="mouseOver(\'close\')" onmouseout="mouseOut(\'close\')" />'."\n".
          '        <img id="photoObject" class="photo_object" src="' . $this->template . '/blank.png" />' . "\n" .
          '      <img id="previous" class="previous_button" src="' . $previousButtonNormal .
          '" onclick="previousPhoto()" onmouseover="mouseOver(\'previous\')" ' .
          'onmouseout="mouseOut(\'previous\')" />' . "\n" .
          '      <img id="next" class="next_button" src="' . $nextButtonNormal . '" onclick="nextPhoto()" '.'onmouseover="mouseOver(\'next\')" onmouseout="mouseOut(\'next\')" />' . "\n" .
          "    </div>\n";
  }

  public function getContents() {
    $count = 0;
    foreach ($this->listOfPhotos as $thisPhoto) {
      print '          <img class="gallery_photo_thumb" id="' . $count . '" alt="' . $count . '" src="' .
            "$this->photosPath/thumbs/$thisPhoto" . '" onclick="initPhotoFocus(\'' . $count . '\')" />' . "\n";
      $count++;
    }
  }
} 
?>
