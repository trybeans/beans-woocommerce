<?php

namespace BeansWoo\Server;

use Beans\BeansError;
use BeansWoo\Helper;

class ConnectorRESTController extends \WP_REST_Controller
{
    // The `wc-` prefix helps to use the WooCommerce Rest authentication on our routes.
    protected $namespace = "wc-beans/v1";
    protected $rest_base = 'connector';

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/current',
            array(
                array(
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                    'args' => $this->get_args(\WP_REST_Server::READABLE)
                ),
                array(
                    'methods' => \WP_REST_Server::EDITABLE,
                    'callback' => array($this, 'update_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                    'args' => $this->get_args(\WP_REST_Server::EDITABLE)
                )
            )
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/install',
            array(
                array(
                    'methods' => \WP_REST_Server::CREATABLE,
                    'callback' => array($this, 'install'),
                    'permission_callback' => array($this, 'install_item_permissions_check'),
                    'args' => $this->get_args(\WP_REST_Server::CREATABLE)
                ),
            )
        );
    }

    public function get_item($request)
    {
        $response = new \WP_REST_Response(self::get_item_data());
        $response->set_status(200);
        return $response;
    }
    public function install($request)
    {
        if (isset($request['card']) && isset($request['token'])) {
            $card_id = $request['card'];
            $token   = $request['token'];

            // Using `Connector::processSetup()` doesn't work. I had an error about the
            // `Class BeansWoo\Admin\Connector` doesn't exist. I tried to investigate but I am not able to find out
            // what is the bug.
            // todo; Use `Connector::processSetup()` instead of duplicating the logic;
            Helper::$key = $card_id;

            try {
                $integration_key = Helper::API()->get('core/auth/integration_key/' . $token);
            } catch (BeansError  $e) {
                Helper::log('Connecting failed: ' . $e->getMessage());
                return new \WP_Error(
                    "beans_rest_cannot_setup",
                    __("Unable to setup Beans plugin", 'beans'),
                    array('status' => 400)
                );
            }

            Helper::setConfig('card', $card_id);
            Helper::setConfig('key', $integration_key['id']);
            Helper::setConfig('secret', $integration_key['secret']);
            Helper::clearTransients();
        }

        $response = new \WP_REST_Response(self::get_item_data());
        $response->set_status(201);
        return $response;
    }

    public function update_item($request)
    {
        foreach (['riper_version'] as $field) {
            if (isset($request[$field])) {
                Helper::setConfig($field, $request[$field]);
            }
        }

        $response = new \WP_REST_Response(self::get_item_data());
        $response->set_status(202);
        return $response;
    }

    public function get_item_permissions_check($request)
    {
        return $this->check_permissions($request, 'view');
    }

    public function update_item_permissions_check($request)
    {
        return $this->check_permissions($request, 'edit');
    }

    public function install_item_permissions_check($request)
    {
        return $this->check_permissions($request, 'install');
    }

    public function get_args($action)
    {
        if ($action === \WP_REST_Server::EDITABLE) {
            return array(
                'riper_version' => array(
                    'required' => false,
                    'sanitize_callback' => array(__CLASS__, 'sanitize_value'),
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param) and in_array($param, array('lts', 'edge'));
                    },
                ),
            );
        } elseif ($action == \WP_REST_Server::CREATABLE) {
            return array(
                'token' => array(
                    'required' => true,
                    'sanitize_callback' => array(__CLASS__, 'sanitize_value'),
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    },
                ),
                'card' => array(
                    'required' => true,
                    'sanitize_callback' => array(__CLASS__, 'sanitize_value'),
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param) and substr($param, 0, strlen($key)) === $key;
                    },
                ),
            );
        }
        return array();
    }

    public static function sanitize_value($param, $request, $key)
    {
        return htmlspecialchars($param);
    }

    private function check_permissions($request, $action)
    {
        if (!current_user_can('manage_woocommerce')) {
            return new \WP_Error(
                "beans_rest_cannot_$action",
                __("Sorry, you are not allowed to $action this resource.", 'woocommerce'),
                array('status' => rest_authorization_required_code())
            );
        }
        return true;
    }

    public static function get_item_data()
    {
        return array(
            'card' => Helper::getConfig('card'),
            'is_setup' => Helper::isSetup(),
            'riper_version' => Helper::getConfig('riper_version'),
            'pages' => Helper::getBeansPages(),
        );
    }
}
