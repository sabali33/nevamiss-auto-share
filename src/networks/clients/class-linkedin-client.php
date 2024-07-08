<?php

declare(strict_types=1);

namespace Nevamiss\Networks\Clients;

use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Http_Request;

class Linkedin_Client implements Network_Clients_Interface {

	private string $client_id;
	private string $redirect_url;
	private string $secret;
	private string $root;
	private string $root_api;
	private string $auth_root;
	/**
	 * @var array|string[]
	 */
	private array $scope;

	use Request_Parameter_Trait;

	public function __construct(private Http_Request $request, array $api ) {

		$this->client_id        = $api['client_id'];
		$this->redirect_url = $api['redirect_url'];
		$this->secret           = $api['client_secret'];
		$this->root             = 'https://www.linkedin.com/';
		$this->auth_root             = 'https://www.linkedin.com/oauth/v2/authorization';
		$this->root_api         = 'https://api.linkedin.com/v2';
		$this->scope = array(
			'w_member_social',
			'r_basicprofile',
			'rw_organization_admin',
			'w_organization_social',
			'r_organization_admin'
		);
	}
	public function auth_link(array $scope=array()): string
	{

		return add_query_arg([
			'response_type' => 'code',
			'client_id' => $this->client_id,
			'scope' => implode(',', $this->scope),
			'redirect_uri' => $this->redirect_url,
			'state' => wp_create_nonce( 'nevamiss-linkedin-secret' )

		], $this->auth_root);
	}

	/**
	 * @throws \Exception
	 */
	public function auth(string $code): array {

		$token_url = $this->root . 'oauth/v2/accessToken';
		$body      = sprintf(
			'grant_type=authorization_code&code=%1$s&redirect_uri=%4$s&client_id=%2$s&client_secret=%3$s',
			$code,
			$this->client_id,
			$this->secret,
			urlencode( $this->redirect_url )
		);

		$respones = $this->request->post($token_url, $body, $this->client_id, $this->secret);

		if(!isset($respones['access_token'])){
			throw new \Exception('Unable to get access code');
		}

		$access_token = $respones['access_token'];

		$user = $this->get_account($access_token);
		$user['organizations'] = $this->user_organizations($access_token);
		$user['network_label'] = "Linkedin";
		$user['access_token'] = $access_token;

		return $user;
	}

	/**
	 * @throws \Exception
	 */
	public function get_account(string $access_token): array|string
	{

		$response = $this->request->get(
			$this->root_api . "/me?oauth2_access_token={$access_token}",
			array( 'timeout' => 45 )
		);

		if(isset($response['status']) && $response['status'] === 401){
			throw new \Exception('Access token revoked');
		}

		$account  = array();
		if ( isset( $response['id'] ) ) {
			$account['id']         = $response['id'];
			$account['name'] = $response['localizedFirstName'] . " ". $response['localizedLastName'];
		}

		return $account;
	}
	public function post( string $data, mixed $account ) {
		// TODO: Implement post() method.
		var_dump($data);
	}

	/**
	 * @throws \Exception
	 */
	private function user_organizations(string $token ): array
	{
		if(get_transient('nevamiss_linkedin_organizations')){
			return get_transient('nevamiss_linkedin_organizations');
		}
		$entities      = $this->get_user_organizations_entities( $token );

		if(empty($entities)){
			return $entities;
		}
		if(isset($entities['status']) && $entities['status'] === 401){
			throw new \Exception($entities['message']);
		}

		$organizations = array();
		foreach ( $entities['elements'] as $element ) {
			$org_id = $this->parse_organization_urn( $element['organization'] );

			$url      = sprintf( '%1$s/organizations/%2$s', $this->root_api, $org_id );
			$response = $this->request->get(
				$url,
				array(
					'headers' => array(
						'Authorization' => "Bearer {$token}",
					),
				)
			);

			if ( !isset( $response['id'] ) ) {
				continue;
			}
			$organizations[] = array(
				'id'   => $response['id'],
				'name' => $response['localizedName'],
			);
		}
		if(empty($organizations)){
			return [];
		}
		set_transient( 'nevamiss_linkedin_organizations', $organizations, 60 * 60 * 60 * 24 );
		return $organizations;
	}

	/**
	 * @throws \Exception
	 */
	private function get_user_organizations_entities(string $token)
	{
		if ( $orgs = get_transient( 'nevamiss_linkedin_organizations_entities' ) ) {
			//return $orgs;
		}
		$endpoint = sprintf( '%s/organizationAcls?q=roleAssignee&role=ADMINISTRATOR&projection=(elements*(*,roleAssignee~(localizedFirstName, localizedLastName), organization~(localizedName)))', $this->root_api );

		$response      = $this->request->get(
			$endpoint,
			$this->auth_header($token)
		);

		set_transient( 'nevamiss_linkedin_organizations_entities', $response, 60 * 60 * 60 * 24 );

		return $response;
	}

	private function parse_organization_urn(string $urn): string
	{
		$urn_arr = explode( ':', $urn );
		return $urn_arr[ count( $urn_arr ) - 1 ];
	}
}
