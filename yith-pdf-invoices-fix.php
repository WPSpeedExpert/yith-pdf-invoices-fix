<?php
/**
 * Plugin Name:       YITH PDF Invoices Fix
 * Plugin URI:        https://octahexa.com
 * Description:       Fixes issues with the YITH WooCommerce PDF Invoice plugin for compatibility with PHP 8.2+.
 * Version:           1.0.0
 * Author:            OctaHexa
 * Author URI:        https://octahexa.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Theme URI:  https://github.com/WPSpeedExpert/yith-pdf-invoices-fix

 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Override the YITH_YWPI_PDF_Template_List_Table class to fix PHP 8.2 compatibility issues.
 */
class YITH_PDF_Invoices_Fix {

    /**
     * Initialize the fixes by hooking into the system.
     */
    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'override_classes' ), 20 );
    }

    /**
     * Override classes and apply fixes.
     */
    public function override_classes() {
        if ( ! class_exists( 'YITH_YWPI_PDF_Template_List_Table' ) ) {
            return;
        }

        // Unregister the existing singleton instance to override the class.
        remove_action( 'init', array( 'YITH_YWPI_PDF_Template_List_Table', 'get_instance' ) );

        // Include the fixed version of the class.
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-yith-ywpi-pdf-template-list-table-fixed.php';
    }
}

new YITH_PDF_Invoices_Fix();
