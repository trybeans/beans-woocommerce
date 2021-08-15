#!/usr/bin/env bash

PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." &> /dev/null && pwd )"
SITE_URL=http://localhost:8800/wp
WP_CMD=$PROJECT_DIR/vendor/bin/wp

echo "PROJECT_DIR=$PROJECT_DIR"

cp $PROJECT_DIR/tests/wp-config-test.php $PROJECT_DIR/wp/wp-config.php


if ! $WP_CMD core is-installed --path=$PROJECT_DIR/wp; then
  echo "Wordpress has not yet been initialized";
  echo "Setting up Wordpress";
  $WP_CMD core install \
    --path=$PROJECT_DIR/wp \
    --url=$SITE_URL \
    --title="Beans WooCommerce Testcase" \
    --admin_user="beans" \
    --admin_password="beans" \
    --admin_email="radix+testcases-woocommerce@trybeans.com"

  # $WP_CMD option update siteurl $SITE_URL --path=$PROJECT_DIR/wp
fi

echo "Link Beans WooCommerce plugin"
ln -s $PROJECT_DIR/src $PROJECT_DIR/wp/wp-content/plugins/beans-woocommerce

echo "Activate Woocommerce"
$WP_CMD --path=$PROJECT_DIR/wp  --url=localhost:8800/wp plugin activate woocommerce

echo "Import Woocommerce data"
$WP_CMD --path=$PROJECT_DIR/wp plugin install wordpress-importer --activate
$WP_CMD --path=$PROJECT_DIR/wp import $PROJECT_DIR/wp/wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=create


echo "Test environment has been successfully initialized"


# To reset the db an reinstall wp
# wp --path=$PROJECT_DIR/wp db clean --yes