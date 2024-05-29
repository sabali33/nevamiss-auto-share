<?php

namespace Nevamiss\Presentation\Pages;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Service\Settings;
use Psr\Container\ContainerInterface;

class Module implements ServiceModule, ExecutableModule
{
    use ModuleClassNameIdTrait;

    public function services(): array
    {
        return [

            Schedules_Page::class => static function(ContainerInterface $container) {
                
                return  new Schedules_Page(
                    $container->get(Schedule_Collection::class),
                    'Schedules',
                    'admin.php?page=schedules',
                    'schedules',
                    9
                );
            },

            Settings_Page::class => static function(ContainerInterface $container ){
                return new Settings_Page(
                    $container->get(Settings::class),
                    'Settings',
                    'admin.php?page=settings',
                    'settings',
                    10
                );
            }
        ];
    }

    public function run(ContainerInterface $container): bool
    {
        add_action('admin_menu', [ $container->get(Schedules_Page::class), 'register']);
        add_action('admin_menu', [ $container->get(Settings_Page::class), 'register']);

        return true;
    }
}