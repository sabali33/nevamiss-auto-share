<?php

declare(strict_types=1);

namespace Nevamiss\Service;

use factory\Factory;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;


class Factory_Module implements ServiceModule
{
    use ModuleClassNameIdTrait;

    public function services(): array
    {
        return [
            Factory::class => fn() => new Factory()
        ];
    }
}