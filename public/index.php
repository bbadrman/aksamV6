<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpKernel\Kernel;

require dirname(__DIR__) . '/vendor/autoload.php';

if (class_exists(Dotenv::class)) {
    (new Dotenv())->loadEnv(dirname(__DIR__) . '/.env');
}

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
