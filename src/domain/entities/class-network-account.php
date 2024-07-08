<?php

namespace Nevamiss\Domain\Entities;

class Network_Account {

	public function __construct(private array $account ) {
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
}
