# Beans for WooCommerce

## 🔨 Prerequisite

- You need to make sure that you have mysql installed and that `mysql` is available in your path.
To check that you can run `which mysql`

- If you don't have [composer](https://getcomposer.org/doc/00-intro.md), you will need to install it:
 
```shell script
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --filename=composer
php -r "unlink('composer-setup.php');"
```

- Install all dependencies using composer:

```shell script
./composer install
```

## ⏯ Develop 

1. Create a local tunnel for localhost on port 8800 using ngrok.io
```shell script
ngrok http 8800
```

2. Copy `.env.testing` to .`env.local` and update env vars.


3. If needed reset any existing testing configuration 
```shell script
./composer test-reset
```

4. Launch the web server
```shell script
./composer run-script start
```

Visit the address given by the localtunnel
Wordpress admin username and password are `beans`

## 🧽 Linting 

To run all linters:
```shell script
./composer run-script lint
```

To only run phpcs, 

```shell script
./vendor/bin/phpcs
```

To only run phpstan, 

```shell script
./vendor/bin/phpstan analyze --memory-limit=200M
```

To reformat the code:
```shell script
./vendor/bin/phpcbf
```

Ensure that your code is well documented:

https://stackoverflow.com/questions/1310050/php-function-comments
https://www.phpdoc.org/
https://phpdocu.sourceforge.net/howto.php
https://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_tags.param.pkg.html
https://developer.wordpress.org/coding-standards/inline-documentation-standards/php/
 
## 🧪 Testing 

to be completed...

## 🐞 Debugging 

Install [Xdebug](https://xdebug.org/) to debug your php code. 
Follow the wizard step here: https://xdebug.org/wizard

To get PHPInfo:

```bash
php -f scripts/phpinfo.php > phpinfo.txt
```

Open `phpinfo.txt` to read the output.


To create pot file:
```bash
./vendor/bin/wp i18n make-pot src src/i18n/beans-woocommerce.pot --exclude=src
```


## 📕 Documentation

- [WooCommerce minimum requirements by version](https://woocommerce.com/document/update-php-wordpress/)
- [WooCommerce versions](https://developer.woocommerce.com/releases/)
- [WooCommerce REST API](https://woocommerce.com/document/woocommerce-rest-api/)
- [WordPress minimum requirements by version](https://make.wordpress.org/core/handbook/references/php-compatibility-and-wordpress-versions/)
- [PHP supported versions](https://www.php.net/supported-versions.php)


Useful links 
- Debugging WP 404: https://gist.github.com/yunusga/33cf0ba9e311e12df4046722e93d4123

