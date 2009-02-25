<?php

define("GALLERY_CLASS","1");

include_once 'HoverEffect.php';
include_once 'DiskIO.php';
include_once 'LayeredContents.php';

/** Class that generates the content with the images thumbnails, and the layers where to display bigger images.
 */
class Gallery extends LayeredContents {
  
  protected $listOfPhotos;
  protected $photosPath;
  
  function __construct( StdUsefulData $data , $subGallery) {
    $this->photosPath = "gallery/{$subGallery}";
    $this->template = $data->templateDir;
    $this->listOfPhotos = $data->ioResource->getPhotosOfGallery($subGallery);
    $this->theHover = $data->hoverHandler;
    
    $this->initFocusEffect();
  }
  
  public function getCss() {
    return "gallery.css";
  }
  
  public function getLayerFunctions() {
    $output  = <<<EOT
    <script type="text/javascript">
      var photoIndex = 0;
      var photosList = new Array (

EOT;
    foreach ($this->listOfPhotos as $thisPhoto) {
      $output .= "                            '$this->photosPath/$thisPhoto',\n";
    }  // ricorda che mentre php parte con indice 1, JS parte come il C da indice 0
      $output .= "                            '$this->templateDir/blank.png'\n";
      $output .= <<<EOT
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

EOT;
    return $output;
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
    $previousButtonNormal = "{$this->template}/button_previous_normal.png";
    $nextButtonNormal = "{$this->template}/button_next_normal.png";
    return '    <img id="darkLayer" class="dark_layer" src="' . $this->template . '/dark_layer.png" />' . "\n" .
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
      $output .= '          <img class="gallery_photo_thumb" id="' . $count . '" alt="' . $count . '" src="' .
            "$this->photosPath/thumbs/$thisPhoto" . '" onclick="initPhotoFocus(\'' . $count . '\')" />' . "\n";
      $count++;
    }
    return $output;
  }
} 
?>
