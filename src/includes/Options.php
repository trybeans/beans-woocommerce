<?php

/**
 * List of settings params that can be configured by the merchant in
 * the WordPress dashboard.
 */

namespace BeansWoo;

const OPTIONS = array(
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
  'enable_manual_registration' => array(
      'handle' => 'beans-liana-manual-registration',
      'label' => 'Manual registration',
      'type' => 'boolean',
      'default' => false,
      'help_text' => (
          'Check this box to force new and existing customers to manually ' .
          'opt in the rewards program by submitting a form. ' .
          '(This is an experimental feature)'
      ),
  ),
);
