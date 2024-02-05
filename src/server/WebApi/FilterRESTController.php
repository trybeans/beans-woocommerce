<?php

namespace BeansWoo\Server;

use BeansWoo\Helper;

/**
 * Add the Filter REST resource on WP REST API.
 *
 * @since 4.0.0
 */
class FilterRESTController extends \WP_REST_Controller
{
    // The `wc-` prefix helps to use the WooCommerce Rest authentication on our routes.
    protected $namespace = "wc-beans/v2";
    protected $resource = 'filter';

    /**
     * Register custom REST routes on WP REST API.
     *
     * @return void
     *
     * @since 4.0.0
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            "/{$this->resource}/(?P<id>[\w]+)",
            array(
                'args' => array(
                    'id' => array(
                        'description' => 'WordPress filter identifier.',
                        'type'        => 'string',
                    ),
                ),
                array(
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => array($this, 'retrieve'),
                    'permission_callback' => array(Helper, 'checkAPIPermission'),
                ),
            )
        );
    }

    /**
     * Retrieve the wp_filter object.
     *
     * @param \WP_REST_Request $request
     * @return array the serialized wp_filter object
     *
     * @since 3.4.0
     */
    public function retrieve($request)
    {
        global $wp_filter;

        $filter_name = $request['id'];

        $response = new \WP_REST_Response($wp_filter[$filter_name]);
        $response->set_status(200);
        return $response;
    }
}
