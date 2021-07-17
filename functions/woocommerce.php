<?php
function filter_woocommerce_return_to_shop_redirect( $wc_get_page_permalink ) {
    $link = get_permalink( get_page_by_path('shop') );
    // make filter magic happen here...
    return $link;
};

// add the filter
add_filter( 'woocommerce_return_to_shop_redirect', 'filter_woocommerce_return_to_shop_redirect', 10, 1 );
