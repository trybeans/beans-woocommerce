<?php

namespace BeansWoo\Server;

use BeansWoo\Helper;

class APIRESTController {
    protected $namespace = 'beans/v1';

	protected $rest_base = 'riper_version';

	public function update_riper_version( $data ) {
        Helper::setConfig('riper_version', $data['riper_version']);

		return array( 'result' => true );
	}

	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				'methods' => 'POST',
				'callback' => array( $this, 'update_riper_version' ),
			)
		);
	}
}