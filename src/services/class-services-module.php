<?php
declare(strict_types=1);

namespace Nevamiss\Services\Contracts;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;
use Nevamiss\Networks\Network_Clients;
use Nevamiss\Service\Settings;
use Nevamiss\Services\Date;
use Nevamiss\Services\Logger;
use Nevamiss\Services\Post_Formatter;
use Nevamiss\Services\Schedule_Post_Manager;
use Nevamiss\Services\Schedule_Tasks_Runner;
use Nevamiss\Services\Task_Runner;
use Nevamiss\Services\WP_Cron_Service;
use Psr\Container\ContainerInterface;

class Services_Module implements ServiceModule, ExecutableModule
{
    use ModuleClassNameIdTrait;

    public function run(ContainerInterface $container): bool
    {
        add_action('schedule_create_tasks_completed', [$container->get(Schedule_Tasks_Runner::class), 'run']);
        add_action('schedule_task_complete', [$container->get(Schedule_Tasks_Runner::class), 'update_task']);
    }

    public function services(): array
    {
        return [
            Date::class => fn(): Date => new Date(),
            Logger::class => fn(): Logger => new Logger(),
            Schedule_Post_Manager::class => fn(ContainerInterface $container): Schedule_Post_Manager => new Schedule_Post_Manager(
                $container->get(Schedule_Repository::class),
                $container->get(Network_Account_Repository::class),
                $container->get(Factory::class),
                $container->get(Task_Repository::class),
                $container->get(Post_Formatter::class),
                $container->get(Network_Clients::class)

            ),
            Task_Runner::class => fn(ContainerInterface $container): Task_Runner => new Task_Runner(
                $container->get(Factory::class),
                $container->get(Task_Repository::class),
                $container->get(Network_Account_Repository::class),
                $container->get(Network_Clients::class)
            ),
            Settings::class => fn(): Settings => new Settings(),
            WP_Cron_Service::class => fn(): WP_Cron_Service => new WP_Cron_Service(),
            Post_Formatter::class => fn(): Post_Formatter => new Post_Formatter(),
            Schedule_Tasks_Runner::class => function(ContainerInterface $container) {

                return new Schedule_Tasks_Runner(
                    $container->get(Task_Repository::class),
                    $container->get(Network_Account_Repository::class),
                    $container->get(Factory::class),
                    $container->get(Network_Clients::class)
                );
            }
        ];
    }
}