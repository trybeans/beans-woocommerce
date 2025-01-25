<?php

namespace BeansWoo\StoreFront;

use Beans\BeansError;
use BeansWoo\Helper;

/**
 * Beans account class.
 *
 * @class BeansAccount
 * @since 3.0.0
 */
class BeansAccount
{
    /**
     * Create a Beans account for a customer and
     * (optionally) save it in session.
     *
     * @param string $email The email of the customer
     * @param string $first_name The first name of the customer
     * @param string $last_name The last name of the customer
     * @param bool $use_session If true, save the data in PHP $_SESSION
     *
     * @return array|null a Beans member's account object: https://api.trybeans.com/v3/doc/#tag/Account
     *
     * @since 3.3.0
     */
    public static function create($email, $first_name, $last_name, $use_session = false)
    {
        try {
            $account = Helper::API()->post(
                'ultimate/account',
                array(
                    'email'      => $email,
                    'first_name' => $first_name,
                    'last_name'  => $last_name,
                    'apps'       => ["liana", "bamboo", "foxx"],
                )
            );
        } catch (BeansError $e) {
            Helper::log('Unable to create account: ' . $e->getMessage());

            return null;
        }
        if ($use_session) {
            $_SESSION['beans_account'] = $account;
            self::setSessionToken($account);
        }

        return $account;
    }

    /**
     * Retrieve the Beans account associated to a customer and
     * (optionally) save it in session.
     *
     * @param string $email_or_id The email or Beans AccountID of the customer
     * @param bool $use_session If true, save the data in PHP $_SESSION
     *
     * @return array|null a Beans member's account object: https://api.trybeans.com/v3/doc/#tag/Account
     *
     * @since 3.5.0
     */
    public static function retrieve($email_or_id, $use_session = false)
    {
        try {
            $account = Helper::API()->get("ultimate/account/{$email_or_id}/");
        } catch (BeansError $e) {
            Helper::log('Unable to retrieve account: ' . $e->getMessage());

            if ($use_session) {
                unset($_SESSION['beans_account']);
            }

            return null;
        }

        if ($use_session) {
            $_SESSION['beans_account'] = $account;
            if (!isset($_SESSION['beans_token'])) {
                self::setSessionToken($account);
            }
        }

        return $account;
    }

    /* Active User Session */

    /**
     * Refresh the active user's Beans account saved in session.
     *
     * @return array|null a Beans member's account object: https://api.trybeans.com/v3/doc/#tag/Account
     *
     * @since 3.3.0
     */
    public static function refreshSession()
    {
        if (empty($_SESSION['beans_account'])) {
            return null;
        }

        return self::retrieve(self::getSessionAttribute('id'), true);
    }

    /**
     * Retrieve the active user's Beans account saved in session.
     *
     * @return array|null a Beans member's account object: https://api.trybeans.com/v3/doc/#tag/Account
     *
     * @since 3.3.0
     */
    public static function getSession()
    {
        if (empty($_SESSION['beans_account'])) {
            return null;
        }

        return $_SESSION['beans_account'];
    }

    /**
     * Get an attribute of the active user's Beans account stored in session.
     *
     * @param string $key The key of the attribute.
     *
     * @return mixed
     *
     * @since 3.5.0
     */
    public static function getSessionAttribute($key)
    {
        $account = BeansAccount::getSession();

        if (!$account) {
            return null;
        }

        if (isset($account['liana'][$key])) {
            return $account['liana'][$key];
        }

        if (isset($account[$key])) {
            return $account[$key];
        }

        return null;
    }

    /**
     * Clear the active user's Beans account saved in session.
     *
     * @return void
     *
     * @since 3.3.0
     */
    public static function clearSession()
    {
        unset($_SESSION['beans_token']);
        unset($_SESSION['beans_account']);
    }

    /* Account Token */

    /**
     * Create a beans authentication token for the active customer.
     * This will be used by the JS script to connect to the Beans API and retrieve
     * info relative to the customer's account.
     *
     * @return void
     *
     * @since 3.3.0
     */
    private static function setSessionToken($account)
    {
        try {
            $token = Helper::API()->post('ultimate/auth/consumer_token', array('account' => $account['id']));
        } catch (BeansError $e) {
            Helper::log('Getting Auth Token Failed: ' . $e->getMessage());
            unset($_SESSION['beans_token']);

            return;
        }
        $_SESSION['beans_token'] = $token;
    }

    /**
     * Retrieve a beans authentication token for the active customer.
     * This will be used by the JS script to connect to the Beans API and retrieve
     * info relative to the customer's account.
     *
     * @return array|null
     *
     * @since 3.3.0
     */
    public static function getSessionToken()
    {
        if (empty($_SESSION['beans_token'])) {
            return null;
        }

        return $_SESSION['beans_token'];
    }
}
