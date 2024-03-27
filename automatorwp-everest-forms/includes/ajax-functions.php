<?php
/**
 * Ajax Functions
 *
 * @package     AutomatorWP\EverestForms\Ajax_Functions
 * @since       1.0.0
 */
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Ajax function for selecting forms
 *
 * @since 1.0.0
 */
function automatorwp_everest_forms_ajax_get_forms() {
    // Security check, forces to die if not security passed
    check_ajax_referer( 'automatorwp_admin', 'nonce' );

    global $wpdb;

    // Pull back the search string
    $search = isset( $_REQUEST['q'] ) ? $wpdb->esc_like( $_REQUEST['q'] ) : '';

    $results = array();

    // Get the forms

    
    $forms = wpFluent()->table( 'everest_forms_forms' )
        ->select( array( 'id', 'title' ) )
        ->where( 'title', 'LIKE', "%{$search}%" )
        ->get();

    foreach( $forms as $form ) {
        $results[] = array(
            'id' => $form->id,
            'text' => $form->title,
        );
    }

    // Prepend option none
    $results = automatorwp_ajax_get_ajax_results_option_none( $results );

    // Return our results
    wp_send_json_success( $results );
    die;

}
add_action( 'wp_ajax_automatorwp_everest_forms_get_forms', 'automatorwp_everest_forms_ajax_get_forms', 5 );