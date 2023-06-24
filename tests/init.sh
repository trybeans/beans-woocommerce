#!/usr/bin/env bash


PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." &> /dev/null && pwd )"

WP_CMD=$PROJECT_DIR/vendor/bin/wp
WP_DIR=$PROJECT_DIR/$WP_ROOT_FOLDER

echo "PROJECT_DIR=$PROJECT_DIR"

ENV_FILENAME="$PROJECT_DIR/.env"

echo "Loading $ENV_FILENAME file"

# Create local env file, if not exists
if [ ! -f $ENV_FILENAME ]; then
  cp $PROJECT_DIR/.env.testing $ENV_FILENAME
fi

# set -a # automatically export all variables
source $ENV_FILENAME
# set +a

echo "TEST_SITE_WP_DOMAIN=$TEST_SITE_WP_DOMAIN"
echo "TEST_SITE_WP_URL=$TEST_SITE_WP_URL"

cp $PROJECT_DIR/tests/wp-config-test.php $WP_DIR/wp-config.php

if ! $WP_CMD core is-installed --path=$WP_DIR; then
  echo "Wordpress has not yet been initialized. Setting up Wordpress: ";
  $WP_CMD core install \
    --path=$WP_DIR \
    --url=$TEST_SITE_WP_URL \
    --title="Beans WooCommerce Testcase" \
    --admin_user=$TEST_SITE_ADMIN_USERNAME \
    --admin_password=$TEST_SITE_ADMIN_PASSWORD \
    --admin_email=$TEST_SITE_ADMIN_EMAIL
fi

echo "Setting up WordPress"
$WP_CMD --path=$WP_DIR option update siteurl "$TEST_SITE_WP_URL"
$WP_CMD --path=$WP_DIR option update home "$TEST_SITE_WP_URL"

echo "Link Beans WooCommerce plugin"
ln -s $PROJECT_DIR/src $WP_DIR/wp-content/plugins/beans-woocommerce

echo "Activate Woocommerce"
$WP_CMD --path=$WP_DIR  --url=$TEST_SITE_WP_URL plugin activate woocommerce
# Isntall specific version manually
# ./vendor/bin/wp --path=./store plugin install woocommerce --version=6.5.0 --activate --force

echo "Setting up Woocommerce"
$WP_CMD --path=$WP_DIR option update woocommerce_demo_store yes
$WP_CMD --path=$WP_DIR option update woocommerce_currency "USD"
$WP_CMD --path=$WP_DIR option update woocommerce_store_address	"41430 Market Street"
$WP_CMD --path=$WP_DIR option update woocommerce_store_address_2 ""
$WP_CMD --path=$WP_DIR option update woocommerce_store_city	"San Francisco"
$WP_CMD --path=$WP_DIR option update woocommerce_store_postcode	91234
$WP_CMD --path=$WP_DIR option update woocommerce_default_country	"US:CA"
$WP_CMD --path=$WP_DIR option update woocommerce_registration_generate_password	no
$WP_CMD --path=$WP_DIR option update woocommerce_enable_myaccount_registration	yes

echo "Updating Woocommerce"
$WP_CMD --path=$WP_DIR --path=$WP_DIR wc update

echo "Install & Activate Woocommerce Stripe Gateway"
$WP_CMD --path=$WP_DIR plugin install woocommerce-gateway-stripe --version=6.1.1 --activate

echo "Setting up Woocommerce Stripe Gateway"
$WP_CMD --path=$WP_DIR wc payment_gateway update stripe --user=1 --settings='{"testmode":"yes", "enabled": "yes", "test_publishable_key":"'$TEST_STRIPE_PUBLIC_KEY'", "test_secret_key":"'$TEST_STRIPE_SECRET_KEY'"}'

echo "Activate Woocommerce Subscription"
$WP_CMD --path=$WP_DIR plugin install https://bnsre.s3.us-west-2.amazonaws.com/media/radix/woocommerce-subscriptions.zip --activate
$WP_CMD --path=$WP_DIR plugin install https://bnsre.s3.us-west-2.amazonaws.com/media/radix/woocommerce-memberships.zip --activate
$WP_CMD --path=$WP_DIR plugin install https://bnsre.s3.us-west-2.amazonaws.com/media/radix/woocommerce-all-products-for-subscriptions.zip --activate
$WP_CMD --path=$WP_DIR plugin install https://bnsre.s3.us-west-2.amazonaws.com/media/radix/minimum-periods-for-woocommerce-subscriptions.zip --activate

echo "Setting up Permalink"
$WP_CMD --path=$WP_DIR rewrite structure '/%postname%'

echo "Import Woocommerce data"
$WP_CMD --path=$WP_DIR plugin install wordpress-importer --activate
$WP_CMD --path=$WP_DIR import $WP_DIR/wp-content/plugins/woocommerce/sample-data/sample_products.xml --authors=create

echo "Test environment has been successfully initialized"

# To reset the db and reinstall WordPress
# ;
