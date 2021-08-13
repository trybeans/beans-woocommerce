<?php

namespace BeansWoo\StoreFront;

class Registration
{
    public static function init()
    {
        add_action('woocommerce_register_form_start', array(__CLASS__, 'renderRegister'));
        add_action('woocommerce_created_customer', array(__CLASS__, 'registerSaveNameFields'));
        add_filter('woocommerce_registration_errors', array(__CLASS__, 'registerValidateNameFields'), 10, 3);
    }

    public static function registerValidateNameFields($errors, $username, $email)
    {
        if (isset($_POST['first_name']) && empty($_POST['first_name'])) {
            $errors->add('first_name_error', __('<strong>Error</strong>: First name is required!', 'woocommerce'));
        }
        if (isset($_POST['last_name']) && empty($_POST['last_name'])) {
            $errors->add('last_name_error', __('<strong>Error</strong>: Last name is required!.', 'woocommerce'));
        }
        return $errors;
    }


    public static function registerSaveNameFields($customer_id)
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

    public static function renderRegister()
    {
        ?>
        <p class="form-row form-row-first">
            <label for="reg_first_name"><?php _e('First name', 'woocommerce'); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="first_name" id="reg_first_name"
                   value="<?php if (! empty($_POST['first_name'])) {
                        esc_attr_e($_POST['first_name']);
                          } ?>" />
        </p>

        <p class="form-row form-row-last">
            <label for="reg_last_name"><?php _e('Last name', 'woocommerce'); ?><span class="required">*</span></label>
            <input type="text" class="input-text" name="last_name" id="reg_last_name"
                   value="<?php if (! empty($_POST['last_name'])) {
                        esc_attr_e($_POST['last_name']);
                          } ?>" />
        </p>
        <?php
    }
}
