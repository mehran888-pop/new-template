<?php
/**
 * Plugin Name: Neomorph Core
 * Plugin URI: https://example.com/neomorph
 * Description: افزونه همراه قالب نئومورف — پنل مشتری با OTP، باشگاه مشتریان، فاکتور و لینک پرداخت، تیکت، CRM و اتوماسیون، سیستم استخدام مرحله‌ای با امتیازدهی خودکار، اتصال sms.ir و ملی‌پیامک، بله و تلگرام.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Author: Neomorph Team
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: neomorph-core
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'NEOMORPH_CORE_VERSION', '1.0.0' );
define( 'NEOMORPH_CORE_FILE', __FILE__ );
define( 'NEOMORPH_CORE_DIR', __DIR__ );
define( 'NEOMORPH_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * PSR-4 autoloader: NeomorphCore\Foo\Bar → src/Foo/Bar.php
 */
spl_autoload_register(
	function ( $class ) {
		if ( 0 !== strpos( $class, 'NeomorphCore\\' ) ) {
			return;
		}
		$relative = substr( $class, strlen( 'NeomorphCore\\' ) );
		$path     = NEOMORPH_CORE_DIR . '/src/' . str_replace( '\\', '/', $relative ) . '.php';
		if ( is_readable( $path ) ) {
			require_once $path;
		}
	}
);

require_once NEOMORPH_CORE_DIR . '/src/Helpers.php';
require_once NEOMORPH_CORE_DIR . '/src/Plugin.php';

register_activation_hook( __FILE__, array( '\NeomorphCore\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( '\NeomorphCore\Deactivator', 'deactivate' ) );

add_action( 'plugins_loaded', array( '\NeomorphCore\Plugin', 'instance' ), 5 );
