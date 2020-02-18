jQuery(document).ready(function() {
	$('.donate-button a').click(function(event) {
		event.preventDefault();
		$('body').append('<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank" id="secret-paypal-button"><input type="hidden" name="cmd" value="_s-xclick" /><input type="hidden" name="hosted_button_id" value="HX34NQMZC2RV6" /><input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" /><img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" /></form>');
		$('#secret-paypal-button').submit();
	});
	
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