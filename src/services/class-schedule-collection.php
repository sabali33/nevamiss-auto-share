<?php
declare(strict_types=1);
namespace Nevamiss\Service;

use Exception;
use Traversable;

class Schedule_Collection implements \IteratorAggregate {

	public array $schedules;
	public function __construct( array $schedules ) {
		$this->schedules = $schedules;
	}

	public function getIterator() {
		return new \ArrayIterator( $this->schedules );
	}

}
