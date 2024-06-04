<?php

declare(strict_types=1);

namespace Nevamiss\Application;

use Exception;

class Plugin {

    const MINIMUM_PHP_VERSION = '8.0';
    private static DB $db;

    public function __construct( DB $db)
    {
        static::$db = $db;
    }

    /**
     * @throws Exception
     */
    public static function activate(): void
    {
        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            throw new Exception("The server PHP version {PHP_VERSION} is not compatible", );
        }
        static::$db->setup_tables();
    }

    public function deactivate(): void
    {
        self::$db->drop_tables();
    }
}