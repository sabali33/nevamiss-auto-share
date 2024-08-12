<?php

declare(strict_types=1);

namespace unit\services;

use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Network_Post_Manager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Network_Post_Manager::class)]
class NetworkPostManagerTest extends TestCase {
	public function test_it_can_post_to_network()
	{
		$network_account = $this->createMock(Network_Account::class);

		$network_client = $this->createMock(Network_Clients_Interface::class);
		$network_client->expects($this->once())->method('post')->willReturn('24857445');
		$postManager = new Network_Post_Manager($network_account, $network_client);

		$response = $postManager->post([]);

		$this->assertSame($response, '24857445');
	}
}