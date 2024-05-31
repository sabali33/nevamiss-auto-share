<?php
declare(strict_types=1);

namespace Nevamiss\Service;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Domain\Repositories\Network_Account_Repository;
use Nevamiss\Domain\Repositories\Posts_Stats_Repository;
use Nevamiss\Domain\Repositories\Schedule_Queue_Repository;
use Nevamiss\Domain\Repositories\Schedule_Repository;
use Nevamiss\Domain\Repositories\Task_Repository;
use Psr\Container\ContainerInterface;
use Saas\Infrastructure\WP_Cron_Service;

class Repositories_Module implements ServiceModule
{
    use ModuleClassNameIdTrait;

    public function services(): array
    {
        return [
            Schedule_Repository::class => fn(): Schedule_Repository => new Schedule_Repository(),
            Posts_Stats_Repository::class => fn(): Posts_Stats_Repository => new Posts_Stats_Repository(),
            Schedule_Queue_Repository::class => fn(): Schedule_Queue_Repository => new Schedule_Queue_Repository(),
            Task_Repository::class => fn(): Task_Repository => new Task_Repository(),
            Logger_Repository::class => fn(): Logger_Repository => new Logger_Repository(),
            Network_Account_Repository::class => fn(): Network_Account_Repository => new Network_Account_Repository(),
        ];
    }
}