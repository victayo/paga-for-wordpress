<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/victayo
 * @since      1.0.0
 *
 * @package    Paga_Integration
 * @subpackage Paga_Integration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Paga_Integration
 * @subpackage Paga_Integration/admin
 * @author     Victor Temitayo Okala <victokala@gmail.com>
 */
class Paga_Integration_Admin
{

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action('admin_menu', [$this, 'paga_integration_menu'], 9);
		add_action('admin_init', [$this, 'register_and_build_fields']);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/select2.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/paga-integration-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

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

		//wp_enqueue_script( string $handle, string $src = '', string[] $deps = array(), string|bool|null $ver = false, bool $in_footer = false )
		wp_enqueue_script('select2', plugin_dir_url(__FILE__) . 'js/select2.min.js', [], '', true);
		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/paga-integration-admin.js', array('jquery'), $this->version, false);
	}

	public function paga_integration_menu()
	{
		//add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		// add_menu_page('Page Integration Page', 'Page Integration', 'administrator', 'paga_integration', [$this, 'menu_page']);
		add_menu_page('Page Integration Page', 'Page Integration', 'administrator', 'paga_integration', [$this, 'display_plugin_admin_settings']);

		//add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
		// add_submenu_page('paga_integration', 'Paga Integration Setting', 'Settings', 'administrator',  'paga_integration_settings', [$this, 'display_plugin_admin_settings']);
	}

	public function display_plugin_admin_settings()
	{
		// set this var to be used in the settings-display view
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
		if (isset($_GET['error_message'])) {
			add_action('admin_notices', [$this, 'settings_page_settings_messages']);
			do_action('admin_notices', $_GET['error_message']);
		}
		require_once 'partials/' . $this->plugin_name . '-admin-settings-display.php';
	}

	public function settings_page_settings_messages($error_message)
	{
		switch ($error_message) {
			case '1':
				$message = __('There was an error adding this setting. Please try again.  If this persists, shoot us an email.', 'my-text-domain');
				$err_code = esc_attr('settings_page_example_setting');
				$setting_field = 'settings_page_example_setting';
				break;
		}
		$type = 'error';
		//add_settings_error( string $setting, string $code, string $message, string $type = 'error' )
		add_settings_error($setting_field, $err_code, $message, $type);
	}

	public function register_and_build_fields()
	{
		$response = wp_remote_get(get_site_url()."/wp-json/paga/v1/getMerchants?api_key=" . API_TOKEN);
		$body = json_decode($response['body']);
		$merchants = $body->merchants;

		// add_settings_section( string $id, string $title, callable $callback, string $page )
		add_settings_section(
			'paga_integration_general_section',
			'',
			[$this, 'paga_integration_section_description'],
			'paga_integration_general_page'
		);

		$organization = [
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'paga_organization',
			'name'      => 'paga_organization',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		$base_url = [
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'paga_url',
			'name'      => 'paga_url',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		$credentials = [
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'paga_credential',
			'name'      => 'paga_credential',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		$secret = [
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'paga_secret',
			'name'      => 'paga_secret',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		$hmac = [
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'paga_hmac',
			'name'      => 'paga_hmac',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		$merchantArgs = [
			'type'      => 'select',
			'subtype'   => 'multiple',
			'id'    => 'paga_merchants',
			'name'      => 'paga_merchants',
			'required' => 'true',
			'multiple' => true,
			'get_options_list' => $merchants,
			'value_type' => 'normal',
			'wp_data' => 'option'
		];

		$account = [
			'type'      => 'input',
			'subtype'   => 'text',
			'id'    => 'paga_account',
			'name'      => 'paga_account',
			'required' => 'true',
			'get_options_list' => '',
			'value_type' => 'normal',
			'wp_data' => 'option'
		];


		// add_settings_field( string $id, string $title, callable $callback, string $page, string $section = 'default', array $args = array() )

		add_settings_field(
			'paga_organization',
			'Organization',
			[$this, 'paga_integration_render_settings_field'],
			'paga_integration_general_page',
			'paga_integration_general_section',
			$organization
		);

		add_settings_field(
			'paga_account',
			'Account Number',
			[$this, 'paga_integration_render_settings_field'],
			'paga_integration_general_page',
			'paga_integration_general_section',
			$account
		);

		add_settings_field(
			'paga_url',
			'Paga Base URL',
			[$this, 'paga_integration_render_settings_field'],
			'paga_integration_general_page',
			'paga_integration_general_section',
			$base_url
		);

		add_settings_field(
			'paga_credential',
			'public key/username',
			[$this, 'paga_integration_render_settings_field'],
			'paga_integration_general_page',
			'paga_integration_general_section',
			$credentials
		);

		add_settings_field(
			'paga_secret',
			'Secret key/credential',
			[$this, 'paga_integration_render_settings_field'],
			'paga_integration_general_page',
			'paga_integration_general_section',
			$secret
		);

		add_settings_field(
			'paga_hmac',
			'HMAC',
			[$this, 'paga_integration_render_settings_field'],
			'paga_integration_general_page',
			'paga_integration_general_section',
			$hmac
		);

		if(count($merchants)){
			add_settings_field(
				'paga_merchants',
				'Merchants',
				[$this, 'paga_integration_render_settings_field'],
				'paga_integration_general_page',
				'paga_integration_general_section',
				$merchantArgs
			);
			register_setting('paga_integration_general_page', 'paga_merchants');
		}

		// register_setting( string $option_group, string $option_name, array $args = array() )
		register_setting('paga_integration_general_page', 'paga_organization');
		register_setting('paga_integration_general_page', 'paga_url');
		register_setting('paga_integration_general_page', 'paga_account');
		register_setting('paga_integration_general_page', 'paga_credential');
		register_setting('paga_integration_general_page', 'paga_secret');
		register_setting('paga_integration_general_page', 'paga_hmac');
	}

	public function menu_page()
	{
		ob_start();
		include plugin_dir_path(__FILE__) . 'partials/paga-integration-admin-display.php';
		$output = ob_get_contents();
		ob_end_clean();
		echo $output;
	}

	public function paga_integration_section_description()
	{
		echo '<p>Settings displayed on your Paga Merchant Dashboard</p>';
	}

	public function paga_integration_render_settings_field($args)
	{
		if ($args['wp_data'] == 'option') {
			$wp_data_value = get_option($args['name']);
		} elseif ($args['wp_data'] == 'post_meta') {
			$wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
		}

		switch ($args['type']) {

			case 'input':
				$value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
				if ($args['subtype'] != 'checkbox') {
					$prependStart = (isset($args['prepend_value'])) ? '<div class="input-prepend"> <span class="add-on">' . $args['prepend_value'] . '</span>' : '';
					$prependEnd = (isset($args['prepend_value'])) ? '</div>' : '';
					$step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
					$min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
					$max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';
					if (isset($args['disabled'])) {
						// hide the actual input bc if it was just a disabled input the info saved in the database would be wrong - bc it would pass empty values and wipe the actual information
						echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr($value) . '" /><input type="hidden" id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
					} else {
						echo $prependStart . '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
					}
					/*<input required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" /><input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost" value="' . esc_attr( $cost ) . '" />*/
				} else {
					$checked = ($value) ? 'checked' : '';
					echo '<input type="' . $args['subtype'] . '" id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
				}
				break;
			case 'select':
				$multiple = isset($args['multiple']) ? $args['multiple'] : false;
				$options = isset($args['get_options_list']) ? $args['get_options_list'] : [];
				$select = "<select multiple='$multiple' class='merchants'  name='".$args['name']."[]'>";
				if(!$wp_data_value){
					$wp_data_value = [];
				}
				foreach ($options as $option) {
					$opt = $option->uuid;
					$selected = $this->isSelected($opt, $wp_data_value);
					if($selected){
						$select .= "<option value='{$option->uuid}' selected='$selected'> {$option->displayName}</option>";
					}else{
						$select .= "<option value='{$option->uuid}'> {$option->displayName}</option>";
					}
				}
				$select .= "</select>";
				echo $select;
			default:
				# code...
				break;
		}
	}

	private function isSelected($opt, $merchants){
		foreach($merchants as $merchant){
			if($merchant == $opt){
				return true;
			}
		}
		return false;
	}
}
