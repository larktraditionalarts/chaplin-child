<?php
// shortcodes
require_once('shortcodes/checkfilter.php');
require_once('shortcodes/print_evening_schedule.php');
require_once('shortcodes/print_workshop_schedule.php');
require_once('functions/woocommerce.php');

// styles
require_once('functions/enqueue_base_styles.php');

// javascript
require_once('functions/enqueue_base_js.php');

// other wp hooks
require_once('functions/wpforms_process_complete.php');

// enqueue js used only on specific pages
function lta_specific_page_js()
{

    // enqueue popup stuff
    if (is_page(['workshops', 'workshop-search'])) {
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script( 'workshop-dialog', get_stylesheet_directory_uri() . '/js/workshop-dialog.js', array('jquery-ui-dialog'), null);
    }

    // enqueue filter stuff
    if (is_page('workshop-search')) {
        // support scripts
        wp_enqueue_script('promise-polyfill', get_stylesheet_directory_uri() . '/js/promise-polyfill.js', array(), null);
        wp_enqueue_script('lodash', get_stylesheet_directory_uri() . '/js/lodash.min.js', array(), null);

        // actual stuff
        wp_enqueue_script( 'workshop-ajax-filters', get_stylesheet_directory_uri() . '/js/workshop-filters.js', array('promise-polyfill', 'lodash'), null );
    }
}

add_action('wp_enqueue_scripts', 'lta_specific_page_js');

/**
* Removes or edits the 'Protected:' part from posts titles
*/
add_filter( 'protected_title_format', 'remove_protected_text' );
function remove_protected_text() {
	return __('%s');
}
