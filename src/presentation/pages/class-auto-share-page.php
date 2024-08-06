<?php

namespace Nevamiss\Presentation\Pages;

class Auto_Share_Page extends Page {
	public const SLUG        = 'nevamiss-auto-share-content';
	public const TEMPLE_PATH = 'templates/home';

	public function __construct() {

		parent::__construct(
			'',
			__( 'Auto Share', 'nevamiss' ),
			self::SLUG,
			10,
		);
	}
}
