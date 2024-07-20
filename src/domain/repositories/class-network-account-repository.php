<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Contracts\Update_Interface;
use Nevamiss\Domain\Entities\Network_Account;


class Network_Account_Repository implements
	Create_Interface,
	Delete_Interface,
	Get_All_Interface,
	Get_One_Interface,
	Update_Interface {

	use Repository_Common_Trait;
	use To_Model_Trait;
	use Create_Trait;
	use Get_One_Trait;
	use Delete_Trait;
	use Get_All_Trait;
	use Count_Model_Trait;

	private const ENTITY_NAME  = 'Network Account';
	private const ENTITY_CLASS = Network_Account::class;

	private const ENTITY_SLUG = 'network_account';

	private const ALLOWED_TABLE_COLUMNS = array(
		'id',
		'name',
		'remote_account_id',
		'token',
		'network',
	);

	public function get_by_remote_id( string|int $remote_account_id ): array|false {
		$account = $this->get_all( array( 'where' => array( 'remote_account_id' => $remote_account_id ) ) );

		return empty( $account ) ? false : $account[0];
	}

	/**
	 * @param int|string $id Remote account ID.
	 * @param array      $data
	 * @return bool
	 */
	public function update( string|int $id, array $data ): bool {
		$updated    = $this->wpdb->update( $this->table_name(), $data, array( 'remote_account_id' => $id ) );
		$model_slug = self::ENTITY_SLUG;

		do_action( "nevamiss_{$model_slug}_updated", $this->wpdb->last_result );

		return (bool) $updated;
	}

	private function table_name(): string {
		return "{$this->wpdb->prefix}nevamiss_network_accounts";
	}
}
