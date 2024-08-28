<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Infrastructure\Url_Shortner\Rebrandly;
use Nevamiss\Infrastructure\Url_Shortner\Shortner_Collection;
use Nevamiss\Services\Http_Request;
use Nevamiss\Services\Settings;
use Psr\Container\ContainerInterface;

class Infrastructure_Module implements ServiceModule{
	use ModuleClassNameIdTrait;

	/**
	 * @return callable[]
	 */
	public function services(): array
	{
		return [
			Rebrandly::class => fn(ContainerInterface $container) => new Rebrandly(
				$container->get(Http_Request::class),
				$container->get(Settings::class)
			),
			Shortner_Collection::class => function (ContainerInterface $container) {
				$collection = new Shortner_Collection();

				$shortners = apply_filters('nevamiss-url-shortners', array(
					$container->get(Rebrandly::class)
				));

				foreach ($shortners as $shortner){
					$collection->register($shortner);
				}

				return $collection;
			}
		];
	}
}