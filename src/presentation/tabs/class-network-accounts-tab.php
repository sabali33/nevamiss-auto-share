<?php

declare(strict_types=1);

namespace Nevamiss\presentation\Tabs;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Networks\Media_Network_Collection;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Tabs\Tab;
use Nevamiss\Presentation\Pages\Tables\Network_Accounts_Table_List;

class Network_Accounts_Tab implements Tab_Interface {

	const TEMPLATE_PATH = 'resources/templates/network-accounts';
	const LOGIN_PATH = 'resources/templates/network-login';
	private string $title;

	public function __construct(
		private Factory $factory,
		private Network_Accounts_Table_List $table_list,
		private Media_Network_Collection $network_collection
	)
	{
		$this->title = __('Network Accounts', 'nevamiss');
	}
	public const SLUG = 'network-accounts';

	public function render($attributes = array()): string
	{
		ob_start();

		include NEVAMISS_PATH . self::TEMPLATE_PATH .'.php';

		return ob_get_clean();
	}

	public function label(): ?string
	{
		return __('Network Accounts', 'nevamiss');
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

	public function table_list(): Network_Accounts_Table_List
	{
		return $this->table_list;
	}

	public function notices()
	{
	}

	public function bulk_delete()
	{
	}

	public function title(): string
	{
		return $this->title;
	}
	public function networks(): array
	{
		return $this->network_collection->get_all();
	}
	public function login_links(): bool|string
	{
		ob_start();

		include NEVAMISS_PATH . self::LOGIN_PATH .'.php';

		return ob_get_clean();
	}
}