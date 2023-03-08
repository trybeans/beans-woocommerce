# Beans for WooCommerce

### Prerequisite

- You need to make sure that you have mysql installed and that `mysql` is available in your path 
To check that you can run `which mysql`


### 1. Install Composer 

```shell script
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --filename=composer
php -r "unlink('composer-setup.php');"
```
If you are having trouble to find out more about it here: https://getcomposer.org/doc/00-intro.md

### 2. Install dependencies 

Ensure that mysql is installed and setup. 

```shell script
./composer install
```

### 3. Running Linters 
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
./vendor/bin/phpstan analyze --memory-limit=100
```

To reformat the code:
```shell script
./vendor/bin/phpcbf
```

### 4. Develop 

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

 
### 5. Testing 


### 6. DocStrings 
https://stackoverflow.com/questions/1310050/php-function-comments
https://www.phpdoc.org/
https://phpdocu.sourceforge.net/howto.php
https://manual.phpdoc.org/HTMLSmartyConverter/HandS/phpDocumentor/tutorial_tags.param.pkg.html
https://developer.wordpress.org/coding-standards/inline-documentation-standards/php/
