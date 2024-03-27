<?php
/**
 * Plugin Name:           AutomatorWP - WP Everest Forms
 * Plugin URI:            https://automatorwp.com/add-ons/everest-forms/
 * Description:           Connect AutomatorWP with WP Everest Forms
 * Version:               1.1.0
 * Author:                AutomatorWP
 * Author URI:            https://automatorwp.com/
 * Text Domain:           automatorwp-everest-forms
 * Domain Path:           /languages/
 * Requires at least:     4.4
 * Tested up to:          6.4
 * License:               GNU AGPL v3.0 (http://www.gnu.org/licenses/agpl.txt)
 *
 * @package               AutomatorWP\EverestForms
 * @author                AutomatorWP
 * @copyright             Copyright (c) AutomatorWP
 */

final class AutomatorWP_EverestForms {

    /**
     * @var         AutomatorWP_EverestForms $instance The one true AutomatorWP_EverestForms
     * @since       1.0.0
     */
    private static $instance;

    /**
     * Get active instance
     *
     * @access      public
     * @since       1.0.0
     * @return      AutomatorWP_EverestForms self::$instance The one true AutomatorWP_EverestForms
     */
    public static function instance() {
        if( !self::$instance ) {
            self::$instance = new AutomatorWP_EverestForms();
            self::$instance->constants();
            self::$instance->includes();
            self::$instance->hooks();
            self::$instance->load_textdomain();
        }

        return self::$instance;
    }


    /**
     * Setup plugin constants
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function constants() {
        // Plugin version
        define( 'AUTOMATORWP_EVERESTFORMS_VER', '1.1.0' );

        // Plugin file
        define( 'AUTOMATORWP_EVERESTFORMS_FILE', __FILE__ );

        // Plugin path
        define( 'AUTOMATORWP_EVERESTFORMS_DIR', plugin_dir_path( __FILE__ ) );

        // Plugin URL
        define( 'AUTOMATORWP_EVERESTFORMS_URL', plugin_dir_url( __FILE__ ) );
    }

    /**
     * Include plugin files
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function includes() {

        if( $this->meets_requirements() ) {

            // Includes
            require_once AUTOMATORWP_EVERESTFORMS_DIR . 'includes/ajax-functions.php';
            require_once AUTOMATORWP_EVERESTFORMS_DIR . 'includes/functions.php';

            // Triggers
            require_once AUTOMATORWP_EVERESTFORMS_DIR . 'includes/triggers/submit-form.php';
            //require_once AUTOMATORWP_EVERESTFORMS_DIR . 'includes/triggers/submit-field-value.php';
            // Anonymous Triggers
            // require_once AUTOMATORWP_EVERESTFORMS_DIR . 'includes/triggers/anonymous-submit-form.php';
            // require_once AUTOMATORWP_EVERESTFORMS_DIR . 'includes/triggers/anonymous-submit-field-value.php';

        }
    }

    /**
     * Setup plugin hooks
     *
     * @access      private
     * @since       1.0.0
     * @return      void
     */
    private function hooks() {

        add_action( 'automatorwp_init', array( $this, 'register_integration' ) );

        add_filter( 'automatorwp_licenses_meta_boxes', array( $this, 'license' ) );

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Registers this integration
     *
     * @since 1.0.0
     */
    function register_integration() {

        automatorwp_register_integration( 'everest-forms', array(
            'label' => 'WP Everest Forms',
            'icon'  => AUTOMATORWP_EVERESTFORMS_URL . 'assets/everestforms.svg',
        ) );

    }

    /**
     * Licensing
     *
     * @since 1.0.0
     *
     * @param array $meta_boxes
     *
     * @return array
     */
    function license( $meta_boxes ) {

        $meta_boxes['automatorwp-everest-forms-license'] = array(
            'title' => 'WP Everest Forms',
            'fields' => array(
                'automatorwp_everest_forms_license' => array(
                    'type' => 'edd_license',
                    'file' => AUTOMATORWP_EVERESTFORMS_FILE,
                    'item_name' => 'WP Everest Forms',
                ),
            )
        );

        return $meta_boxes;

    }

    /**
     * Plugin admin notices.
     *
     * @since  1.0.0
     */
    public function admin_notices() {

        if ( ! $this->meets_requirements() && ! defined( 'AUTOMATORWP_ADMIN_NOTICES' ) ) : ?>

            <div id="message" class="notice notice-error is-dismissible">
                <p>
                    <?php printf(
                        __( 'AutomatorWP - WP Fluent Forms requires %s and %s in order to work. Please install and activate them.', 'automatorwp-everest-forms' ),
                        '<a href="https://wordpress.org/plugins/automatorwp/" target="_blank">AutomatorWP</a>',
                        '<a href="https://wordpress.org/plugins/everest-forms/" target="_blank">WP Fluent Forms</a>'
                    ); ?>
                </p>
            </div>

            <?php define( 'AUTOMATORWP_ADMIN_NOTICES', true ); ?>

        <?php endif;

    }

    /**
     * Check if there are all plugin requirements
     *
     * @since  1.0.0
     *
     * @return bool True if installation meets all requirements
     */


    private function meets_requirements() {

        if ( ! class_exists( 'AutomatorWP' ) ) {
            return false;
        }

        if ( ! class_exists( 'EverestForms' ) ) {
            
            return false;
        }

        return true;

    }    


    /**
     * Internationalization
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function load_textdomain() {

        // Set filter for language directory
        $lang_dir = AUTOMATORWP_EVERESTFORMS_DIR . '/languages/';
        $lang_dir = apply_filters( 'automatorwp_everest_forms_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), 'automatorwp-everest-forms' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'automatorwp-everest-forms', $locale );

        // Setup paths to current locale file
        $mofile_local   = $lang_dir . $mofile;
        $mofile_global  = WP_LANG_DIR . '/automatorwp-everest-forms/' . $mofile;

        if( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/automatorwp-everest-forms/ folder
            load_textdomain( 'automatorwp-everest-forms', $mofile_global );
        } elseif( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/automatorwp-everest-forms/languages/ folder
            load_textdomain( 'automatorwp-everest-forms', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'automatorwp-everest-forms', false, $lang_dir );
        }

    }

}

/**
 * The main function responsible for returning the one true AutomatorWP_EverestForms instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \AutomatorWP_EverestForms The one true AutomatorWP_EverestForms
 */
function AutomatorWP_EverestForms() {
    return AutomatorWP_EverestForms::instance();
}
add_action( 'plugins_loaded', 'AutomatorWP_EverestForms' );
