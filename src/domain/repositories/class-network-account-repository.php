<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Repositories;

use Nevamiss\Domain\Contracts\Create_Interface;
use Nevamiss\Domain\Contracts\Delete_Interface;
use Nevamiss\Domain\Contracts\Get_All_Interface;
use Nevamiss\Domain\Contracts\Get_One_Interface;
use Nevamiss\Domain\Entities\Network_Account;


class Network_Account_Repository implements Create_Interface, Delete_Interface, Get_All_Interface, Get_One_Interface {

	use Repository_Common_Trait;
	use Create_Trait;
	use Get_One_Trait;
	use Delete_Trait;
	use Get_All_Trait;

	private const ENTITY_NAME  = 'Network Account';
	private const ENTITY_CLASS = Network_Account::class;

	private const ALLOWED_TABLE_COLUMNS = array(
		'id',
		'name',
		'remote_account_id',
		'token',
		'network',
	);

	private function table_name(): string {
		return "{$this->wpdb->prefix}_nevamiss_network_accounts";
	}
}
