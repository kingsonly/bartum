<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// This file allows us to emulate Apache's "mod_rewrite" functionality from the
// built-in PHP web server. This provides a convenient way to test a Laravel
// application without having installed a "real" web server software here.
if ($uri !== '/' && file_exists(__DIR__.'/public'.$uri)) {
    return false;
}

require_once __DIR__.'/public/index.php';

//sed -i -e 's:/public/index.php:index.php:g' server.php && sed -i -e 's:/../vendor/autoload.php:\vendor/autoload.php:g' public/index.php && sed -i -e 's:DB_DATABASE=forge:DB_DATABASE=bartumen_forge:g' .env.sample && sed -i -e 's:DB_USERNAME=forge:DB_USERNAME=bartumen_forge:g' .env.sample  && sed -i -e 's:/../bootstrap/app.php:bootstrap/app.php:g' public/index.php