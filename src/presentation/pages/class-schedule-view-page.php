<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Domain\Repositories\Schedule_Repository;

class Schedule_View_Page extends Page {

	public const TEMPLE_PATH = 'templates/schedule';
	public const SLUG        = 'schedule';

	public function __construct( Schedule_Repository $schedule_repository ) {
		parent::__construct(
			$schedule_repository,
			'Schedule',
			self::SLUG,
			10,
			null,
			true
		);
	}
}
