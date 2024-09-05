<?php

namespace Nevamiss\Domain\Entities;

class Network_Account {

	/**
	 * @param array{name:string, remote_account_id:string, id:int, token:string,
	 *     network:string, parent_remote_id:string, created_at:string, expires_in:int} $account
	 */
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
			'expires_in'        => $this->expires_in(),
		);
	}

	public function parent_remote_id() {
		return $this->account['parent_remote_id'];
	}

	public function created_at() {
		return $this->account['created_at'];
	}

	public function expires_in()
	{
		return $this->account['expires_in'] ?? null;
	}
}
