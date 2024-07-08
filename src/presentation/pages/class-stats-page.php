<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Posts_Stats_Repository;

class Stats_Page extends Page {

	public const TEMPLE_PATH = 'templates/stats';
	const SLUG               = 'nevamiss-stats';
	public function __construct( Posts_Stats_Repository $stats ) {
		parent::__construct(
			$stats,
			'Stats',
			self::SLUG,
			10,
			Auto_Share_Page::SLUG,
			true
		);
	}
}
