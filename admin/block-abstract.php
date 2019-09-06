<?php


namespace BeansWoo\Admin;

use BeansWoo\Helper;

abstract class BlockAbstract {

	static public $errors = array();
	static public $messages = array();

    static public $app_name;
	static public $app_info ;

    static public $has_install_asset = false;

	abstract public static function init();

	abstract protected static function _installAssets();

	public static function render_settings_page() {
		static::$app_info = Helper::getApps()[static::$app_name];

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

		if ( Helper::isSetup() && Helper::isSetupApp(static::$app_name)) {
			return include( dirname( __FILE__ ) . '/block.info.php' );
		}

		return include( dirname( __FILE__ ) . '/block.connect.php' );
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
}