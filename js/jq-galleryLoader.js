
var photoIndex = 0;
var photosList = new Array();

function initGallery(gallery){
  $.ajax({
    method: "get",
    url: "DataRetrievers/Retriever.php",
    data: "id=gallery&subid=" + gallery,
    beforeSend: function(){
//      $("#loading_layer").show("fast");
    }, //show loading just when link is clicked
    complete: function(){
//      $("#loading_layer").hide("fast");
    }, //stop showing loading when the process is complete
    success: function(html){ //so, if data is retrieved, store it in html
      parseMessages(html);
    }
  }); //close $.ajax(
}

function parseMessages(responseXML) {
  // no matches returned
  if (responseXML == null) {
    // do something
  } else {
    var photos = responseXML.getElementsByTagName("photo");
    for (count = 0; count < photos.length; count++) {
      photosList[count] = new Image();
      photosList[count].src = photos[count].childNodes[0].nodeValue;
    }
  }
}

function raisePhotoFocus() {
  $('#darkLayer').show("normal");
  $('#displayerFrame').show("normal");
}

function removePhotoFocus() {
  $('#darkLayer').hide("normal");
  $('#displayerFrame').hide("normal");
}

function initPhotoFocus( photo_id ) {
  photoIndex = photo_id;
  putPhotoAnim();
  raisePhotoFocus();
}

function changePhotoAnim() {
  $("#photoObject").addClass("hidden", 500);
  setTimeout("document.getElementById('photoObject').src = photosList[photoIndex].src;",
              500);
  setTimeout("$('#photoObject').removeClass('hidden', 500);",600);
}

function putPhotoAnim() {
  $("#photoObject").hide();
  document.getElementById('photoObject').src = photosList[photoIndex].src;
  $('#photoObject').show();
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