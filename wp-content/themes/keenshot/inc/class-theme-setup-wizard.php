<?php

class Keen_Theme_Setup_Wizard {

	protected $step;
	protected $steps = [];

	protected $tgmpa;

	public function __construct() {

		if ( is_admin() && isset( $_GET['activated'] ) ) {
			//redirect to setup page after activating the theme
			wp_redirect( admin_url( 'themes.php?page=keenshot-setup' ) );
		}

		if ( current_user_can( 'edit_theme_options' ) ) {

			add_action( 'admin_menu', [ $this, 'admin_menu' ] );
			add_action( 'admin_init', [ $this, 'remove_tgmpa_menu' ] );

			add_action( 'keen_setup_after_activate_plugin', [ $this, 'redirect_after_plugin_install' ] );
			add_action( 'keen_setup_after_install_plugin', [ $this, 'redirect_after_plugin_install' ] );
			if ( ! empty( $_REQUEST['action'] ) && 'tgmpa-bulk-install' == $_REQUEST['action'] ) {
				add_action( 'upgrader_process_complete', [ $this, 'redirect_after_plugin_install' ] );
			}

			add_filter( 'pt-ocdi/plugin_page_display_callback_function', '__return_false' );
			add_filter( 'pt-ocdi/plugin_intro_text', [ $this, 'ocdi_intro_text' ] );
			add_filter( 'pt-ocdi/plugin_page_setup', [ $this, 'remove_ocdi_menu' ] );
		}

		//prevent Elementor activation redirection
		set_transient( 'elementor_activation_redirect', true, - 1 );

		//prevent WP Forms
		remove_action( 'admin_init', [ 'WPForms_Welcome', 'redirect' ], 9999 );
	}

	public function admin_menu() {
		add_theme_page( 'Keenshot Setup', 'Keenshot Setup', 'manage_options', 'keenshot-setup', [
			$this,
			'setup_wizard'
		] );

	}

	public function setup_wizard() {
		$this->steps = array(
			'install_plugins' => array(
				'name'    => __( 'Install Plugins', 'keenshot' ),
				'view'    => array( $this, 'install_plugins' ),
				'handler' => array( $this, 'install_plugins_save' ),
			),
			'import_demo'     => array(
				'name'    => __( 'Import Demo', 'keenshot' ),
				'view'    => array( $this, 'import_demo' ),
				'handler' => array( $this, 'import_demo_save' ),
			),

			'next_steps' => array(
				'name'    => __( 'Ready!', 'keenshot' ),
				'view'    => array( $this, 'setup_ready' ),
				'handler' => '',
			),
		);

		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'], $this );
		}

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	public function setup_wizard_header() {
		printf(
			'<h1 id="keen-logo"><a href="http://themes.keendevs.com/keenshot/"><img src="%1$s" alt="Keenshot" /></a></h1>',
			get_template_directory_uri() . '/assets/images/setup-logo.png'
		);
	}

