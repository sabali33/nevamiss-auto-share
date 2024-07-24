<?php

declare(strict_types=1);

use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\presentation\Tabs\Network_Accounts_Tab;

/**
 * @var Network_Accounts_Tab $this
 * @var Network_Clients_Interface $network_client
 */

foreach ($this->networks() as $network_data){
	printf("<a href='%s'> %s </a>", $network_data['url'], $network_data['label']);
}