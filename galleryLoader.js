/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

var photoIndex = 0;
var photosList;

var requestHandler;


function doRequest( subgallery ) {
  var url = "DataRetrievers/Retriever.php?id=gallery&subid=" + subgallery;
  requestHandler = initRequest();
  requestHandler.open("GET", url, true);
  requestHandler.onreadystatechange = callback;
  requestHandler.send(null);
}

function initRequest() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  } else if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
}

function callback() {

  if (requestHandler.readyState == 4) {
    if (requestHandler.status == 200) {
      parseMessages(requestHandler.responseXML);
    }
  }
}

function parseMessages(responseXML) {
  // no matches returned
  if (responseXML == null) {
    return false;
  } else {
    photosList = new Array(responseXML.getElementsByTagName("photos")[0].nodeValue);
    var photos = responseXML.getElementsByTagName("photo");
    for (count = 0; count < photos.length; count++) {
      photosList[count] = new Image();
      photosList[count].src = photos[count].childNodes[0].nodeValue;
    }
    return true;
  }
}


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
  var time;
  for (i = 0; i< 12; i++) {
    time = i * 40;
    setTimeout("document.getElementById(\'photoObject\').style.opacity = " +
                ((11-i)/12) + "; ",time);
  }
  time = 11 * 40; //anticipato per eventuali lag
  setTimeout("document.getElementById(\'photoObject\').src = photosList[photoIndex].src;",
              time);
  for (i = 14; i< 26; i++) {
    time = i * 40;
    setTimeout("document.getElementById(\'photoObject\').style.opacity = " +
                ((i - 13)/12) + ";",time);
  }
}

function putPhotoAnim() {
  document.getElementById('photoObject').style.opacity = 0;
  document.getElementById('photoObject').src = photosList[photoIndex].src;
  for (i = 0; i< 13; i++) {
    var time = i * 40;
    setTimeout("document.getElementById(\'photoObject\').style.opacity = " +
                (i/12) + ";",time);
  }
}

function putPhoto() {
  var photo_element = document.getElementById('photoObject');
  photo_element.src = photosList[photoIndex].src;
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
  photoIndex = photosList.length - 1;
  changePhotoAnim();
}

