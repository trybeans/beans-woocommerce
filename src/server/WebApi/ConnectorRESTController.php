<?php

namespace BeansWoo\Server;

use BeansWoo\Helper;
use BeansWoo;

/**
 * Add the Connector REST resource on WP REST API.
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
                    'permission_callback' => array(Helper, 'checkAPIPermission'),
                ),
                array(
                    'methods' => \WP_REST_Server::EDITABLE,
                    'callback' => array($this, 'update'),
                    'permission_callback' => array(Helper, 'checkAPIPermission'),
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
        $options = array();
        foreach (BeansWoo\OPTIONS as $key => $params) {
            $options[$key] = array_merge($params, array(
                'key' => $key,
                'value' => get_option($params['handle'])
            ));
        }

        return array(
            'card' => Helper::getConfig('card'),
            'merchant' => Helper::getConfig('merchant'),
            'riper_version' => Helper::getConfig('riper_version'),
            'is_setup' => Helper::isSetup(),
            'pages' => Helper::getBeansPages(),
            'options' => $options
        );
    }
}
