<?php

namespace BeansWoo\Front\Bamboo;

use BeansWoo\Helper;

class Block {

    public static $app_name = 'bamboo';
    static $card;

    public static function init(){
	    self::$card = Helper::getCard( self::$app_name );
	    if ( empty( self::$card ) || ! self::$card['is_active'] || !Helper::isSetupApp(self::$app_name)) {
		    return;
	    }

        add_filter('wp_head',                                       array(__CLASS__, 'render_head'),     10, 1);
        add_filter('the_content',                                   array(__CLASS__, 'render_page'),     10, 1);

        add_filter('wp_footer',                                     array(__CLASS__, 'render_init'),     10, 1);

    }

    public static function render_head(){
        /* Issue with wp_enqueue_script not always loading, preferred using wp_head for a quick fix
           Also the Beans script does not have any dependency so there is no that much drawback on using wp_head
        */

        ?>
        <script src= 'https://<?php echo Helper::getDomain("STATIC"); ?>/lib/bamboo/3.1/js/bamboo.beans.js?radix=woocommerce&id=<?php echo self::$card['id'];  ?>' type="text/javascript"></script>
        <?php
    }

    public static function render_init($force=false){
        if (!$force && get_the_ID() === Helper::getConfig(static::$app_name. '_page')) return;
        $token = array();
        $debit = array();

        if (is_user_logged_in() and !isset($_SESSION[static::$app_name . "_account"])){
            Observer::customerRegister(get_current_user_id());
        }

        if(isset($_SESSION[static::$app_name . '_token'])) $token = $_SESSION[static::$app_name . '_token'];
        if(isset($_SESSION[static::$app_name . '_debit'])) $debit = $_SESSION[static::$app_name . '_debit'];

        ?>
        <script>
            window.Beans3.Bamboo.Shopify.opt = {
                current_page: "<?php echo Helper::getCurrentPage(); ?>",
                login_page: "<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>",
                register_page: "<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>",
                reward_page: "<?php echo get_permalink( Helper::getConfig('liana_page') ); ?>",
                referral_page: "<?php echo get_permalink( Helper::getConfig(static::$app_name . '_page') ); ?>"
            };
            //window.Beans3.Bamboo.Account = {
            //    _update: function (resolve, reject) {
            //        window.Beans3
            //            .get('bamboo/account/current')
            //            .then(function (account) {
            //                console.log(account);
            //                resolve(account);
            //                // if (!account.referral && Bamboo.Referral._br) {
            //                //     window.Beans3.Bamboo.Referral.commit(account);
            //                // }
            //                // window.Beans3.Bamboo.Coupon.refresh();
            //                window.Beans3.setAccountID(account.id);
            //            })
            //            .catch(reject);
            //    },
            //    authenticate: function () {
            //        return new Promise(function (resolve, reject) {
            //            const accountToken = "<?php // echo isset($token['key'])? $token['key'] : ''; ?>//";
            //            if(accountToken){
            //
            //                window.Beans3.setAccountToken(accountToken);
            //                // window.Beans3.Widget.showMessageDefault(true);
            //                return window.Beans3.Bamboo.Account._update(resolve, reject);
            //            }
            //            resolve();
            //        })
            //    },
            //};
            window.Beans3.Bamboo.init();
        </script>
        <?php

    }

    public static function render_page($content, $vars=null){
        if (strpos($content,'[beans_bamboo_page]') !== false and !is_null(Helper::getConfig(static::$app_name. '_page')) ) {
            ob_start();
            include(dirname(__FILE__) . '/html-page.php');
            self::render_init(true);
            $page = ob_get_clean();
            $content = str_replace('[beans_bamboo_page]', $page, $content);
        }
        return $content;
    }

    public static function render_register(){
       ?>
        <p class="form-row form-row-first">
            <label for="reg_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="first_name" id="reg_first_name"
                   value="<?php if ( ! empty( $_POST['first_name'] ) ) esc_attr_e( $_POST['first_name'] ); ?>" />
        </p>

        <p class="form-row form-row-last">
            <label for="reg_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="last_name" id="reg_last_name"
                   value="<?php if ( ! empty( $_POST['last_name'] ) ) esc_attr_e( $_POST['last_name'] ); ?>" />
        </p>
        <?php
    }
}