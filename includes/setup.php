<?php
namespace BeansWoo;

class Setup
{
    const REWARD_PROGRAM_PAGE = 'beans_page_id';

    static $errors = array();
    static $messages = array();

    public static function init(){
//            register_activation_hook( __FILE__,                                     array('BeansWoo\Setup', 'install'));
//            add_action( 'plugin_loaded',                                            array('BeansWoo\Setup', 'install'));
        add_action( 'admin_notices',                                            array(__CLASS__, 'admin_notice'));
        add_action( 'admin_menu',                                               array(__CLASS__, 'admin_menu'),     100);
        add_filter( 'plugin_row_meta',                                          array(__CLASS__, 'plugin_row_meta' ), 10, 2 );
    }

    public static function plugin_row_meta( $links, $file ) {
        if ( $file == BEANS_PLUGIN_FILE ) {
            $row_meta = array(
                'help'      => '<a href="http://help.trybeans.com/" title="Help">Get help</a>',
                'api'       => '<a href="http://business.trybeans.com/doc/api/" title="Help">API doc</a>',
            );

            return array_merge( $links, $row_meta );
        }

        return (array) $links;
    }

    public static function admin_menu()
    {
        if (current_user_can('manage_woocommerce'))
            add_submenu_page('woocommerce', 'Beans',
                             'Beans' , 'manage_woocommerce',
                             'beans-woo', array('\BeansWoo\Setup', 'render_settings_page' ) );
    }

    public static function admin_notice() {

        if(!Helper::isConfigured()){

            echo '<div class="error" style="margin-left: auto"><div style="margin: 10px auto;"> Beans: '.
                 __( 'Beans is not properly configured.', 'beans-woo' ) .
                 ' <a href="' . admin_url( 'admin.php?page=beans-woo' ) . '">' .
                 __( 'Set up', 'beans-woo' ) .
                 '</a>'.
                 '</div></div>';

        }

        if ( 'yes' !== get_option( 'woocommerce_api_enabled' ) ) {

            echo '<div class="error" style="margin-left: auto"><div style="margin: 10px auto;"> Beans: ' .
                 __('Beans cannot work if Woocommerce API is disabled.', 'beans-woo') .
                 ' <a href="' . admin_url('admin.php?page=wc-settings&tab=api') . '">' .
                 __('Enable API', 'beans-woo') .
                 '</a>' .
                 '</div></div>';
        }
//        if ( !Helper::log('|') ) {
//
//            echo '<div class="error" style="margin-left: auto"><div style="margin: 10px auto;"> Beans: '.
//                 __( 'Beans is unable to log error trace.', 'beans-woo' ) .
//                 '</div></div>';
//        }
    }

    public static function render_settings_page()
    {

        if(isset($_GET['reset_beans'])){
            if(Helper::resetSetup()){
                return wp_redirect(admin_url( 'admin.php?page=beans-woo' ));
            }
        }

        Helper::synchronise();

        if(isset($_GET['card']) && isset($_GET['token'])){
            if(self::_processSetup()){
                return wp_redirect(admin_url( 'admin.php?page=beans-woo' ));
            }
        }

        if (self::$errors || self::$messages){
            ?>
            <div class="<?php if(self::$errors): echo "error"; elseif (self::$messages): echo "updated"; endif;?> ">
                <?php if (self::$errors) : ?>
                    <ul>
                        <?php  foreach(self::$errors as $error) echo "<li>$error</li>"; ?>
                    </ul>
                <?php else : ?>
                    <ul>
                        <?php  foreach(self::$messages as $message) echo "<li>$message</li>"; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php
        }

        if(Helper::isConfigured()){
            return self::_getSetupDoneHTML();
        }

        return self::_getSetupPendingHTML();
    }

    private static function _getSetupPendingHTML(){

        $domain = Helper::getDomain('BUSINESS');
        $admin = wp_get_current_user();
        $country_code = get_option('woocommerce_default_country');

        if($country_code && strpos($country_code, ':') !== false){
            try {
                $country_parts = explode( ':', $country_code );
                $country_code = $country_parts[0];
            }catch(\Exception $e){}
        }

        $params = array(
            'redirect' => admin_url( 'admin.php?page=beans-woo' ),
            'email' => $admin->user_email,
            'last_name' => $admin->user_lastname,
            'first_name' => $admin->user_firstname,
            'website' => get_site_url(),
            'currency' => strtoupper(get_woocommerce_currency()),
            'company_name' => get_bloginfo('name'),
            'country' => $country_code,
        );

        $query_string = http_build_query($params);
        $action = "/integrations/woocommerce/authorize/?$query_string";

        ?>
            <div style="background-color: white; padding: 10px">
                <h3>Getting started with Beans</h3>
                <p>
                    Beans allows you to easily reward your customers for their loyalty, referrals and things
                    that matters to your business. Everything is automated. All you need to do is set how you want to reward your customers and then sit back.
                    Last year, retailers using Beans have increased their revenue by 40% in average.
                    <a href="//<?php echo $domain; ?>" target="_blank">Learn more</a>
                </p>
                <p>
                    This is a Beans private beta. Contact us on Twitter to request an invitation to join our private beta.
                </p>
                <p>
                    <a href="https://twitter.com/BeansHQ" target="_blank" class="button">
                        Request access
                    </a>
                </p>
                <p>
                    Happy incentivizing!
                </p>
                <p>
                    <a href="//<?php echo $domain.$action; ?>" class="button button-primary">
                        Connect to Beans
                    </a>
                </p>
            </div>
        <?php

        return true;
    }

    private static function _getSetupDoneHTML(){
        $domain = Helper::getDomain('BUSINESS');
        $card = Helper::getCard();
        $card_address = '?';
        if(isset($card['address'])) $card_address = $card['address'];
        ?>
        <div style="padding: 10px">
            <h3>Beans</h3>
            <div>
              Your Beans account "<?php echo $card_address; ?>" is successfully connected.
              <a target='_blank' href='//<?php echo $domain; ?>'>Update settings</a>.
            </div>
            <div style='margin: 20px auto'>
            </div>
        </div>
        <?php
        return true;
    }

    private static function _processSetup(){

        self::_installAssets();

        $card = $_GET['card'];
        $token = $_GET['token'];

        Helper::$key = $card;

        try{
            $integration_key = Helper::API()->get('integration_key/'.$token);
            if($integration_key['card']['version'] < 2){
                Helper::resetSetup();
                self::$errors[] = 'Please upgrade your rewards program to Beans 2.0';
                return null;
            }
        }catch(\Beans\Error\BaseError  $e){
            self::$errors[] = 'Connecting to Beans failed with message: '.$e->getMessage();
            Helper::log('Connecting failed: '.$e->getMessage());
            return null;
        }

        Helper::setConfig('key', $integration_key['id']);
        Helper::setConfig('card', $integration_key['card']['id']);
        Helper::setConfig('secret', $integration_key['secret']);

        return true;
    }

    private static function _installAssets()
    {
        // Install Reward Program Page
        if(!get_post(Helper::getConfig('page'))){
            require_once(WP_PLUGIN_DIR . '/woocommerce/includes/admin/wc-admin-functions.php');
            $page_id = wc_create_page('beans', self::REWARD_PROGRAM_PAGE, 'Beans', '[beans_page]', 0);
            Helper::setConfig('page', $page_id);
        }
    }
}