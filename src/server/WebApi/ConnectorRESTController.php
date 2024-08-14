<?php

namespace BeansWoo\Server;

use Beans\BeansError;
use BeansWoo\Helper;
use BeansWoo\Preferences;

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
                    'permission_callback' => array("BeansWoo\Helper", 'checkAPIPermission'),
                ),
                array(
                    'methods' => \WP_REST_Server::EDITABLE,
                    'callback' => array($this, 'update'),
                    'permission_callback' => array("BeansWoo\Helper", 'checkAPIPermission'),
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
        $request_data = $request->get_json_params();

        foreach ($request_data as $key => $value) {
            if ($key === 'preferences') {
                foreach ($value as $opt_key => $opt_value) {
                    Preferences::set($opt_key, $opt_value);
                }
            }
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
    private static function serialize($status = null)
    {
        return array(
            'card' => Helper::getConfig('card'),
            'merchant' => Helper::getConfig('merchant'),
            'riper_version' => Helper::getConfig('riper_version'),
            'is_setup' => Helper::isSetup(),
            'pages' => Helper::getBeansPages(),
            'preferences' => Preferences::getAll(),
            'status' => $status
        );
    }

    /**
     * Post update of the Beans plugin status
     *
     * @param enum $status of the plugin: activated, deactivated, uninstalled
     * @return void
     *
     * @since 3.4.0
     */
    public static function postWebhook($status = null)
    {
        try {
            Helper::API('TRELLIS', 'hooks/v4/woocommerce')->post(
                "connector/updated/?merchant=" . Helper::getConfig('merchant'),
                self::serialize($status),
                ['X-WC-Webhook-Source: ' . home_url()]
            );
        } catch (BeansError $e) {
        }
    }
}
