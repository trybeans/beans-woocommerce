<?php

namespace BeansWoo\StoreFront;

defined('ABSPATH') or die;

require_once "base/BeansAccount.php";
require_once "base/Auth.php";
require_once "base/Scripts.php";
require_once "base/Registration.php";

require_once "liana/Observer/LianaObserver.php";
require_once "liana/Observer/LianaCartObserver.php";
require_once "liana/Observer/LianaProductObserver.php";
require_once "liana/Observer/LianaSubscriptionObserver.php";

require_once "liana/Views/LianaBlocks.php";
require_once "liana/Views/LianaPage.php";
require_once "bamboo/Views/BambooBlocks.php";
require_once "bamboo/Views/BambooPage.php";

use BeansWoo\Helper;
use BeansWoo\Server\ReviewHookController;


class Main
{
    public static function init()
    {
        Registration::init();
        ReviewHookController::init();

        if (!Helper::isSetup()) {
            return;
        }

        if (! session_id()) {
            session_start();
        }

        if (isset($_GET['beans-mode'])) {
            $_SESSION['beans_mode'] = $_GET['beans-mode'];
        }

        if (isset($_SESSION['beans_mode'])) {
            Helper::log('Session: Beans mode live testing is active.');
        }

        add_action('wp_loaded', array(__CLASS__, 'routeActions'), 30, 1);

        Scripts::init();

        $display = Helper::getDisplay();

        BambooPage::init($display);
        LianaPage::init($display);

        if (empty($display)) {
            Helper::log('Init: Display Liana is empty');
            return;
        }

        Auth::init($display);

        // Either the rewards program is active, or we are in live test mode
        if (!$display['is_active'] && !isset($_SESSION['beans_mode'])) {
            Helper::log('Init: Display Liana is deactivated');
            return;
        }

        BambooBlocks::init();
        LianaBlocks::init($display);

        LianaCartObserver::init($display);
        LianaSubscriptionObserver::init($display);
        // LianaProductObserver::init($display);
    }

    public static function initAjax()
    {
        if (!Helper::isSetup()) {
            return;
        }

        if (! session_id()) {
            session_start();
        }

        $display = Helper::getDisplay();

        if (empty($display)) {
            Helper::log('Ajax: Display Liana is empty');
            return;
        }

        // Either the rewards program is active, or we are in live test mode
        if (!$display['is_active'] && !isset($_SESSION['beans_mode'])) {
            Helper::log('Ajax: Display Liana is deactivated');
            return;
        }

        LianaCartObserver::init($display);
        LianaProductObserver::init($display);
    }

    /**
     * A router that matches the request action to the right method.
     * Actions are submitted through a post request and this method acts
     * as an entrypoint that will re-route the request to right method.
     *
     * `POST store.com/cart?beans_action=redeem-lifetime
     *
     * @return void
     *
     * @since 3.5.0
     */
    public static function routeActions()
    {
        if (!isset($_POST['beans_action'])) {
            return;
        }

        $action = $_POST['beans_action'];

        // TODO: delete, only used for backward compatibility with Riper
        if ($action == 'apply' and isset($_POST['tier_id'])) {
            $action = 'redeem-lifetime';
        } elseif ($action == 'apply') {
            $action = 'redeem-cart';
        } elseif ($action == 'cancel') {
            $action = 'cancel-redeem-cart';
        }

        switch ($action) {
            case 'redeem-cart':
                LianaCartObserver::applyCartRedemption();
                break;
            case 'redeem-lifetime':
                LianaCartObserver::applyLifetimeRedemption();
                break;
            case 'redeem-subscription':
                LianaSubscriptionObserver::applySubscriptionRedemption();
                break;
            case 'cancel-redeem-subscription':
                LianaSubscriptionObserver::cancelSubscriptionRedemption();
                break;
            case 'cancel-redeem':
            case 'cancel-redeem-cart':
            case 'cancel-redeem-product':
            case 'cancel-redeem-lifetime':
                LianaObserver::cancelRedemption();
                break;
            case 'manual-registration':
                Auth::onManualRegister();
                break;
        }
    }
}
