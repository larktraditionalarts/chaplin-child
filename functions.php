<?php
require_once('custom_shortcodes.php');

add_action('wp_enqueue_scripts', 'chaplin_child_enqueue_styles');
add_action('wp_enqueue_scripts', 'lta_enqueue_scripts');
add_action( 'wpforms_process_complete', 'lta_process_contact_form', 10, 4 );

function chaplin_child_enqueue_styles()
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

/**
 * Enqueue custom scripts
 */
function lta_enqueue_scripts()
{

    wp_enqueue_script('jquery-ui-dialog');
    wp_enqueue_script( 'workshop-dialog', get_stylesheet_directory_uri() . '/js/workshop-dialog.js', array('jquery-ui-dialog'), null );
    wp_enqueue_script('every-page-js', get_stylesheet_directory_uri() . '/js/every-page.js', array(), null );

    if (is_page('workshop-search')) {
        wp_enqueue_script( 'promise-polyfill', get_stylesheet_directory_uri() . '/js/promise-polyfill.js', array(), null );
        wp_enqueue_script( 'lodash', get_stylesheet_directory_uri() . '/js/lodash.min.js', array(), null );
        wp_enqueue_script( 'workshop-ajax-filters', get_stylesheet_directory_uri() . '/js/workshop-filters.js', array('promise-polyfill', 'lodash'), null );
    }
}


function lta_process_contact_form( $fields, $entry, $form_data, $entry_id )
{
    $id = absint($form_data['id']);

    // Restrict output to our two forms.
    if ($id !== 1327 && $id !== 112) {
        return;
    }

    $values = lta_map_fields_to_assoc($fields);

    if ($values['Newsletter'] || $id === 1327) {
        lta_add_mailing_list($values);
    }

    if ($id === 112) {
        lta_add_ticket($values);
    }
}

function lta_add_ticket($values)
{
    $json_values = [
        'source' => 'API',
        'name' => $values['First name'] . ' ' . $values['Last name'],
        'email' => $values['Email'],
        'subject' => $values['Subject'],
        'ip' => lta_get_ip(),
        'message' => 'data:text/plain,MESSAGE ' . $values['Message'],
    ];

    $ch = curl_init();

    $data_string = json_encode($json_values);
    curl_setopt($ch, CURLOPT_URL,            "https://support.larktraditionalarts.info/api/tickets.json" );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_POST,           true );
    curl_setopt($ch, CURLOPT_SAFE_UPLOAD,    true );
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $data_string );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Accept: application/json',
        'X-API-KEY: ' . OSTICKET_TOKEN,
        'Content-Length: ' . strlen($data_string))
    );

    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    if( WP_DEBUG === true || curl_errno($ch) || $info['http_code'] >= 400 ) {
        error_log('osticket/curl error: ' . $output);
    }

}

function lta_add_mailing_list($values)
{
    // add to email list
    $list_form = array();
    $list_form['EMAIL'] = $values['Email'];
    $list_form['FIRST_NAME'] = $values['First name'];
    $list_form['LAST_NAME'] = $values['Last name'];
    $list_form['FORCE_SUBSCRIBE'] = 'yes';
    $list_form['REQUIRE_CONFIRMATION'] = 'yes';

    if ($values['Brochure']) {
        // also add brochure values
        $list_form['MERGE_MAILING_STREET_ADDRESS'] = $values['Address'];
        $list_form['MERGE_MAILING_CITY'] = $values['City'];
        $list_form['MERGE_MAILING_STATE'] = $values['State'];
        $list_form['MERGE_MAILING_ZIPCODE'] = $values['Zip'];
        $list_form['MERGE_MAILING_COUNTRY'] = $values['Country'];
    }

    if ($values['Message']) $list_form['MERGE_MESSAGE'] = $values['Message'];

    $ch = curl_init();

    $data_string = json_encode($list_form);                                                                                   
    curl_setopt($ch, CURLOPT_URL,            "https://mail.larkcamp.org/api/subscribe/DAgFxfpDg?access_token=" . MAILTRAIN_TOKEN );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt($ch, CURLOPT_POST,           true );
    curl_setopt($ch, CURLOPT_SAFE_UPLOAD,    true );
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $data_string ); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Accept: application/json',
        'Content-Length: ' . strlen($data_string))                                                                       
    );          
    $output = curl_exec($ch);
    $info = curl_getinfo($ch);
    if( WP_DEBUG === true || curl_errno($ch) || $info['http_code'] >= 400 ) {
        error_log('mailtrain/curl error: ' . $output);
    }
}

function lta_map_fields_to_assoc($fields)
{
    $output = array();
    foreach ($fields as $field) {
        $output[$field['name']] = $field['value'];

        if ($field['type'] === 'checkbox') $output[$field['name']] = !!$field['value'];
        if ($field['type'] === 'name') {
            $output['First name'] = $field['first'];
            $output['Last name'] = $field['last'];
        }
    }

    return $output;
}

function lta_get_ip()
{
    // check ip from share internet
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
        // to check ip is pass from proxy
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}
