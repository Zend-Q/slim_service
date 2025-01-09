<?php

declare(strict_types=1);

use App\Auth;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return [
    EntityManagerInterface::class => static function (ContainerInterface $container): EntityManagerInterface {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *     metadata_dirs:array,
         *     dev_mode:bool,
         *     proxy_dir:string,
         *     cache_dir:?string,
         *     types:array<string,string>,
         *     subscribers:string[],
         *     connection:array
         *     } $settings
         */
        $settings = $container->get('config')['doctrine'];
        $config   = ORMSetup::createAttributeMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir'] ? new FilesystemAdapter('', 0, $settings['cache_dir']) : new ArrayAdapter(),
            false
        );
        $config->setNamingStrategy(new UnderscoreNamingStrategy());
        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }
        $eventManager = new EventManager();
        foreach ($settings['subscribers'] as $name) {
            /** @var EventSubscriber $subscriber */
            $subscriber = $container->get($name);
            $eventManager->addEventSubscriber($subscriber);
        }
        /** @psalm-suppress InvalidArgument */
        $connection = DriverManager::getConnection($settings['connection'], $config, $eventManager);

        return new EntityManager($connection, $config, $eventManager);
    },
    'config'                      => [
        'doctrine' => [
            'dev_mode'      => true,
            'cache_dir'     => __DIR__ . '/../../var/cache/' . PHP_SAPI . '/doctrine/cache',
            'proxy_dir'     => __DIR__ . '/../../var/cache/' . PHP_SAPI . '/doctrine/proxy',
            'connection'    => [
                'driver'  => 'pdo_pgsql',
                'url'     => getenv('DB_URL'),
                'charset' => 'utf-8',
            ],
            'metadata_dirs' => [
                __DIR__ . '/../../src/Auth/Entity',
            ],
            'types'         => [
                Auth\Entity\User\IdType::NAME    => Auth\Entity\User\IdType::class,
                Auth\Entity\User\EmailType::NAME => Auth\Entity\User\EmailType::class,
            ],
        ],
    ],
];
