<?php

/**
 * List of settings params that can be configured by the merchant in
 * the WordPress dashboard.
 */

namespace BeansWoo;

const PREFERENCES_META = array(
  'redemption_min_beans' => array(
      'handle' => 'beans-liana-redemption-min-beans',
      'label' => 'Minimum points required',
      'type' => 'int',
      'default' => 0,
      'help_text' => (
          'Set the threshold of points a customer must have to start redeeming. ' .
          'If a customer has fewer points than this number, ' .
          'they will not be able to use their points for a discount.'
      ),
  ),
  'redemption_max_percentage' => array(
      'handle' => 'beans-liana-redemption-max-percentage',
      'label' => 'Maximum discount per redemption',
      'type' => 'int',
      'default' => 100,
      'help_text' => (
          'Specify the highest percentage of discount that ' .
          'can be applied to an order using points redemption.'
      ),
  ),
  'redemption_collections' => array(
      'handle' => 'beans-liana-redemption-collections',
      'label' => 'Redeemable collections',
      'type' => 'list',
      'default' => array(),
      'help_text' => (
          'Choose specific collections where points can be redeemed. ' .
          'If you prefer points to be redeemable across your entire catalog, leave this section empty.'
      ),
  ),
  'display_checkout_redeem' => array(
      'handle' => 'beans-liana-display-redemption-checkout',
      'label' => 'Redemption on checkout',
      'type' => 'boolean',
      'default' => true,
      'help_text' => 'Display redemption on checkout page.',
  ),
  'display_product_points' => array(
      'handle' => 'beans-liana-display-product-points',
      'label' => 'Product points',
      'type' => 'boolean',
      'default' => false,
      'help_text' => 'Display points info on product page.',
  ),
  'display_cart_notices' => array(
      'handle' => 'beans-liana-display-cart-notices',
      'label' => 'Cart notices',
      'type' => 'boolean',
      'default' => true,
      'help_text' => 'Display points balance notice on cart page.',
  ),
  'display_account_nav' => array(
      'handle' => 'beans-liana-display-account-navigation',
      'label' => 'Account navigation',
      'type' => 'boolean',
      'default' => true,
      'help_text' => 'Display links to rewards, referral pages in account navigation.',
  ),
  'enable_subscription_redemption' => array(
      'handle' => 'beans-liana-display-subscription-redemption',
      'label' => 'Subscription redemption',
      'type' => 'boolean',
      'default' => false,
      'help_text' => 'Allow customers to redeem points on their upcoming subscription renewal.',
  ),
  'is_auto_register' => array(
      'handle' => 'beans-liana-auto-registration',
      'label' => 'Auto registration',
      'type' => 'boolean',
      'default' => true,
      'help_text' => (
          'Check this box to enroll shoppers in the rewards program when they create a customer account.'
      ),
  ),
);

/**
 * A Helper to perform operations on Beans Preferences.
 */
class Preferences
{
    /**
     * retrieve a beans preference value
     *
     * @param string $key: The key of the preference
     *
     * @return any: value of the preference.
     * @since 4.0.0
     */
    public static function get($key)
    {
        return get_option(PREFERENCES_META[$key]['handle']);
    }

    /**
     * Set a beans preference value
     *
     * @param string $key: The key of the preference
     * @param any $value: The value of the preference
     *
     * @return void:
     * @since 4.0.0
     */
    public static function set($key, $value)
    {
        return update_option(PREFERENCES_META[$key]['handle'], $value);
    }

    /**
     * Clear all beans preferences
     *
     * @return void:
     * @since 4.0.0
     */
    public static function clearAll()
    {
        foreach (PREFERENCES_META as $key => $preference) {
            delete_option($preference['handle']);
        }
    }

    /**
     * Get all beans preferences
     *
     * @return array: the dictionary of preference_key => preference_value
     * @since 4.0.0
     */
    public static function getAll()
    {
        $result = array();
        foreach (PREFERENCES_META as $key => $preference) {
            $result[$key] = get_option($preference['handle']);
        }
        return $result;
    }
}
