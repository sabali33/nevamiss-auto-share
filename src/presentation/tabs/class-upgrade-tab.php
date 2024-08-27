<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Tabs\Tab;
use Nevamiss\Presentation\Pages\Tables\Logs_Table_List;

class Upgrade_Tab implements Tab_Interface {
	use Render_Interface;
	public const SLUG = 'upgrade';
	const TEMPLATE_PATH = 'resources/templates/upgrade';

	public function __construct( private Factory $factory) {
	}

	public function label(): ?string {
		return __( 'Become a pro member', 'nevamiss' );
	}

	public function slug(): string {
		return self::SLUG;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function link( string $active_tab ): Component {
		return $this->factory->component(
			Tab::class,
			array(
				'slug'       => $this->slug(),
				'label'      => $this->label(),
				'active_tab' => $active_tab,
			)
		);
	}
	public function premium_feature_list()
	{
		return [
			'Import export schedules',
			'More channels to share to',
			'Calendar view of schedules',
			'Remote sharing support never miss a post',
			'Pre-publish account customization',
			'Group share on post meta',
			'AI generated posts for sharing',
			'Automatic clearing of logs'
		];
	}

}
