<?php

namespace Nevamiss\Setup;

use Exception;
use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Psr\Container\ContainerInterface;

class Module implements ServiceModule, ExecutableModule
{
    const MINIMUM_PHP_VERSION = '8.0';

    use ModuleClassNameIdTrait;

    public function services(): array
    {
        return [];
    }

    public function run(ContainerInterface $container): bool
    {
        register_activation_hook(NEVAMISS_ROOT, [$this, 'activate']);
        register_deactivation_hook(NEVAMISS_ROOT, [$this, 'deactivate']);

        return true;
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
    }

    public function deactivate()
    {

    }
}