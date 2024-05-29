<?php
declare(strict_types=1);

namespace Nevamiss\Service;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ServiceModule;
use Psr\Container\ContainerInterface;
use Saas\Infrastructure\WP_Cron_Service;

class Module implements ServiceModule, ExecutableModule
{

    public function run(ContainerInterface $container): bool
    {
        // TODO: Implement run() method.
    }

    public function id(): string
    {
        // TODO: Implement id() method.
    }

    public function services(): array
    {
        return [
            Schedule_Collection::class =>
            static function($container): Schedule_Collection {
                $schedule_service = $container->get(WP_Cron_Service::class);
                $schedules = $schedule_service->get_all();
                return new Schedule_Collection($schedules);
            },

            Settings::class => static function(){
                return new Settings();
            }

        ];
    }
}