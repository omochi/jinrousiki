/*
  ◆TOPページ制御関数
*/

/* getElementsByClass() 代替関数 */
function fold_menu(element) {
  var list = element.childNodes;

  for (var i = 1; i < list.length; i++) {
    child = list[i];
    if (child.className == 'menu-name') {
      continue;
    }
    if (child.tagName == 'LI') {
      if (!child.style.display || child.style.display == 'none') {
	child.style.display = 'block';
      } else {
	child.style.display = 'none';
      }
    }
  }
}
