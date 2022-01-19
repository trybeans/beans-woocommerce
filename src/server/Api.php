<?php

namespace BeansWoo\Server;

use BeansWoo\Helper;

class ConnectorRESTController extends \WP_REST_Controller
{
    // The `wc-` prefix is to help to use the WooCommerce Rest authentication on our routes.
    protected $namespace = "wc-beans/v1";
    protected $rest_base = 'connector';

    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/riper_version',
            array(
                'methods' => 'POST',
                'callback' => array(__CLASS__, 'update_item_riper_version'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
                'args' => array(
                    'riper_version' => array(
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_string($param) and in_array($param, array('lts', 'edge'));
                        },
                        'sanitize_callback' => function ($param, $request, $key) {
                            return htmlspecialchars($param);
                        },
                    )
                )
            )
        );
    }

    public static function update_item_riper_version($request)
    {
        Helper::setConfig('riper_version', $request['riper_version']);

        $data = array(
            'result' =>  true,
        );
        $response = new \WP_REST_Response($data);
        $response->set_status(202);
        return $response;
    }

    public function update_item_permissions_check($request)
    {

        if (!current_user_can('manage_options')) {
            return new \WP_Error(
                'beans_rest_cannot_edit',
                __('Sorry, you are not allowed to edit this resource.', 'woocommerce'),
                array('status' => rest_authorization_required_code())
            );
        }
        return true;
    }
}
