<?php
/**
 * Plugin Name: CodeManas Customizer Reset
 * Plugin URI: https://www.codemanas.com/
 * Description: Reset CodeManas themes customizer options
 * Version: 1.0.0
 * Author: CodeManas
 * Author URI: https://www.codemanas.com/
 * Text Domain: codemanas-customizer-reset
 */

define( 'CODEMANAS_THEME_CUSTOMIZER_RESET_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Codemanas Customizer Reset
 *
 * @since 1.0.0
 */
if ( ! class_exists( 'Codemanas_Theme_Customizer_Reset' ) ) :

	class Codemanas_Theme_Customizer_Reset {

		private static $instance;

		/**
		 * @var WP_Customize_Manager
		 */
		private $wp_customize;

		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'customize_register', array( $this, 'customize_register' ) );
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'controls_scripts' ) );
			add_action( 'wp_ajax_codemanas_customizer_reset', array( $this, 'ajax_customizer_reset' ) );
		}

		/**
		 * Customize Register Description
		 *
		 * @param  object $wp_customize Object of WordPress customizer.
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function customize_register( $wp_customize ) {
			$this->wp_customize = $wp_customize;
		}

		/**
		 * AJAX Customizer Reset
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function ajax_customizer_reset() {

			//Is customizer preview page ?
			if ( ! $this->wp_customize->is_preview() ) {
				wp_send_json_error( 'Not Allowed' );
			}

			// Validate nonce.
			check_ajax_referer( 'codemanas-theme-reset-customizer', 'nonce' );

			$theme_settings = 'theme_mods_' . get_template();
			if ( ! empty( $theme_settings ) ) {

				$theme_mods      = get_theme_mods();
				$default_setting = array();
				if ( ! empty( $theme_mods ) ) {
					$default_setting['nav_menu_locations'] = $theme_mods['nav_menu_locations'];
				}

				if ( defined( CODEMANAS_THEME_VERSION ) ) {
					$default_setting['theme_version'] = CODEMANAS_THEME_VERSION;
				}

				if ( ! empty( $default_setting ) ) {
					update_option( $theme_settings, $default_setting );
				} else {
					delete_option( $theme_settings );
				}
			}

			wp_send_json_error( 'pass' );

			wp_die();
		}

		/**
		 * Customizer Scripts
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function controls_scripts() {

			// Enqueue JS.
			wp_enqueue_script( 'codmenas-theme-reset-customizer', CODEMANAS_THEME_CUSTOMIZER_RESET_URI . 'assets/js/busify-reset-customizer.js', array( 'jquery' ), null, true );

			// Add localize JS.
			wp_localize_script( 'codmenas-theme-reset-customizer', 'cmCustomizerReset', array(
				'reset' => array(
					'stringConfirm' => __( 'Warning! This will remove all the theme customizer options!', 'codemanas-customizer-reset' ),
					'stringReset'   => __( 'Reset All', 'codemanas-customizer-reset' ),
					'security'      => wp_create_nonce( 'codemanas-theme-reset-customizer' ),
				),
			) );
		}
	}

endif;

//Load on plugins loaded
add_action( 'plugins_loaded', array( 'Codemanas_Theme_Customizer_Reset', 'get_instance' ), 9999 );
