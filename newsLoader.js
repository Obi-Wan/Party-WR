
function singleItem( title_n, time_n, cathegory_n, description_n) {
  this.title = title_n;
  this.time = time_n;
  this.cathegory = cathegory_n;
  this.description = description_n;
}

var itemIndex = 0;
var itemsList = new Array();


var requestHandler;


function doRequest( ) {
  var url = "DataRetrievers/Retriever.php?id=news";
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
    var list_of_news = responseXML.getElementsByTagName("item");
    for (count = 0; count < list_of_news.length; count++) {
      var thisItem = list_of_news[count];
      itemsList[count] = new singleItem(
          thisItem.getElementsByTagName("title")[0].childNodes[0].nodeValue,
          thisItem.getElementsByTagName("time")[0].childNodes[0].nodeValue,
          thisItem.getElementsByTagName("cathegory")[0].childNodes[0].nodeValue,
          thisItem.getElementsByTagName("description")[0].childNodes[0].nodeValue
        );
      //photosList[count].src = photos[count].childNodes[0].nodeValue;
    }
    return true;
  }
}


function raiseEffectFocus() {
  var darkLayer = document.getElementById('darkLayer');
  var displayerFrame = document.getElementById('displayerFrame');
  var displayerFrameBG = document.getElementById('displayerFrameBackground');
  darkLayer.style.display = "inline";
  displayerFrame.style.display = "inline";
  displayerFrameBG.style.display = "inline";
}

function removeEffectFocus() {
  var darkLayer = document.getElementById('darkLayer');
  var displayerFrame = document.getElementById('displayerFrame');
  var displayerFrameBG = document.getElementById('displayerFrameBackground');
  darkLayer.style.display = "none";
  displayerFrame.style.display = "none";
  displayerFrameBG.style.display = "none";
}

function initEffectFocus( item_id ) {
  itemIndex = item_id;
  putItem();
  raiseEffectFocus();
}

function putItem() {
  var title_in_frame = document.getElementById('title_in_frame');
  var time_in_frame = document.getElementById('time_in_frame');
  var cathegory_in_frame = document.getElementById('cathegory_in_frame');
  var description_in_frame = document.getElementById('description_in_frame');
  title_in_frame.innerHTML = itemsList[itemIndex].title;
  time_in_frame.innerHTML = itemsList[itemIndex].time;
  cathegory_in_frame.innerHTML = itemsList[itemIndex].cathegory;
  description_in_frame.innerHTML = itemsList[itemIndex].description;
}

function nextItem() {
  if (itemIndex >= (itemsList.length - 1)) {
    itemIndex = 0;
  } else {
    itemIndex++;
  }
  putItem();
}

function previousItem() {
  if (itemIndex <= 0) {
    itemIndex = itemsList.length - 1;
  } else {
    itemIndex--;
  }
  putItem();
}
