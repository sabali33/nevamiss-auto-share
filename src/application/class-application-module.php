<?php

namespace Nevamiss\Application;

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
        return [
            DB::class => static function(){
                global $wpdb;
                return new DB($wpdb);
            }
        ];
    }

    public function run(ContainerInterface $container): bool
    {
        register_activation_hook(
         NEVAMISS_ROOT,
            static function() use($container){
                $this->activate($container);
            }
        );
        register_deactivation_hook(NEVAMISS_ROOT, static function() use($container){
            $this->deactivate($container);
        });

        return true;
    }

    /**
     * @throws Exception
     */
    private function activate(ContainerInterface $container): void
    {
        // Check for required PHP version
        if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
            throw new Exception('The server PHP version is not compatible', );
        }
        $container->get(DB::class)->setup_tables();
    }

    public function deactivate(ContainerInterface $container)
    {
        $container->get(DB::class)->drop_tables();
    }
}