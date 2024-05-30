<?php
declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Service\Settings;
use Nevamiss\Services\Date;
use Nevamiss\Services\Instant_Post_Manager;
use Nevamiss\Services\Logger;
use Nevamiss\Services\Schedule_Post_Manager;
use Nevamiss\Services\Task_Runner;
use Nevamiss\Services\WP_Cron_Service;
use Psr\Container\ContainerInterface;

class Services_Module implements ServiceModule, ExecutableModule
{
    use ModuleClassNameIdTrait;

    public function run(ContainerInterface $container): bool
    {
        // TODO: Implement run() method.
    }

    public function services(): array
    {
        return [
            Date::class => fn(): Date => new Date(),
            Logger::class => fn(): Logger => new Logger(),
            Instant_Post_Manager::class => fn(): Instant_Post_Manager => new Instant_Post_Manager(),
            Schedule_Post_Manager::class => fn(): Schedule_Post_Manager => new Schedule_Post_Manager(),
            Task_Runner::class => fn(): Task_Runner => new Task_Runner(),
            Settings::class => fn(): Settings => new Settings(),
            WP_Cron_Service::class => fn(): WP_Cron_Service => new WP_Cron_Service()
        ];
    }
}