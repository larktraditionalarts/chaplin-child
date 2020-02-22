<?php
if ( ! function_exists( 'lta_enqueue_base_styles' ) ) :

function lta_enqueue_base_styles()
{
    $parent_style = 'chaplin-style';

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style',
        get_stylesheet_directory_uri() . '/style.less',
        array($parent_style),
        wp_get_theme()->get('Version')
    );

    $wp_scripts = wp_scripts();
    wp_enqueue_style('plugin_name-admin-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/' . $wp_scripts->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.css');
}

add_action('wp_enqueue_scripts', 'lta_enqueue_base_styles');

endif;
