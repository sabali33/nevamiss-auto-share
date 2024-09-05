<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Tabs\Tab;
use Nevamiss\Presentation\Pages\Tables\Stats_Table_List;

class Stats_Tab implements Tab_Interface, Bulk_Delete_Interface {
	use Bulk_Delete_Trait;
	use Render_Interface;

	const TEMPLATE_PATH = 'resources/templates/stats';

	public function __construct(
		private Factory $factory,
		private Stats_Table_List $table_list
	) {
	}

	public const SLUG = 'stats';


	public function label(): ?string {
		return __( 'Stats', 'nevamiss' );
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

	public function table_list(): Stats_Table_List {
		return $this->table_list;
	}
	public function redirect( array $data ): void {
		$redirect_url = add_query_arg(
			$data,
			admin_url( 'admin.php?page=nevamiss-settings&tab=stats' )
		);

		wp_redirect( $redirect_url );
		exit;
	}
}
