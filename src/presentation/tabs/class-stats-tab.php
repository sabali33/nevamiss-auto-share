<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Tabs\Tab;
use Nevamiss\Presentation\Pages\Tables\Stats_Table_List;

class Stats_Tab implements Tab_Interface {

	const TEMPLATE_PATH = 'resources/templates/stats';

	public function __construct(
		private Factory $factory,
		private Stats_Table_List $stats_table_list
	)
	{
	}

	public const SLUG = 'stats';

	public function render($attributes = array()): string
	{
		ob_start();

		include NEVAMISS_PATH . self::TEMPLATE_PATH .'.php';

		return ob_get_clean();
	}

	public function label(): ?string
	{
		return __("Stats", 'nevamiss');
	}

	public function slug(): string
	{
		return self::SLUG;
	}

	/**
	 * @throws Not_Found_Exception
	 */
	public function link(string $active_tab): Component
	{
		return $this->factory->component(
			Tab::class,
			[
				'slug' => $this->slug(),
				'label' => $this->label(),
				'active_tab' => $active_tab
			]
		);
	}

	public function table_list(): Stats_Table_List
	{
		return $this->stats_table_list;
	}

	public function bulk_delete()
	{
	}
}