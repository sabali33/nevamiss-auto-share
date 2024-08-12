<?php

declare(strict_types=1);

use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\presentation\Tabs\Network_Accounts_Tab;

/**
 * @var Network_Accounts_Tab $this
 * @var Network_Clients_Interface $network_client
 */

foreach ($this->networks() as $network_data){
	/* translators: %s: Login link to networks %s: Network name" */
	printf("<a href='%s' class='button button-default'> %s </a>", esc_url($network_data['url']), esc_html($network_data['label']));
}