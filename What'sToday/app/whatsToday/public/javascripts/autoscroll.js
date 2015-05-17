/*!
 * Auto horizontal scroll script
 */
var $x = 0;
function auto_horizontal_scroll() {
    var $dbox = document.getElementById("ahs_displayBox");
    $dbox.scrollLeft = ++$x;
    if ($x < $dbox.scrollWidth - $dbox.clientWidth) {
        setTimeout("auto_horizontal_scroll()", 20);
    } else {
        $x = 0;
        $dbox.scrollLeft = 0;
        setTimeout("auto_horizontal_scroll()", 20);
    }
}
