<?php


namespace BeansWoo\Admin\Connector;

defined('ABSPATH') or die;

use BeansWoo\Helper;

abstract class AbstractConnector {

	static public $errors = array();
	static public $messages = array();

    static public $app_name;
    static public $app_info;

    static public $has_install_asset = false;

	abstract public static function init();

	abstract protected static function _installAssets();

	public static function render_settings_page() {
        self::$app_info = Helper::getApps()[static::$app_name];

		if ( isset( $_GET['card'] ) && isset( $_GET['token'] ) ) {
			if ( static::_processSetup() ) {
				return wp_redirect( admin_url( static::$app_info['link'] ) );
			}
		}

		if ( isset( $_GET['reset_beans'] ) ) {
			if ( Helper::resetSetup(static::$app_name) ) {
				return wp_redirect( admin_url( static::$app_info['link'] ) );
			}
		}

		self::_render_notices();

		if (Helper::isSetup()){
		    if (static::$app_name == 'liana'){
		        $page = Helper::getConfig('page');
		        if ( ! is_null($page)){
		            Helper::setConfig(static::$app_name. '_page', $page);
		            Helper::setConfig('page', null);
                    Helper::setAppInstalled(static::$app_name);
		        }
            }
        }

		if ( Helper::isSetup() && Helper::isSetupApp(static::$app_name) ) {
			return include( dirname( __FILE__ , 2) . '/views/html-info.php' );
		}

		return include( dirname( __FILE__,2) . '/views/html-connect.php' );
	}


	protected static function _processSetup() {
        if (static::$has_install_asset){

	        static::_installAssets();
        }

		Helper::log( print_r( $_GET, true ) );

		$card  = $_GET['card'];
		$token = $_GET['token'];

		Helper::$key = $card;

		try {
			$integration_key = Helper::API()->get( '/core/auth/integration_key/' . $token );
		} catch ( \Beans\Error\BaseError  $e ) {
			self::$errors[] = 'Connecting to Beans failed with message: ' . $e->getMessage();
			Helper::log( 'Connecting failed: ' . $e->getMessage() );

			return null;
		}

		Helper::setConfig( 'key', $integration_key['id'] );
		Helper::setConfig( 'card', $integration_key['card']['id'] );
		Helper::setConfig( 'secret', $integration_key['secret'] );
		Helper::setAppInstalled(static::$app_name);
		return true;
	}

	public static function _render_notices() {
		if ( static::$errors || static::$messages ) {
			?>
			<div class="<?php echo empty(static::$errors)? "updated" : "error"; ?> ">
				<?php if ( static::$errors ) : ?>
					<ul>
						<?php foreach ( static::$errors as $error ) {
							echo "<li>$error</li>";
						} ?>
					</ul>
				<?php else : ?>
					<ul>
						<?php foreach ( static::$messages as $message ) {
							echo "<li>$message</li>";
						} ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php
		}
	}

    public static function admin_notice() {
        static::$app_info = Helper::getApps()[static::$app_name];
        $page = isset($_GET['page']) ? $_GET['page'] : null;

        if ($page && strpos($page, BEANS_WOO_BASE_MENU_SLUG ) !== false and
            (! Helper::isSetup() || ! Helper::isSetupApp(static::$app_name))) {
            echo '<div class="notice notice-error is-dismissible" style="margin-left: auto"><div style="margin: 10px auto;"> Beans: ' .
                __(  static::$app_info['name'] ." is not properly setup.", 'beans-woo' ) .
                ' <a href="' . admin_url(static::$app_info['link'] ) . '">' .
                __( 'Set up', 'beans-woo' ) .
                '</a>' .
                '</div></div>';
        }
    }

}