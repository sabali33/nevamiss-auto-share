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
            Date::class => fn() => new Date(),
            Logger::class => fn() => new Logger(),
            Instant_Post_Manager::class => fn() => new Instant_Post_Manager(),
            Schedule_Post_Manager::class => fn() => new Schedule_Post_Manager(),
            Task_Runner::class => fn() => new Task_Runner(),
            Settings::class => fn() => new Settings(),
        ];
    }
}