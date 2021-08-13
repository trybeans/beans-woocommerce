#!/usr/bin/env bash

PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." &> /dev/null && pwd )"
echo "PROJECT_DIR=$PROJECT_DIR"

cp $PROJECT_DIR/tests/wp-config-test.php $PROJECT_DIR/wp/wp-config.php

WP_CMD=$PROJECT_DIR/vendor/bin/wp

if ! $WP_CMD core is-installed --path=$PROJECT_DIR/wp; then
  echo "Wordpress has not yet been initialized";
  echo "Setting up Wordpress";
  $WP_CMD core install \
    --path=$PROJECT_DIR/wp \
    --url=localhost:8800 \
    --title="Beans WooCommerce Testcase" \
    --admin_user="beans" \
    --admin_password="beans" \
    --admin_email="radix+testcases-woocommerce@trybeans.com"

  $WP_CMD option update siteurl http://localhost:8800/wp --path=$PROJECT_DIR/wp
fi

echo "Add Beans WooCommerce Plugin"
ln -s ./src ./wp/wp-content/plugins/beans-woocommerce

echo "Test environment has been successfully initialized"
