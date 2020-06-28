<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/victayo
 * @since      1.0.0
 *
 * @package    Paga_Integration
 * @subpackage Paga_Integration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Paga_Integration
 * @subpackage Paga_Integration/public
 * @author     Victor Temitayo Okala <victokala@gmail.com>
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/paga-utility.php';

class Paga_Integration_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Paga_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Paga_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/paga-integration-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Paga_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Paga_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'angular', plugin_dir_url( __FILE__ ) . 'js/angular.min.js', [], $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/paga-integration-public.js', array( 'jquery' ), $this->version, false );
	}

	public function airtimeRecharge(){
		ob_start();
		$data['reference'] = paga_generate_reference();
		$data['account_number'] = get_option('paga_account');
		$data['public_key'] = get_option('paga_credential');
		$data['checkout_url'] = get_option('paga_url').'/checkout';
		include plugin_dir_path( __FILE__ ) . 'partials/airtime-recharge-form.php';

		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	public function billPayment(){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'controllers/PagaController.php';
		ob_start();
		$data['merchants_url'] = get_site_url().'/wp-json/paga/v1/getMerchants?api_key='.API_TOKEN.'&filter=1';
		$data['merchant_services_url'] = get_site_url().'/wp-json/paga/v1/getMerchantServices?api_key='.API_TOKEN;
		$data['checkout_url'] = get_option('paga_url').'/checkout';
		$data['reference'] = paga_generate_reference();
		$data['account_number'] = get_option('paga_account');
		$data['public_key'] = get_option('paga_credential');
		include plugin_dir_path( __FILE__ ) . 'partials/bill-payment-form.php';
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

}
