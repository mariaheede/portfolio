<?php
/**
 * Keenshot functions and definitions
 *
 * @link
 *
 * @package keenshot
 */

/**
 * Keenshot Theme Version
 */
if ( ! defined( 'KEENSHOT_THEME_VERSION' ) ) {
	$theme_data = wp_get_theme();
	define( 'KEENSHOT_THEME_VERSION', $theme_data->get( 'Version' ) );
}

/**
 * Keenshot Walker Activation
 */
require get_template_directory() . '/inc/walker.php';

/**
 * Keenshot Required Plugin Recommendation
 */
require get_template_directory() . '/inc/tgmpa/recommended-plugins.php';

/**
 * Keenshot Theme Functions
 */
require get_template_directory() . '/inc/custom-functions.php';
require get_template_directory() . '/inc/customizer-functions.php';
require get_template_directory() . '/inc/customizer.php';


/**
 * Keenshot custom customizer function definations
 */

require get_template_directory() . '/customizer-repeater/inc/footer-settings.php';
require get_template_directory() . '/customizer-repeater/inc/custom-login-page.php';

/**
 * Keenshot extra theme function
 */

require get_template_directory() . '/inc/extras.php';

/**
 * Configure one click demo
 */
require get_template_directory() . '/inc/demo-config.php';


require get_template_directory() . '/inc/class-theme-setup-wizard.php';

/**
 * [Appsero SDK]
 */

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_keenshot() {

    if ( ! class_exists( 'Appsero\Client' ) ) {
      require_once __DIR__ . '/inc/appsero/src/Client.php';
    }

    $client = new Appsero\Client( 'fbecd438-1d80-4227-8c47-8903b648a257', 'Keenshot', __FILE__ );

    // Active insights
    $client->insights()->init();

}

appsero_init_tracker_keenshot();