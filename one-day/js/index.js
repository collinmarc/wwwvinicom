/* 

Made checkboxes behave like radio buttons
bcos' you can't deselected radio buttons...!

*/

var cbs = Array.prototype.slice.apply(document.querySelectorAll('#screen input')),
caption = document.getElementById("caption");
function cbclick (e) {
	if (e.target.type === "checkbox") {
		cbs.forEach(
			function (cb) {
				if (cb.id !== e.target.id) {
					if (cb.checked) cb.checked = false;
				}
			}
		);
	} else {
		if (e.target.alt) {
			caption.innerHTML = e.target.alt;
		}
	}
}