<?php

namespace Nevamiss\Presentation\Pages;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Networks\Network_Clients;
use Nevamiss\Presentation\Post_Meta\Post_Meta;
use Nevamiss\Service\Settings;
use Nevamiss\Services\Post_Formatter;
use Psr\Container\ContainerInterface;

class Presentation_Module implements ServiceModule, ExecutableModule
{
    use ModuleClassNameIdTrait;

    public function services(): array
    {
        return [

            Schedules_Page::class => static function(ContainerInterface $container) {
                
                return  new Schedules_Page(
                    $container->get(Schedule_Repository::class),
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
            },
            Stats_Page::class => static function(ContainerInterface $container ){
                return new Stats_Page(
                    $container->get(Posts_Stats_Repository::class),
                    'Stats',
                    'admin.php?page=stats',
                    'stats',
                    10
                );
            },
            Schedule_View_Page::class => function(ContainerInterface $container): Schedule_View_Page {

                return new Schedule_View_Page(
                    $container->get(Posts_Stats_Repository::class),
                    'Schedule',
                    'admin.php?page=schedule',
                    'schedule',
                    10
                );
            },
            Post_Meta::class => function(ContainerInterface $container) {
                return new Post_Meta(
                    $container->get(Network_Account_Repository::class),
                    $container->get(Factory::class),
                    $container->get(Post_Formatter::class),
                    $container->get(Network_Clients::class),
                );
            },
        ];
    }

    public function run(ContainerInterface $container): bool
    {
        add_action(
            'admin_menu',
            static function () use ($container){
                $container->get(Schedules_Page::class)->register();
                $container->get(Settings_Page::class)->register();
                $container->get(Schedule_View_Page::class)->register();
                $container->get(Stats_Page::class)->register();
            }
        );

        add_action(
            'add_meta_boxes',
            [$container->get(Post_Meta::class), 'meta_boxes']
        );

        return true;
    }
}