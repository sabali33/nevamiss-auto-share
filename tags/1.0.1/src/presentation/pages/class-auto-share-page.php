<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Repositories\Command_Query;
use Nevamiss\Services\Network_Post_Aggregator;

class Auto_Share_Page extends Page {
	public const SLUG        = 'nevamiss-auto-share-content';
	public const TEMPLE_PATH = 'templates/home';

	public function __construct( private Network_Post_Aggregator $aggregator ) {

		parent::__construct(
			'',
			__( 'Auto Share', 'nevamiss' ),
			self::SLUG,
			10,
		);
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function upcoming_posts(): array {
		return $this->aggregator->upcoming_posts();
	}

	public function last_posted(): array {
		return $this->aggregator->last_posted();
	}
}
