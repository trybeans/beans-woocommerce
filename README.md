# Beans for WooCommerce

### 1. Install Composer 

```shell script
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --filename=composer
php -r "unlink('composer-setup.php');"
```
If you are having trouble to find out more about it here: https://getcomposer.org/doc/00-intro.md

### 2. Install dependencies 

```shell script
./composer update
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