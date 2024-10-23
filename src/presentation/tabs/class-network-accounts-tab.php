<?php

declare(strict_types=1);

namespace Nevamiss\Presentation\Tabs;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Factory\Factory;
use Nevamiss\Infrastructure\networks\Media_Network_Collection;
use Nevamiss\Presentation\Components\Component;
use Nevamiss\Presentation\Components\Tabs\Tab;
use Nevamiss\Presentation\Pages\Tables\Network_Accounts_Table_List;

class Network_Accounts_Tab implements Tab_Interface, Bulk_Delete_Interface {

	use Bulk_Delete_Trait;
	use Render_Interface;

	const TEMPLATE_PATH = 'resources/templates/network-accounts';
	const LOGIN_PATH    = 'resources/templates/network-login';
	private string $title;

	public function __construct(
		private Factory $factory,
		private Network_Accounts_Table_List $table_list,
		private Media_Network_Collection $network_collection
	) {
	}
	public const SLUG = 'network-accounts';


	public function label(): ?string {
		return __( 'Network Accounts', 'nevamiss' );
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

	public function table_list(): Network_Accounts_Table_List {
		return $this->table_list;
	}

	public function networks(): array {
		$clients = $this->network_collection->get_all();

		$links = array();
		foreach ( $clients as $network => $client ) {
			try {
				$links[] = array(
					/* translators: %s: Social media network name */
					'label' => sprintf( __( 'Login to %s', 'nevamiss' ), ucfirst( $network ) ),
					'url'   => $client->auth_link(),
				);
			} catch ( \Exception $exception ) {
				$links[] = array(
					/* translators: %s: Social media network name */
					'label' => sprintf( __( 'You need to setup %s account here', 'nevamiss' ), $network ),
					'url'   => admin_url( 'admin.php?page=nevamiss-settings&tab=general&section=network_api_keys' ),
				);
			}
		}
		return $links;
	}
	public function login_links(): bool|string {
		ob_start();

		include NEVAMISS_PATH . self::LOGIN_PATH . '.php';

		return ob_get_clean();
	}

	public function redirect( array $data ): void {
		$redirect_url = add_query_arg(
			$data,
			admin_url( 'admin.php?page=nevamiss-settings&tab=network-accounts' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}
}
