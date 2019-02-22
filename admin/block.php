<?php

namespace BeansWoo\Admin;

use BeansWoo\Helper;

class Block {
    const REWARD_PROGRAM_PAGE = 'beans_page_id';

    static $errors = array();
    static $messages = array();

    public static function init() {

    }

    public static function render_settings_page() {


        if ( isset( $_GET['card'] ) && isset( $_GET['token'] ) ) {
            if ( self::_processSetup() ) {
                return wp_redirect( admin_url( 'admin.php?page=beans-woo' ) );
            }
        }

        if ( isset( $_GET['reset_beans'] ) ) {
            if ( Helper::resetSetup() ) {
                return wp_redirect( admin_url( 'admin.php?page=beans-woo' ) );
            }
        }

        self::_render_notices();

        if ( Helper::isSetup() ) {
            return include( dirname( __FILE__ ) . '/block.info.php' );
        }

        return include( dirname( __FILE__ ) . '/block.connect.php' );
    }

    private static function _render_notices() {
        if ( self::$errors || self::$messages ) {
            ?>
          <div class="<?php echo empty(self::$errors)? "updated" : "error"; ?> ">
              <?php if ( self::$errors ) : ?>
                <ul>
                    <?php foreach ( self::$errors as $error ) {
                        echo "<li>$error</li>";
                    } ?>
                </ul>
              <?php else : ?>
                <ul>
                    <?php foreach ( self::$messages as $message ) {
                        echo "<li>$message</li>";
                    } ?>
                </ul>
              <?php endif; ?>
          </div>
            <?php
        }
    }

    private static function _processSetup() {

        self::_installAssets();

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

        return true;
    }

    private static function _installAssets() {
        // Install Reward Program Page
        if ( ! get_post( Helper::getConfig( 'page' ) ) ) {
            require_once( WP_PLUGIN_DIR . '/woocommerce/includes/admin/wc-admin-functions.php' );
            $page_id = wc_create_page( 'rewards-program', self::REWARD_PROGRAM_PAGE, 'Rewards Program', '[beans_page]', 0 );
            Helper::setConfig( 'page', $page_id );
        }
    }
}