	public function setup_wizard_steps() {
		$output_steps = $this->steps;

		?>
        <ol class="keen-setup-steps">
			<?php
			foreach ( $output_steps as $step_key => $step ) {
				$is_completed = array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true );

				if ( $step_key === $this->step ) {
					?>
                    <li class="active"><?php echo esc_html( $step['name'] ); ?></li>
					<?php
				} elseif ( $is_completed ) {
					?>
                    <li class="done">
                        <a href="<?php echo esc_url( add_query_arg( 'step', $step_key, remove_query_arg( 'activate_error' ) ) ); ?>"><?php echo esc_html( $step['name'] ); ?></a>
                    </li>
					<?php
				} else { ?>
                    <li><?php echo esc_html( $step['name'] ); ?></li>
					<?php
				}
			}
			?>
        </ol>
		<?php
	}

	public function setup_wizard_content() {
		echo( '<div class="keen-setup-content step-' . $this->step . '">' );
		if ( ! empty( $this->steps[ $this->step ]['view'] ) ) {
			call_user_func( $this->steps[ $this->step ]['view'], $this );
		}
		echo '</div>';
	}

	public function setup_wizard_footer() {
		echo '<div class="keen-setup-footer">';
		if ( 'install_plugins' === $this->step ) {
			printf( '<a class="keen-setup-footer-links" href="%1$s">%2$s</a>', esc_url( admin_url() ), esc_html__( 'Not right now', 'keenshot' ) );
		} elseif ( 'install_plugins' === $this->step ) {
			printf( '<a class="keen-setup-footer-links" href="%1$s">%2$s</a>', esc_url( $this->get_next_step_link() ), esc_html__( 'Skip this step', 'keenshot' ) );
		}
		echo '</div>';
	}

	public function install_plugins() {
		$this->tgmpa = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );

		$plugins = array(
			'all'      => array(), // Meaning: all plugins which still have open actions.
			'install'  => array(),
			'update'   => array(),
			'activate' => array(),
		);

		$lists = '';
		foreach ( $this->tgmpa->plugins as $slug => $plugin ) {
			if ( $this->tgmpa->is_plugin_active( $slug ) && false === $this->tgmpa->does_plugin_have_update( $slug ) ) {
				// No need to display plugins if they are installed, up-to-date and active.
				continue;
			} else {
				$plugins['all'][ $slug ] = $plugin;

				if ( ! $this->tgmpa->is_plugin_installed( $slug ) ) {
					$plugins['install'][ $slug ] = $plugin;
				} else {
					if ( false !== $this->tgmpa->does_plugin_have_update( $slug ) ) {
						$plugins['update'][ $slug ] = $plugin;
					}

					if ( $this->tgmpa->can_plugin_activate( $slug ) ) {
						$plugins['activate'][ $slug ] = $plugin;
					}
				}
			}

			$lists .= sprintf( '<li><a href="https://wordpress.org/plugins/%1$s" target="_blank">%2$s</a> %3$s</li>', $slug, $plugin['name'], current( $this->get_plugin_actions( $plugin ) ) );

		}

		?>
        <div class="required-plugins-wrapper">
			<?php if ( ! empty( array_merge( $plugins['install'], $plugins['activate'], $plugins['update'] ) ) ) { ?>

                <h1 class="heading">Recommended Plugins for KeenShot:</h1>

                <form action="<?php echo admin_url( 'themes.php?page=tgmpa-install-plugins' ); ?>" method="post">
                    <input type="hidden" name="action" value="tgmpa-bulk-install">

					<?php

					wp_nonce_field();

					foreach ( $this->tgmpa->plugins as $slug => $plugin ) {
						if ( $this->tgmpa->is_plugin_installed( $slug ) ) {
							continue;
						}
						printf( '<input type="hidden" name="plugin[]" value="%1$s">', $slug );
					}

					?>


                    <button type="submit" class="button">Install All</button>
                </form>

                <ul class="required-plugins">
					<?php echo $lists; ?>
                </ul>
			<?php } else {
				printf( '<h3 class="description">All the recommended plugins are activated. Now, You can continue to import the demo.</h3>' );
			} ?>


        </div>

        <div class="step-action">
			<?php

			$demo_required = array(
				'one-click-demo-import' => 'One Click Demo Import',
				'elementor'             => 'Elementor Page Builder',
				'wpforms-lite'          => 'Contact Form by WPForms'
			);

			$links = [];
			foreach ( $demo_required as $slug => $title ) {
				if ( ! $this->tgmpa->is_plugin_active( $slug ) ) {
					$links[] = str_replace( [ 'button', 'Install', 'Activate', ], [
						'',
						$title,
						$title
					], current( $this->get_plugin_actions( $this->tgmpa->plugins[ $slug ] ) ) );
				}
			}

			if ( ! empty( $links ) ) {
				printf( '<p>You must need to activate the <strong>%1$s</strong> plugin, to continue import the demos.</p>', implode( ', ', $links ) );
				printf( '<a href="%1$s" class="btn %2$s">Import Demo <i class="dashicons dashicons-arrow-right-alt"></i> </a>', '#', 'button-disabled' );
			} else {
				printf( '<a href="%1$s" class="btn">Import Demo <i class="dashicons dashicons-arrow-right-alt"></i> </a>', $this->get_next_step_link() );
			}
			?>
        </div>

		<?php

	}

	protected function get_plugin_actions( $item, $return_url = false ) {
		$actions      = array();
		$action_links = array();

		// Display the 'Install' action link if the plugin is not yet available.
		if ( ! $this->tgmpa->is_plugin_installed( $item['slug'] ) ) {
			/* translators: %2$s: plugin name in screen reader markup */
			$actions['install'] = __( 'Install %2$s', 'keenshot' );
		} else {
			// Display the 'Update' action link if an update is available and WP complies with plugin minimum.
			if ( false !== $this->tgmpa->does_plugin_have_update( $item['slug'] ) && $this->tgmpa->can_plugin_update( $item['slug'] ) ) {
				/* translators: %2$s: plugin name in screen reader markup */
				$actions['update'] = __( 'Update %2$s', 'keenshot' );
			}

			// Display the 'Activate' action link, but only if the plugin meets the minimum version.
			if ( $this->tgmpa->can_plugin_activate( $item['slug'] ) ) {
				/* translators: %2$s: plugin name in screen reader markup */
				$actions['activate'] = __( 'Activate %2$s', 'keenshot' );
			}
		}

		// Create the actual links.
		foreach ( $actions as $action => $text ) {
			$nonce_url = wp_nonce_url(
				add_query_arg(
					array(
						'plugin'           => urlencode( $item['slug'] ),
						'tgmpa-' . $action => $action . '-plugin',
					),
					$this->tgmpa->get_tgmpa_url()
				),
				'tgmpa-' . $action,
				'tgmpa-nonce'
			);

			$action_links[ $action ] = sprintf(
				'<a href="%1$s" class="button">' . esc_html( $text ) . '</a>', // $text contains the second placeholder.
				esc_url( $nonce_url ),
				''
			);
		}

		return $action_links;

	}

	public function import_demo() {

		if ( ! class_exists( 'OCDI\OneClickDemoImport' ) ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', function ( $hook ) {
			OCDI\OneClickDemoImport::get_instance()->admin_enqueue_scripts( 'appearance_page_pt-one-click-demo-import' );
		} );

		OCDI\OneClickDemoImport::get_instance()->display_plugin_page();

		?>
        <div class="step-action">
			<?php printf( '<a href="%1$s" class="btn">Done <i class="dashicons dashicons-arrow-right-alt"></i> </a>', $this->get_next_step_link() ); ?>
        </div>
		<?php

	}

	public function setup_ready() { ?>
        <h1 class="heading"><?php esc_html_e( "All are setup!", 'keenshot' ); ?></h1>
        <h3 class="heading"><?php esc_html_e( "Now, You're ready to publish!", 'keenshot' ); ?></h3>

        <div class="step-action">
			<?php printf( '<a href="%1$s" class="btn">Visit Dashboard</a>', admin_url() ); ?>
        </div>
		<?php
	}

	public function get_next_step_link( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}

		$keys = array_keys( $this->steps );
		if ( end( $keys ) === $step ) {
			return admin_url();
		}

		$step_index = array_search( $step, $keys, true );
		if ( false === $step_index ) {
			return '';
		}

		return add_query_arg( 'step', $keys[ $step_index + 1 ], remove_query_arg( 'activate_error' ) );
	}

	function remove_tgmpa_menu() {

		global $submenu;

		if ( ! empty( $submenu ) ) {
			foreach ( $submenu['themes.php'] as $index => $menu ) {
				if ( in_array( 'tgmpa-install-plugins', $menu ) ) {
					unset( $submenu['themes.php'][ $index ] );
					break;
				}
			}
		}
	}

	function redirect_after_plugin_install() {

		if ( empty( $_REQUEST['page'] ) || $_REQUEST['page'] != 'tgmpa-install-plugins' ) {
			return;
		}

		printf( '<script>window.location = "%1$s";</script>', admin_url( 'themes.php?page=keenshot-setup' ) );

		exit();
	}

	function ocdi_intro_text( $intro_text ) {
		ob_start(); ?>
        <div class="ocdi__intro-text">
            <p class="about-description">
				<?php esc_html_e( 'Importing demo data (post, pages, images, theme settings, ...) is the easiest way to setup your theme.', 'keenshot' ); ?>
				<?php esc_html_e( 'It will allow you to quickly edit everything instead of creating content from scratch.', 'keenshot' ); ?>
            </p>
        </div>
		<?php
		return ob_get_clean();
	}

	function remove_ocdi_menu( $args ) {
		$args['parent_slug'] = '';
		$args['menu_slug']   = 'keenshot-setup';

		return $args;
	}

}

new Keen_Theme_Setup_Wizard();