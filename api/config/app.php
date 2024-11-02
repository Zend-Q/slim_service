<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

return static function (ContainerInterface $container): App {
    $app = AppFactory::createFromContainer($container);
    /** @psalm-suppress InvalidArgument App */
    (require __DIR__ . '/middleware.php')($app, $container);
    /** @psalm-suppress InvalidArgument App */
    (require __DIR__ . '/routes.php')($app);

    return $app;
};
