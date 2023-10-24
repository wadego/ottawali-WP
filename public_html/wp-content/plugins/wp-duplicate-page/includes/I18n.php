<?php
namespace NjtDuplicate;

defined( 'ABSPATH' ) || exit;
/**
 * I18n Logic
 */
class I18n {
	public static function loadPluginTextdomain() {
		if ( function_exists( 'determine_locale' ) ) {
			$locale = determine_locale();
		} else {
			$locale = is_admin() ? get_user_locale() : get_locale();
		}
		unload_textdomain( 'njt_duplicate' );
		load_textdomain( 'njt_duplicate', NJT_DUPLICATE_PLUGIN_PATH . '/i18n/languages/njt_duplicate-' . $locale . '.mo' );
		load_plugin_textdomain( 'njt_duplicate', false, NJT_DUPLICATE_PLUGIN_PATH . '/i18n/languages/' );
	}
}
