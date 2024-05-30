<?php 
namespace Nevamiss\Service;

use Saas\Inc\Domain\Repository\Network_Post_Manager_Repository_Interface;
use Saas\Inc\Domain\Repository\Network_Poster_Interface;

class Network_Post_Manager implements Network_Post_Manager_Repository_Interface {

	public function __construct( Network_Poster_Interface $network_poster )
	{
		$this->network_poster = $network_poster;
	}
	public function share()
	{
		$this->network_poster->post();
	}

    public function get_user_accounts()
    {
        // TODO: Implement get_user_accounts() method.
    }
}