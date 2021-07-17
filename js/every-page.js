jQuery(document).ready(function() {

	$('.lta-mailing-list').hide();
	$('.lta-mailing-list-choices label:contains("brochure")')
		.parent()
		.find('input')
		.on('change', function() {
			if ($(this).prop("checked")) {
				$('.lta-mailing-list').show();
			} else {
				$('.lta-mailing-list').hide();
			}
		});
});