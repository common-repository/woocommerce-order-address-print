<?php
/**
 * WC Dependency Checker
 *
 * Checks if WooCommerce is enabled
 */
 if ( ! class_exists( 'SH_WC_Dependencies' ) )
{
class SH_WC_Dependencies {

	private static $active_plugins;

	public static function init() {

		self::$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	public static function woocommerce_active_check() {

		if ( ! self::$active_plugins ) self::init();

		return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );
	}

}

}
if ( ! function_exists( 'sh_is_woocommerce_active' ) ) {
	function sh_is_woocommerce_active() {
		return SH_WC_Dependencies::woocommerce_active_check();
	}
}

/**
		 * WooCommerce not active notice.
		 *
		 * @return string Fallack notice.
		 */
if ( ! function_exists( 'wcop_need_woocommerce' ) ) {
function wcop_need_woocommerce() {
			$error = sprintf( __( 'WooCommerce Shipping Address Labels Print requires %sWooCommerce%s to be installed & activated!' , 'wpo_wclabels' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>' );

			$message = '<div class="error"><p>' . $error . '</p></div>';

			echo $message;
		}
}
