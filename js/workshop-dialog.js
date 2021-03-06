jQuery(document).ready(function() {
	bindWorkshopsClickHandlers();
});

function bindWorkshopsClickHandlers() {
	jQuery('.workshops-schedule').on('click', '.workshop', function() {
		var $el = jQuery(this);
		// console.log('clicked on ' + $el.data('workshop-name'));

		if (!$el.data('workshop-name')) return;

		var html = '<div class="workshop-dialog">'
			+ $el.data('workshop-body')
			+ '<h4>' + $el.data('instructor-name') + '</h4>'
			+ '<img src="' + $el.data('instructor-pic') + '" />'
			+ $el.data('instructor-body');

		if ($el.data('instructor-media1')) {
			html += '<audio controls><source src="'+ $el.data('instructor-media1') +'" type="audio/mpeg"></audio>';
		}

		if ($el.data('instructor-media2')) {
			html += '<audio controls><source src="'+ $el.data('instructor-media2') +'" type="audio/mpeg"></audio>';
		}

		if ($el.data('instructor-website')) {
			html += '<div class="wp-block-button"><a class="wp-block-button__link" target="_blank" href="'+ $el.data('instructor-website') +'">Website</a></div>';
		}

		if ($el.data('instructor-youtube')) {
			html += '<div class="wp-block-button"><a class="wp-block-button__link" target="_blank" href="'+ $el.data('instructor-youtube') +'">Youtube</a></div>';
		}

		html += '</div>';

		var $dialog = $(html).dialog({
			autoOpen: true,
			closeOnEscape: true,
			modal: true,
			title: $el.data('workshop-name'),
			buttons: [],
			minWidth: Math.min(1000, $(window).width() * 0.8),
			maxHeight: $(window).height() * 0.8,
			close: function( event, ui ) {
				$dialog.dialog('destroy');
			},
			open: function(event, ui) {
				$('.ui-widget-overlay').bind('click', function() {
					$dialog.dialog('close');
				});

				$('*').blur();
				$('.ui-dialog-content').scrollTop(0);

			}
		});
	});
}