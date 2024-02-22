<?php

namespace BeansWoo\Server;

/**
 * Add the Log REST resource on WP REST API.
 *
 * @since 4.0.0
 */
class LogRESTController extends \WP_REST_Controller
{
    // The `wc-` prefix helps to use the WooCommerce Rest authentication on our routes.
    protected $namespace = "wc-beans/v2";
    protected $resource = 'log';

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
            "/{$this->resource}/",
            array(
                array(
                    'methods' => \WP_REST_Server::READABLE,
                    'callback' => array($this, 'list'),
                    'permission_callback' => array("BeansWoo\Helper", 'checkAPIPermission'),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            "/{$this->resource}/(?P<id>[\w.-]+)",
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
                    'permission_callback' => array("BeansWoo\Helper", 'checkAPIPermission'),
                ),
            )
        );
    }

    /**
     * Retrieve the content of a specific log file.
     *
     * @param \WP_REST_Request $request
     * @return array the log file content
     *
     * @since 4.0.0
     */
    public function retrieve($request)
    {
        $log_handle = $request['id'];
        $logs = \WC_Log_Handler_File::get_log_files();

        if (!isset($logs[$log_handle])) {
            $response = new \WP_REST_Response(array("error" => "Log file `$log_handle` cannot be found."));
            $response->set_status(404);
            return $response;
        }

        $log_content = file_get_contents(WC_LOG_DIR . $logs[$log_handle]);

        $response = new \WP_REST_Response(array('data' => $log_content));
        $response->set_status(200);
        return $response;
    }

    /**
     * List available log files
     *
     * @param \WP_REST_Request $request
     * @return array the list of log files by name
     *
     * @since 4.0.0
     */
    public function list($request)
    {
        $response = new \WP_REST_Response(array('data' => \WC_Log_Handler_File::get_log_files()));
        $response->set_status(200);
        return $response;
    }
}
