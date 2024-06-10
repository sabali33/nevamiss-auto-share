<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Factory;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Presentation\Components\Component;

class Factory {

	/**
	 * @throws Not_Found_Exception
	 */
	public function new( string $class_name, mixed ...$args ) {
		if ( ! class_exists( $class_name ) ) {
			throw new Not_Found_Exception( "class '$class_name' does not exist" );

		}
		return new $class_name( ...$args );
	}

    /**
     * @throws Not_Found_Exception
     */
    public function component(
        string $class_name,
        array $attributes,
        array $inner_components=[]
    ): Component {
        $renderable = new $class_name();
		return $this->new( Component::class, $renderable, $attributes, $inner_components  );
	}
}
