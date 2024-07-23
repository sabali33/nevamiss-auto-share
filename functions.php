<?php

declare(strict_types=1);

namespace Nevamiss;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Presentation\Components\Component;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * @throws Throwable
 */
function container(): ContainerInterface
{
	return plugin()->container();
}

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 * @throws Throwable
 */
function factory(): Factory{
	return container()->get(Factory::class);
}

/**
 * @throws Not_Found_Exception
 * @throws NotFoundExceptionInterface
 * @throws Throwable
 * @throws ContainerExceptionInterface
 */
function component(string $class, array $attributes, array $inner_components=[]): Component
{
	return \Nevamiss\factory()->component($class, $attributes, $inner_components);
}