<?php

namespace Nevamiss\Application;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Psr\Container\ContainerInterface;

class Application_Module implements ServiceModule, ExecutableModule
{

    use ModuleClassNameIdTrait;

    public function services(): array
    {
        return [
            DB::class => static function(){
                global $wpdb;
                return new DB($wpdb);
            },
            Plugin::class => static function(ContainerInterface $container) {
                return new Plugin($container->get(DB::class));
            }
        ];
    }

    public function run(ContainerInterface $container): bool
    {

        \register_deactivation_hook(
            NEVAMISS_ROOT,
            [$container->get(Plugin::class), 'deactivate']
        );

        return true;
    }
}