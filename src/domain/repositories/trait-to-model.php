<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Application\Not_Found_Exception;

trait To_Model_Trait
{
	/**
	 * @throws Not_Found_Exception
	 */
	private function to_model(array $entity )
	{
		return $this->factory->new( self::ENTITY_CLASS, $entity );
	}
	private function to_models(array $entities): array
	{
		return array_map(/**
			 * @throws Not_Found_Exception
			 */
			function(array $entity){
				return $this->to_model($entity);
			},
			$entities
		);
	}
}