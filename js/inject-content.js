jQuery(document).ready(function() {
	injectContent();
});

function injectContent() {
console.log(window.frames);

	jQuery('figure.bboard').each(function() {
                var $el = jQuery(this);
                var contents = $el.find('iframe');
	});
}

function alterAllAnchors() {
    var els = document.querySelectorAll('a');
    for (var i=0; i < els.length; i++) {
        els[i].setAttribute("target", "_blank");
    }
}
