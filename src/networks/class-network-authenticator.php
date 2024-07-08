<?php

declare(strict_types=1);

namespace Nevamiss\Networks;

use Nevamiss\Networks\Clients\Facebook_Client;
use Nevamiss\Networks\Clients\Linkedin_Client;

class Network_Authenticator{
	public function __construct(private Media_Network_Collection $collection)
	{
	}

	public function facebook_auth(): void
	{

		if( !$this->authorize('facebook') ){
			$this->redirect(['status' => 'error', 'message' => 'Not authorized']);
			exit;
		}
		$code = $_GET['code'];
		/**
		 * @var Facebook_Client $facebook_client
		 */
		$facebook_client = $this->collection->get('facebook');
		$user = $facebook_client->auth($code);

		$this->redirect($user['name']);
	}

	/**
	 * @throws \Exception
	 */
	public function linkedin_auth(): void
	{
		if( !$this->authorize('linkedin') ){
			$this->redirect('No user');
			exit;
		}
		$code = $_GET['code'];
		/**
		 * @var Linkedin_Client $linkedin_client
		 */
		$linkedin_client = $this->collection->get('linkedin');
		try {
			$data =  $linkedin_client->auth($code);

			do_action('nevamiss_user_network_login', $data, 'linkedin');

			$this->redirect([
				'status' => 'success',
				'message' => urlencode("{$data['name']} has successfully logged in to {$data['network_label']}!")
			]);

		}catch (\Exception $exception){
			$this->redirect([
				'status' => 'error',
				'message' => urlencode($exception->getMessage())
			]);
			exit;
		}



	}
	private function authorize(string $network): bool
	{
		return isset($_GET['state']) && wp_verify_nonce($_GET['state'], "nevamiss-$network-secret");
	}

	private function redirect(array $data): void
	{
		$redirect_url = add_query_arg(
			$data,
			admin_url('admin.php?page=nevamiss-settings')
		);

		wp_redirect($redirect_url);
	}
}

