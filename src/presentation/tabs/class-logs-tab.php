<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Domain\Repositories\Logger_Repository;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Tabs\Tab;
use Nevamiss\Presentation\Pages\Tables\Logs_Table_List;

class Logs_Tab implements Tab_Interface {
	use Render_Interface;

	public const SLUG = 'logs';
	const TEMPLATE_PATH = 'resources/templates/logs';

	public function __construct( private Factory $factory, private Logs_Table_List $table_list) {
	}

	public function label(): ?string {
		return __( 'Logs', 'nevamiss' );
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
	public function table_list(): Logs_Table_List
	{
		return $this->table_list;
	}
}
