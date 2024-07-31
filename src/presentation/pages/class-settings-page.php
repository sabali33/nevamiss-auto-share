<?php

namespace Nevamiss\Presentation\Pages;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Networks\Media_Network_Collection;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Tabs\Tab_Collection;
use Nevamiss\Presentation\Tabs\Tab_Interface;
use Nevamiss\Services\Settings;

class Settings_Page extends Page {
	public const TEMPLE_PATH = 'templates/settings';
	const SLUG               = 'nevamiss-settings';
	private Media_Network_Collection $network_collection;

	public function __construct(
		private Settings $settings,
		private Media_Network_Collection $collection,
		private Tab_Collection $tab_collection,
	) {

		$this->network_collection = $collection;

		parent::__construct(
			$settings,
			'Settings',
			self::SLUG,
			10,
			Auto_Share_Page::SLUG,
			true
		);
	}

	public function network_collection(): Media_Network_Collection {
		return $this->network_collection;
	}

	public function settings(): Settings {
		return $this->data;
	}

//	public function notices(): void {
//		if ( ! isset( $_GET['status'] ) ) {
//			return;
//		}
//		if ( ! isset( $_GET['message'] ) ) {
//			return;
//		}
//		wp_admin_notice(
//			$_GET['message'],
//			array(
//				'type'               => $_GET['status'],
//				'dismissible'        => false,
//				'additional_classes' => array( 'inline', 'notice-alt' ),
//			)
//		);
//	}

	/**
	 * @return Array<Tab_Interface>
	 */
	public function tabs(): array {
		return $this->tab_collection->get_all();
	}

	public function tab( string $tab ): Tab_Interface {
		return $this->tab_collection->get( $tab );
	}
}
