<?php

use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\Doctrine\UuidType;
use Zain\LaravelDoctrine\Jetpack\Providers\GeneratorServiceProvider;

return [
    'generators' => [
        /**
         * Load stubs files from this directory.
         * Make sure the path ends with a /
         * e.g. resource_path('jetpack/stubs/'),
         */
        'stubs_directory' => resource_path('jetpack/stubs/'),

        'namespaces' => [
            /**
             * Define the namespace (relative to your root namespace e.g. App\)
             * where your Entity classes are located.
             */
            'entities' => 'Entities',

            /**
             * Define the namespace (relative to your root namespace e.g. App\)
             * where your Value Object classes are located.
             *
             * These value objects can be mapped to the database as Embeddables.
             */
            'values' => 'Values',

            /**
             * Define the namespace (relative to your root namespace e.g. App\)
             * where your Fluent Mapping classes are located.
             */
            'mappings' => 'Database\Doctrine\Mappings',
        ],
    ],

    /**
     * These settings are only relevant if you're using the Fluent mapping driver.
     * Check your configuration option for `doctrine.managers.default.meta`
     * under config/doctrine.php
     */
    'fluent' => [
        /**
         * By default, the FluentServiceProvider from zain/laravel-doctrine-jetpack will
         * auto-load and register the mappings from this directory.
         *
         * To disable this functionality, set the value to `null`.
         * @see \Zain\LaravelDoctrine\Jetpack\Providers\FluentServiceProvider::registerMappings()
         */
        'autoload_mappings_from' => app_path('Database/Doctrine/Mappings'),

        /**
         * To allow easily mapping UUID types, we register custom LaravelDoctrine\Fluent
         * macros. Use this configuration option to specify which Uuid type we should
         * for Doctrine.
         *
         * You'll want  to require `ramsey/uuid-doctrine` to make use of this.
         *
         * To turn this feature off, set the value of `uuid_type` to `null`.
         *
         * @link http://laraveldoctrine.org/docs/1.4/fluent/reference#macros
         * @see \Zain\LaravelDoctrine\Jetpack\Providers\FluentServiceProvider::registerCustomMacros()
         */
        'uuid_type' => [
            'name' => UuidType::NAME,
            'class' => UuidType::class,
        ]
    ],
];
