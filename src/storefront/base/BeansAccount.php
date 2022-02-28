<?php

namespace BeansWoo\StoreFront;

use Beans\BeansError;
use BeansWoo\Helper;

class BeansAccount
{

    public static function create($email, $firstname, $lastname)
    {
        $card = Helper::requestTransientAPI('GET', 'ultimate/card/current');

        if (isset($card['is_active']) and $card['is_active']) {
            Helper::log("Unable to create account: Ultimate card is not active.");
            return null;
        }

        $apps = array();
        foreach (["liana", "bamboo", "foxx"] as $app) {
            if (isset($card['apps'][$app]) and $card['apps'][$app]['is_active']) {
                $apps[] = $app;
            }
        }

        try {
            $account = Helper::API()->post(
                'ultimate/account',
                array(
                    'email'      => $email,
                    'first_name' => $firstname,
                    'last_name'  => $lastname,
                    'apps'       => $apps,
                )
            );
        } catch (BeansError $e) {
            Helper::log('Unable to create account: ' . $e->getMessage());

            return null;
        }
        $_SESSION['beans_account'] = $account;

        self::setToken($account);

        return $account;
    }

    public static function update()
    {
        if (empty($_SESSION['beans_account'])) {
            return null;
        }

        $account = $_SESSION['beans_account'];

        try {
            $account = Helper::API()->get('ultimate/account/' . $account['id']);
        } catch (BeansError $e) {
            Helper::log('Unable to get account: ' . $e->getMessage());
            unset($_SESSION['beans_account']);

            return null;
        }

        $_SESSION['beans_account'] = $account;

        return $account;
    }

    public static function get()
    {
        if (empty($_SESSION['beans_account'])) {
            return null;
        }

        return $_SESSION['beans_account'];
    }

    public static function clear()
    {
        unset($_SESSION['beans_token']);
        unset($_SESSION['beans_account']);
    }

    /* Account Token */

    private static function setToken($account)
    {
        try {
            $token = Helper::API()->post(
                'ultimate/auth/consumer_token',
                array('account' => $account['id'])
            );
        } catch (BeansError $e) {
            Helper::log('Getting Auth Token Failed: ' . $e->getMessage());
            unset($_SESSION['beans_token']);

            return;
        }
        $_SESSION['beans_token'] = $token;
    }

    public static function getToken()
    {
        if (empty($_SESSION['beans_token'])) {
            return null;
        }

        return $_SESSION['beans_token'];
    }
}
