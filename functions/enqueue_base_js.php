<?php
if ( ! function_exists( 'lta_enqueue_base_js' ) ) :

function lta_enqueue_base_js()
{
    wp_enqueue_script('every-page-js', get_stylesheet_directory_uri() . '/js/every-page.js', array(), '0.1' );
}

add_action('wp_enqueue_scripts', 'lta_enqueue_base_js');

endif;
