<?php


namespace BeansWoo\Front;

use BeansWoo\Helper;

class Base
{
    public static function init()
    {
        add_filter('wp_head',                 array(__CLASS__, 'base_common_config'));
        add_filter('woocommerce_registration_errors', array(__CLASS__, 'register_validate_name_fields'), 10, 3);
        add_action('woocommerce_register_form_start', array(__CLASS__, 'render_register'));
        add_action('woocommerce_created_customer', array(__CLASS__, 'register_save_name_fields'));

        add_filter('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'), 10, 1);

        if (current_user_can('administrator') and is_null(Helper::getConfig('is_admin_account')) and
            Helper::isSetupApp('liana')) {
            do_action('woocommerce_new_customer', get_current_user_id());
            Helper::setConfig('is_admin_account', true);
        }

    }


    public static function register_validate_name_fields($errors, $username, $email)
    {
        if (isset($_POST['first_name']) && empty($_POST['first_name'])) {
            $errors->add('first_name_error', __('<strong>Error</strong>: First name is required!', 'woocommerce'));
        }
        if (isset($_POST['last_name']) && empty($_POST['last_name'])) {
            $errors->add('last_name_error', __('<strong>Error</strong>: Last name is required!.', 'woocommerce'));
        }
        return $errors;
    }


    public static function register_save_name_fields($customer_id)
    {
        if (isset($_POST['first_name'])) {
            update_user_meta($customer_id, 'billing_first_name', sanitize_text_field($_POST['first_name']));
            update_user_meta($customer_id, 'first_name', sanitize_text_field($_POST['first_name']));
        }
        if (isset($_POST['last_name'])) {
            update_user_meta($customer_id, 'billing_last_name', sanitize_text_field($_POST['last_name']));
            update_user_meta($customer_id, 'last_name', sanitize_text_field($_POST['last_name']));
        }

    }

    public static function render_register()
    {
        ?>
        <p class="form-row form-row-first">
            <label for="reg_first_name"><?php _e('First name', 'woocommerce'); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="first_name" id="reg_first_name"
                   value="<?php if (!empty($_POST['first_name'])) esc_attr_e($_POST['first_name']); ?>"/>
        </p>

        <p class="form-row form-row-last">
            <label for="reg_last_name"><?php _e('Last name', 'woocommerce'); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="last_name" id="reg_last_name"
                   value="<?php if (!empty($_POST['last_name'])) esc_attr_e($_POST['last_name']); ?>"/>
        </p>
        <?php
    }

    public static function enqueue_scripts()
    {
        wp_enqueue_style('beans-style', plugins_url('assets/css/beans.css', BEANS_PLUGIN_FILENAME));
    }

    public static function base_common_config()
    {
        ?>
        <script>
            window.beans_cjs_id = "<?php echo is_user_logged_in() ? wp_get_current_user()->ID : ''; ?>";
            window.beans_cjs_email = "<?php echo is_user_logged_in() ? wp_get_current_user()->user_email : ''; ?>";
            window.beans_currentPage = "<?php echo Helper::getCurrentPage(); ?>";
            window.beans_loginPage = "<?php echo str_replace(home_url(), '', get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>";
            window.beans_registerPage = "<?php echo str_replace(home_url(), '', get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>";
        </script>
        <?php
    }
}