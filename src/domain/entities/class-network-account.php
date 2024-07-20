<?php

namespace Nevamiss\Domain\Entities;

class Network_Account {

	public function __construct( private array $account ) {
	}

	public function name(): string {
		return $this->account['name'];
	}

	public function remote_account_id(): string {
		return $this->account['remote_account_id'];
	}

	public function id(): int {
		return $this->account['id'];
	}
	public function token(): string {
		return $this->account['token'];
	}
	public function network(): string {
		return $this->account['network'];
	}

	public function to_array(): array {
		return array(
			'name'              => $this->name(),
			'remote_account_id' => $this->remote_account_id(),
			'id'                => $this->id(),
			'token'             => $this->token(),
			'network'           => $this->network(),
			'parent_remote_id'  => $this->parent_remote_id(),
		);
	}

	public function parent_remote_id() {
		return $this->account['parent_remote_id'];
	}

	public function created_at()
	{
		return $this->account['created_at'];
	}
}
