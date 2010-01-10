
function mouseOver( idOgg ) {
  if (listObjImages[idOgg] == "hover") {
    var elem = document.getElementById( idOgg );
    elem.src = hoveredImages[idOgg].src;
  } else if (listObjImages[idOgg] == "shadow") {
    $("#" + idOgg).show("normal");
  }
}
function mouseOut( idOgg ) {
  if (listObjImages[idOgg] == "hover") {
    var elem = document.getElementById( idOgg );
    elem.src = normalImages[idOgg].src;
  } else if (listObjImages[idOgg] == "shadow") {
    $("#" + idOgg).hide("normal");
  }
}