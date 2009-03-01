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

  protected $tableOfFunctions = array (
                    "close" => "removePhotoFocus()",
                    "next" => "nextPhoto()",
                    "previous" => "previousPhoto()",
                    "first" => "firstPhoto()",
                    "last" => "lastPhoto()",
                    "buttons_bar" => "hide_expose_bar()"
  );
  
  function __construct( StdUsefulData $data , $subGallery) {
    $this->photosPath = "gallery/{$subGallery}";
    $this->template = $data->templateDir;
    $this->listOfPhotos = $data->ioResource->getPhotosOfGallery($subGallery);
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
    }
      $output .= "                            '$this->templateDir/blank.png'\n";
      $output .= <<<EOT
                                  );
      function raisePhotoFocus() {
        var darkLayer = document.getElementById('darkLayer');
        var displayerFrame = document.getElementById('displayerFrame');
        var displayerFrameBG = document.getElementById('displayerFrameBackground');
        var buttonsContainer = document.getElementById('buttons_container');
        var buttonsBar = document.getElementById('buttons_bar');
        darkLayer.style.display = "inline";
        displayerFrame.style.display = "inline";
        displayerFrameBG.style.display = "inline";
        buttonsContainer.style.display = "inline";
        buttonsBar.style.display = "inline";
      }
      function removePhotoFocus() {
        var darkLayer = document.getElementById('darkLayer');
        var displayerFrame = document.getElementById('displayerFrame');
        var displayerFrameBG = document.getElementById('displayerFrameBackground');
        var buttonsContainer = document.getElementById('buttons_container');
        var buttonsBar = document.getElementById('buttons_bar');
        darkLayer.style.display = "none";
        displayerFrame.style.display = "none";
        displayerFrameBG.style.display = "none";
        buttonsContainer.style.display = "none";
        buttonsBar.style.display = "none";
      }
      function initPhotoFocus( photo_id ) {
        photoIndex = photo_id;
        putPhotoAnim();
        raisePhotoFocus();
      }
      function changePhotoAnim() {
        var i = 0;
        for (i = 0; i< 12; i++) {
          var time = i * 40;
          setTimeout("document.getElementById(\'photoObject\').style.opacity = " +
                      ((11-i)/12) + "; ",time);
        }
        var time = 11 * 40; //anticipato per eventuali lag
        setTimeout("document.getElementById(\'photoObject\').src = photosList[photoIndex];",
                    time);
        for (i = 14; i< 26; i++) {
          var time = i * 40;
          setTimeout("document.getElementById(\'photoObject\').style.opacity = " +
                      ((i - 13)/12) + ";",time);
        }
      }
      function putPhotoAnim() {
        document.getElementById('photoObject').style.opacity = 0;
        document.getElementById('photoObject').src = photosList[photoIndex];
        for (i = 0; i< 13; i++) {
          var time = i * 40;
          setTimeout("document.getElementById(\'photoObject\').style.opacity = " +
                      (i/12) + ";",time);
        }
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
        changePhotoAnim();
      }
      function previousPhoto() {
        if (photoIndex <= 0) {
          photoIndex = photosList.length - 1;
        } else {
          photoIndex--;
        }
        changePhotoAnim();
      }
      function firstPhoto() {
        photoIndex = 0;
        changePhotoAnim();
      }
      function lastPhoto() {
        photoIndex = photosList.length - 2;
        changePhotoAnim();
      }
    </script>

EOT;
    return $output;
  }
  
  public function getLayers( ) {
    $output["type"] = "gallery";

    $output["functions"] = $this->tableOfFunctions;

    return $output;
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
