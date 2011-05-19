/**
 * 
 */
function getAction(form) {
  form.action = encodeURIComponent(form.elements['name'].value) + '/';
}
function changeCategory(form) {
}