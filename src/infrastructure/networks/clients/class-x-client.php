<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Networks\Clients;

use Nevamiss\Application\Not_Found_Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Infrastructure\Networks\Clients\X_Api_Version_Strategy\X_Api_V1_Strategy;
use Nevamiss\Infrastructure\Networks\Clients\X_Api_Version_Strategy\X_Api_V2_Strategy;
use Nevamiss\Infrastructure\Networks\Clients\X_Api_Version_Strategy\X_Api_Version_Strategy;
use Nevamiss\Infrastructure\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Http_Request;
use Nevamiss\Services\Settings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function Nevamiss\factory;

class X_Client implements Network_Clients_Interface {
	use Has_Credentials_Trait;
	private ?string $client_id;
	private ?string $client_secret;
	private string $redirect_url;
	private string $root_auth;
	private string $root_api;
	private string $upload_root_api;
	private X_Api_Version_Strategy $api_version_strategy;
	/**
	 * @var mixed|string
	 */
	private mixed $version;

	use Request_Parameter_Trait;

	/**
	 * @throws Not_Found_Exception
	 * @throws NotFoundExceptionInterface
	 * @throws \Throwable
	 * @throws ContainerExceptionInterface
	 */
	public function __construct(private Http_Request $request, private Settings $settings, array $api_credentials ) {

		$this->redirect_url    = admin_url( 'admin-post.php?action=x' );

		$this->api_version_strategy = $api_credentials['version'] === 'v1' ?
			factory()->new(X_Api_V1_Strategy::class, $this->request, $this->settings, $api_credentials) :
			factory()->new(X_Api_V2_Strategy::class, $this->request, $api_credentials);
		$this->version = $api_credentials['version'];
	}

	public function code()
	{
		return $this->api_version_strategy->verified_code();
	}
	/**
	 * @throws \Exception
	 */
	public function auth_link(): string {
		return $this->api_version_strategy->auth_link($this->redirect_url);
	}

	/**
	 * @throws \Exception
	 */
	public function auth( array|string $code ): array {
		return $this->api_version_strategy->auth($code, $this->redirect_url);
	}

	/**
	 * @throws \Exception
	 */
	public function get_account( string $access_token, string $user_id=null ): array {
		if( $this->api_version_strategy instanceof X_Api_V2_Strategy){
			return $this->api_version_strategy->get_accounts( $access_token, $user_id);
		}
		return [];
	}

	/**
	 * @throws \Exception
	 */
	public function post( array $data, Network_Account $account ) {
		return $this->api_version_strategy->post($data, $account);
	}

	public function is_version(string $version): bool
	{
		return $this->version === $version;
	}
}
