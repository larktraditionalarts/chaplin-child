<?php
if ( ! function_exists( 'lta_enqueue_workshop_filters_js' ) ) :

function lta_enqueue_workshop_filters_js()
{
    // support scripts
    wp_enqueue_script('promise-polyfill', get_stylesheet_directory_uri() . '/js/promise-polyfill.js', array(), null);
    wp_enqueue_script('lodash', get_stylesheet_directory_uri() . '/js/lodash.min.js', array(), null);

    // actual stuff
    wp_enqueue_script( 'workshop-ajax-filters', get_stylesheet_directory_uri() . '/js/workshop-filters.js', array('promise-polyfill', 'lodash'), null );
}

endif;
