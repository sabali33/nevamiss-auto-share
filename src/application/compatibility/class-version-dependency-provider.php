<?php

declare(strict_types=1);

namespace Nevamiss\Application\Compatibility;

class Version_Dependency_Provider implements Versions_Dependency_Interface {


	/**
	 * @return string
	 */
	public function php_version(): mixed {
		return PHP_VERSION;
	}
}
