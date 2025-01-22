<?php
/**
 * File Name: class-yith-ywpi-pdf-template-list-table-fixed.php
 * Description: Fixed Class for YITH_YWPI_PDF_Template_List_Table to ensure PHP 8.2 compatibility.
 *
 * @package  YITH\PDF_Invoice\PDF_Builder
 * @version  1.0.0
 */

 if ( ! class_exists( 'YITH_YWPI_PDF_Template_List_Table_Fixed' ) ) {

     class YITH_YWPI_PDF_Template_List_Table_Fixed extends YITH_YWPI_PDF_Template_List {

         /**
          * Singleton instance.
          *
          * @var YITH_YWPI_PDF_Template_List_Table_Fixed
          */
         protected static $instance;

         /**
          * Options for the template.
          *
          * @var array
          */
         protected $options = [];

         /**
          * Returns a single instance of the class.
          *
          * @return YITH_YWPI_PDF_Template_List_Table_Fixed
          */
         public static function get_instance() {
             if ( ! self::$instance instanceof self ) {
                 self::$instance = new self();
             }
             return self::$instance;
         }

         /**
          * Constructor.
          */
         public function __construct() {
             parent::__construct();

             $this->post_type = 'yith_ywpi_pdf_template';

             $this->options = include_once YITH_YWPI_DIR . 'plugin-options/metabox/pdf-template-options.php';
             if ( ! is_array( $this->options ) ) {
                 $this->options = [];
                 error_log( 'YITH PDF Template options failed to load as an array.' );
             }

             add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
             add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
             add_filter( 'post_row_actions', array( $this, 'customize_row_actions' ), 10, 2 );
         }

         /**
          * Change the bulk post updated messages.
          *
          * @param array $messages    List of bulk update messages.
          * @param array $bulk_counts Bulk update counts.
          *
          * @return array Updated messages.
          */
         public function change_bulk_post_updated_messages( $messages, $bulk_counts ) {
             // Modify messages if needed or return unmodified messages.
             return $messages;
         }

         /**
          * Save the meta box data.
          *
          * @param int    $post_id The post ID.
          * @param object $post    The post object.
          */
         public function save_post( $post_id, $post ) {
             if ( ! isset( $_POST['ywpi_pdf_template_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ywpi_pdf_template_nonce'] ) ), 'ywpi_pdf_template' ) ) {
                 return;
             }

             $posted = $_POST;

             $obj = yith_ywpi_get_pdf_template( $post_id );

             $changes = [];
             foreach ( $obj->get_data() as $key => $value ) {
                 $changes[ $key ] = $posted[ $key ] ?? 'no';
             }

             $obj->set_props( $changes );
             $obj->save();
         }

         /**
          * Add meta box.
          */
         public function add_metabox() {
             remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
             add_meta_box( 'yith-ywpi-' . $this->post_type . '-metabox', __( 'Options', 'yith-woocommerce-pdf-invoice' ), array( $this, 'option_metabox' ), $this->post_type, 'normal', 'default' );
         }

         /**
          * Render the meta box.
          *
          * @param WP_Post $post The current post.
          */
         public function option_metabox( $post ) {
             wp_nonce_field( 'ywpi_pdf_template', 'ywpi_pdf_template_nonce' );

             echo '<div class="ywpi-metabox-wrapper">';
             echo '<table class="form-table">';
             foreach ( $this->options as $option ) {
                 echo '<tr>';
                 echo '<th><label for="' . esc_attr( $option['id'] ) . '">' . esc_html( $option['label'] ) . '</label></th>';
                 echo '<td>';
                 echo '<input type="' . esc_attr( $option['type'] ) . '" id="' . esc_attr( $option['id'] ) . '" name="' . esc_attr( $option['id'] ) . '" value="' . esc_attr( get_post_meta( $post->ID, $option['id'], true ) ) . '">';
                 echo '<p class="description">' . esc_html( $option['desc'] ) . '</p>';
                 echo '</td>';
                 echo '</tr>';
             }
             echo '</table>';
             echo '</div>';
         }

         /**
          * Customize row actions.
          *
          * @param array   $actions The existing actions.
          * @param WP_Post $post    The current post.
          *
          * @return array Modified actions.
          */
         public function customize_row_actions( $actions, $post ) {
             if ( $post->post_type === $this->post_type ) {
                 return [];
             }
             return $actions;
         }
     }
 }

 // Instantiate the fixed class.
 YITH_YWPI_PDF_Template_List_Table_Fixed::get_instance();
