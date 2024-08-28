<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Url_Shortner;

class Shortner_Collection
{
	/**
	 * @var array<URL_Shortner_Interface>
	 */
	private array $shortners = [];

	public function register(URL_Shortner_Interface $shortner)
	{
		$this->shortners[$shortner->id()] = $shortner;
	}

	public function all()
	{
		return $this->shortners;
	}

	public function get(string $id): URL_Shortner_Interface|false
	{
		return $this->exists($id) ? $this->shortners[$id] : false;
	}

	private function exists(string $id): bool
	{
		return isset($this->shortners[$id]);
	}
}