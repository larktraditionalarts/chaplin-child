<?php
if ( ! function_exists( 'lta_enqueue_workshop_popup_js' ) ) :

function lta_enqueue_workshop_popup_js()
{
    // support scripts
    wp_enqueue_script('jquery-ui-dialog');

    // actual stuff
    wp_enqueue_script( 'workshop-dialog', get_stylesheet_directory_uri() . '/js/workshop-dialog.js', array('jquery-ui-dialog'), null);
}

endif;
