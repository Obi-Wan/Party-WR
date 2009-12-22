/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function mouseOver( idOgg ) {
  var elem = document.getElementById( idOgg );
  elem.src = hoveredImages[idOgg].src;
}
function mouseOut( idOgg ) {
  var elem = document.getElementById( idOgg );
  elem.src = normalImages[idOgg].src;
}