<?php

declare(strict_types=1);

namespace Nevamiss\Service;

use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Domain\Factory\Factory;


class Factory_Module implements ServiceModule {

	use ModuleClassNameIdTrait;

	public function services(): array {
		return array(
			Factory::class => fn() => new Factory(),
		);
	}
}
