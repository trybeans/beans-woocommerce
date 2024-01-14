<?php

namespace BeansWoo\Server;

use Beans\BeansError;
use BeansWoo\Helper;

/**
 * Add a custom REST resource on WP REST API.
 *
 * @since 3.4.0
 */
class ConnectorRESTController extends \WP_REST_Controller
{
    // The `wc-` prefix helps to use the WooCommerce Rest authentication on our routes.
    protected $namespace = "wc-beans/v2";
    protected $resource = 'connector';

    /**
     * Register custom REST routes on WP REST API.
     *
     * @return void
     *
     * @since 3.4.0
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            "/{$this->resource}/current",
            array(
                array(
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => array($this, 'retrieve'),
                    'permission_callback' => array($this, 'check_permissions'),
                ),
                array(
                    'methods' => \WP_REST_Server::EDITABLE,
                    'callback' => array($this, 'update'),
                    'permission_callback' => array($this, 'check_permissions'),
                )
            )
        );
    }

    /**
     * Retrieve the connector object.
     *
     * @param \WP_REST_Request $request
     * @return array the serialized connector object
     *
     * @since 3.4.0
     */
    public function retrieve($request)
    {
        $response = new \WP_REST_Response(self::serialize());
        $response->set_status(200);
        return $response;
    }

    /**
     * Update the connector object.
     *
     * @param \WP_REST_Request $request
     * @return array the serialized connector object
     *
     * @since 3.4.0
     */
    public function update($request)
    {
        foreach ($request->get_json_params() as $key => $value) {
            Helper::setConfig($key, $value);
        }

        Helper::clearTransients();

        $response = new \WP_REST_Response(self::serialize());
        $response->set_status(202);
        return $response;
    }

    /**
     * Serialize the connector object.
     *
     * @return array the serialized connector object
     *
     * @since 3.4.0
     */
    private static function serialize()
    {
        return array(
            'card' => Helper::getConfig('card'),
            'merchant' => Helper::getConfig('merchant'),
            'riper_version' => Helper::getConfig('riper_version'),
            'is_setup' => Helper::isSetup(),
            'pages' => Helper::getBeansPages(),
        );
    }

    /**
     * Define if the request_use has permission to access the resource.
     *
     * @param \WP_REST_Request $request
     * @return bool True if user has permission
     *
     * @since 3.4.0
     */
    public function check_permissions($request)
    {
        return true;
        // Make GET request public
        if ($request->get_method() === 'GET') {
            return true;
        }

        if (!current_user_can('manage_woocommerce')) {
            return new \WP_Error(
                "beans_rest_cannot_write",
                "Sorry, you are not allowed to update this resource.",
                array('status' => rest_authorization_required_code())
            );
        }
        return true;
    }
}
