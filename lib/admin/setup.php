<?php
namespace BeansWoo;

class Setup {
    const REWARD_PROGRAM_PAGE = 'beans_page_id';

    static $errors = array();
    static $messages = array();

    public static function init() {
        add_action( 'admin_notices', array( __CLASS__, 'admin_notice' ) );
        add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ), 100 );
        add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
    }

    public static function render_settings_page() {

        return include( dirname( __FILE__ ) . '/block.connect.php' );

        if ( isset( $_GET['reset_beans'] ) ) {
            if ( Helper::resetSetup() ) {
                return wp_redirect( admin_url( 'admin.php?page=beans-woo' ) );
            }
        }

        if ( isset( $_GET['card'] ) && isset( $_GET['token'] ) ) {
            if ( self::_processSetup() ) {
                return wp_redirect( admin_url( 'admin.php?page=beans-woo' ) );
            }
        }

        if ( self::$errors || self::$messages ) {
            ?>
          <div class="<?php if ( self::$errors ): echo "error";
          elseif ( self::$messages ): echo "updated"; endif; ?> ">
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

        if ( Helper::isConfigured() ) {
            return self::_getSetupDoneHTML();
        }

        return include( dirname( __FILE__ ) . '/block.connect.php' );
    }

    private static function _getSetupDoneHTML() {
        $domain       = Helper::getDomain( 'BUSINESS' );
        $card         = Helper::getCard();
        $card_address = '?';
        if ( isset( $card['address'] ) ) {
            $card_address = $card['address'];
        }
        ?>
      <div style="padding: 10px">
        <h3>Beans</h3>
        <div>
          Your Beans account "<?php echo $card_address; ?>" is successfully connected.
          <a target='_blank' href='//<?php echo $domain; ?>'>Update settings</a>.
        </div>
        <div style='margin: 20px auto'>
          <a href='<?php echo admin_url( 'admin.php?page=beans-woo&reset_beans=1' ); ?>'>Reset</a>
        </div>
      </div>
        <?php
        return true;
    }

    private static function _processSetup() {

        self::_installAssets();

        Helper::log( print_r( $_GET, true ) );

        $card  = $_GET['card'];
        $token = $_GET['token'];

        Helper::$key = $card;

        try {
            $integration_key = Helper::API()->get( '/core/integration_key/' . $token );
        } catch ( \Beans\Error\BaseError  $e ) {
            self::$errors[] = 'Connecting to Beans failed with message: ' . $e->getMessage();
            Helper::log( 'Connecting failed: ' . $e->getMessage() );

            return null;
        }

        Helper::setConfig( 'key', $integration_key['id'] );
        Helper::setConfig( 'card', $integration_key['card']['id'] );
        Helper::setConfig( 'secret', $integration_key['secret'] );

        Helper::synchronise();

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