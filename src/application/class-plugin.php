<?php

declare(strict_types=1);

namespace Nevamiss\Application;

use Exception;

class Plugin {

    const MINIMUM_PHP_VERSION = '8.0';

    public function __construct(private readonly DB $db)
    {
    }

    /**
     * @throws Exception
     */
    public function activate(): void
    {
        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            throw new Exception('The server PHP version is not compatible', );
        }

        $this->db->setup_tables();
    }

    public function deactivate(): void
    {
        $this->db->drop_tables();
    }
}