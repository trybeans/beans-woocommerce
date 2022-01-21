<?php

namespace BeansWoo\Server;

use BeansWoo\Helper;
use BeansWoo\Admin\Connector;

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
                    'callback' => array($this, 'create_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
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
    public function create_item($request)
    {
        if (isset($request['card']) && isset($request['token'])) {
            $card_id = $request['card'];
            $token   = $request['token'];
            if (!Connector::processSetup($card_id, $token)) {
                return new \WP_Error(
                    "beans_rest_cannot_setup",
                    __("Unable to setup Beans plugin", 'beans'),
                    array('status' => 400)
                );
            }
            Connector::setupPages();
            Helper::clearTransients();
        }

        $response = new \WP_REST_Response(self::get_item_data());
        $response->set_status(201);
    }

    public function update_item($request)
    {
        if (isset($request['riper_version'])) {
            Helper::setConfig('riper_version', $request['riper_version']);
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
                    'required' => false,
                    'sanitize_callback' => array(__CLASS__, 'sanitize_value'),
                    'validate_callback' => function ($param, $request, $key) {
                        return is_string($param);
                    },
                ),
                'card' => array(
                    'required' => false,
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
        if (!current_user_can('manage_options')) {
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
