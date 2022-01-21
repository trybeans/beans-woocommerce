<?php

namespace BeansWoo\Server;

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
            '/' . $this->rest_base,
            array(
                array(
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => array(__CLASS__, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
                    'args' => $this->get_args(\WP_REST_Server::READABLE)
                ),
                array(
                    'methods' => \WP_REST_Server::EDITABLE,
                    'callback' => array(__CLASS__, 'update_item'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                    'args' => $this->get_args(\WP_REST_Server::EDITABLE)
                )
            )
        );
    }

    public function get_item($request)
    {
        $response = new \WP_REST_Response(self::get_item_data());
        $response->set_status(200);
        return $response;
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
