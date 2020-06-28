<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/victayo
 * @since      1.0.0
 *
 * @package    Paga_Integration
 * @subpackage Paga_Integration/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Paga_Integration
 * @subpackage Paga_Integration/includes
 * @author     Victor Temitayo Okala <victokala@gmail.com>
 */

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'services/TransactionLog.php';

class Paga_Integration_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$transactionLog = new TransactionLog();
		$transactionLog->setVersion(PAGA_INTEGRATION_VERSION);
		$transactionLog->activate();
	}

}
