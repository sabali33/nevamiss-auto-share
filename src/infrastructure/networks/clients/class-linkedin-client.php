<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure\Networks\Clients;

use Exception;
use Nevamiss\Domain\Entities\Network_Account;
use Nevamiss\Infrastructure\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Http_Request;
use Nevamiss\Services\Settings;

class Linkedin_Client implements Network_Clients_Interface {
	use Has_Credentials_Trait;
	use Request_Parameter_Trait;

	private ?string $client_id;
	private string $redirect_url;
	private ?string $secret;
	private string $root;
	private string $root_api;
	private string $auth_root;
	/**
	 * @var array|string[]
	 */
	private array $scope;
	private string $linkedin_version;
	private string $root_api_wov;

	public function __construct(
		private Http_Request $request,
		private Settings $settings,
		array $api
	) {

		$this->client_id        = $api['client_id'] ?? null;
		$this->redirect_url     = admin_url( 'admin-post.php?action=linkedin' );
		$this->secret           = $api['client_secret'] ?? null;
		$this->root             = 'https://www.linkedin.com/';
		$this->auth_root        = "{$this->root}oauth/v2/authorization";
		$this->root_api         = 'https://api.linkedin.com/v2';
		$this->root_api_wov     = 'https://api.linkedin.com/';
		$this->linkedin_version = '202406';
		$this->scope            = array(
			'w_member_social',
			'r_basicprofile',
			'rw_organization_admin',
			'w_organization_social',
			'r_organization_admin',
		);
	}

	/**
	 * @throws Exception
	 */
	public function auth_link( array $scope = array() ): string {

		$this->has_credentials( $this->client_id, $this->secret );

		return add_query_arg(
			array(
				'response_type' => 'code',
				'client_id'     => $this->client_id,
				'scope'         => implode( ',', $this->scope ),
				'redirect_uri'  => $this->redirect_url,
				'state'         => wp_create_nonce( 'nevamiss-linkedin-secret' ),

			),
			$this->auth_root
		);
	}

	/**
	 * @throws Exception
	 */
	public function auth( string $code ): array {

		$token_url = $this->root . 'oauth/v2/accessToken';
		$body      = sprintf(
			'grant_type=authorization_code&code=%1$s&redirect_uri=%4$s&client_id=%2$s&client_secret=%3$s',
			$code,
			$this->client_id,
			$this->secret,
			rawurlencode( $this->redirect_url )
		);

		$response = $this->request->post(
			$token_url,
			array(
				'headers' => array(
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( $this->client_id . ':' . $this->secret ), // Encoding data to safely transmit over a network
				),
				'method'  => 'POST',
				'timeout' => 45,
				'body'    => $body,
			)
		);

		if ( ! isset( $response['access_token'] ) ) {
			throw new Exception( 'Unable to get access code' );
		}

		$access_token = $response['access_token'];

		$user                     = $this->get_account( $access_token );
		$user['organizations']    = $this->user_organizations( $access_token );
		$user['network_label']    = 'Linkedin';
		$user['access_token']     = $access_token;
		$user['token_expires_in'] = $response['expires_in'];
		$user['refresh_token']    = $response['refresh_token'];

		return $user;
	}

	/**
	 * @throws Exception
	 */
	public function get_account( string $access_token, string $user_id = null ): array|string {

		$response = $this->request->get(
			$this->root_api . "/me?oauth2_access_token={$access_token}",
			array( 'timeout' => 45 )
		);

		if ( isset( $response['status'] ) && 401 === $response['status'] ) {
			throw new Exception( 'Access token revoked' );
		}

		$account = array();
		if ( isset( $response['id'] ) ) {
			$account['id']   = $response['id'];
			$account['name'] = $response['localizedFirstName'] . ' ' . $response['localizedLastName'];
		}

		return $account;
	}

	/**
	 * @throws Exception
	 */
	public function post( array $data, Network_Account $account ): string {
		$share_url = sprintf( '%srest/posts', $this->root_api_wov );
		$urn       = $this->get_urn( $account );
		$image_url = $data['image_url'];

		$registered_media = $this->register_media( $account->token(), $urn );
		$this->upload_media( $image_url, $registered_media['upload_url'], $account );

		$body                        = $this->request_body( $urn, $data, $registered_media['image'] );
		['headers' => $headers ]     = $this->auth_header( $account->token() );
		$headers['LinkedIn-Version'] = $this->linkedin_version;

		$response = $this->request->post(
			$share_url,
			array(
				'headers' => $headers,
				'timeout' => 60,
				'body'    => wp_json_encode( $body ),
			)
		);

		do_action( 'nevamiss_share_to_network', $response, 'linkedin' );

		return $response;
	}
	/**
	 * @throws Exception
	 */
	private function user_organizations( string $token ): array {
		if ( get_transient( 'nevamiss_linkedin_organizations' ) ) {
			return get_transient( 'nevamiss_linkedin_organizations' );
		}
		$entities = $this->get_user_organizations_entities( $token );

		if ( empty( $entities ) ) {
			return $entities;
		}
		if ( isset( $entities['status'] ) && 401 === $entities['status'] ) {
			throw new Exception( esc_html( $entities['message'] ) );
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

			if ( ! isset( $response['id'] ) ) {
				continue;
			}
			$organizations[] = array(
				'id'   => $response['id'],
				'name' => $response['localizedName'],
			);
		}
		if ( empty( $organizations ) ) {
			return array();
		}
		set_transient( 'nevamiss_linkedin_organizations', $organizations, 60 * 60 * 60 * 24 );
		return $organizations;
	}

	/**
	 * @throws Exception
	 */
	private function get_user_organizations_entities( string $token ): array {
		$orgs = get_transient( 'nevamiss_linkedin_organizations_entities' );
		if ( $orgs ) {
			return $orgs;
		}
		$endpoint = sprintf( '%s/organizationAcls?q=roleAssignee&role=ADMINISTRATOR&projection=(elements*(*,roleAssignee~(localizedFirstName, localizedLastName), organization~(localizedName)))', $this->root_api );

		$response = $this->request->get(
			$endpoint,
			$this->auth_header( $token )
		);

		set_transient( 'nevamiss_linkedin_organizations_entities', $response, 60 * 60 * 60 * 24 );

		return $response;
	}

	private function parse_organization_urn( string $urn ): string {
		$urn_arr = explode( ':', $urn );
		return $urn_arr[ count( $urn_arr ) - 1 ];
	}

	/**
	 * @throws Exception
	 */
	private function register_media( string $token, string $urn ): array {
		$endpoint = sprintf( '%srest/images?action=initializeUpload', $this->root_api_wov );

		$body = array(
			'initializeUploadRequest' => array(
				'owner' => $urn,
			),
		);

		$response = $this->request->post(
			$endpoint,
			array(
				'headers' => array(
					'Content-Type'     => 'application/json',
					'Authorization'    => "Bearer {$token}",
					'Linkedin-Version' => $this->linkedin_version,
				),
				'body'    => wp_json_encode( $body ),
			)
		);

		if ( isset( $response['value'] ) ) {
			return array(
				'upload_url' => $response['value']['uploadUrl'],
				'image'      => $response['value']['image'],
			);
		}
		throw new Exception( 'Couldn\'t get upload url' );
	}

	/**
	 * @throws Exception
	 */
	private function upload_media( string $image_url, mixed $upload_url, Network_Account $account ): void {
		$url                     = $this->request->get( $image_url );
		['headers' => $headers]  = $this->auth_header( $account->token() );
		$headers['Content-Type'] = 'application/binary';
		$headers['Accept']       = 'application/json';

		$this->request->put(
			$upload_url,
			array(
				'headers' => $headers,
				'body'    => $url,
				'timeout' => 60,
			)
		);
	}

	/**
	 * @param string $urn
	 * @param array  $data
	 * @param string $asset
	 * @return array
	 */
	private function request_body( string $urn, array $data, string $asset ): array {
		$content_type  = $this->settings->linkedin_content_type();
		$content_types = $this->content_types( $data, $asset );
		return array(
			'author'                    => $urn,
			'commentary'                => $data['title'],
			'visibility'                => 'PUBLIC',
			'distribution'              => array(
				'feedDistribution'               => 'MAIN_FEED',
				'targetEntities'                 => array(),
				'thirdPartyDistributionChannels' => array(),
			),
			'content'                   => array(
				$content_type => $content_types[ $content_type ],
			),
			'lifecycleState'            => 'PUBLISHED',
			'isReshareDisabledByAuthor' => false,
		);
	}

	/**
	 * @param Network_Account $account
	 * @return string
	 */
	private function get_urn( Network_Account $account ): string {
		$type = $account->parent_remote_id() ? 'organization' : 'person';
		$id   = $account->remote_account_id();
		return sprintf( 'urn:li:%1$s:%2$s', $type, $id );
	}

	private function content_types( array $data, string $asset ): array {
		return array(
			'media'   => array(
				'title'   => $data['title'],
				'id'      => $asset,
				'altText' => $data['title'],
			),
			'article' => array(
				'title'            => $data['title'],
				'thumbnail'        => $asset,
				'source'           => $data['link'],
				'description'      => $data['excerpt'],
				'thumbnailAltText' => $data['title'],
			),
		);
	}

	/**
	 * @param string $refresh_token Refresh token received from initial authorization
	 * @return void
	 */
	public function refresh_token( string $refresh_token ) {
		$token_url = $this->root . 'oauth/v2/accessToken';
		$body      = sprintf(
			'grant_type=refresh_token&refresh_token=%1$s&client_id=%2$s&client_secret=%3$s',
			$refresh_token,
			$this->client_id,
			$this->secret,
		);

		$response = $this->request->post(
			$token_url,
			array(
				'headers' => array(
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( $this->client_id . ':' . $this->secret ),
				),
				'method'  => 'POST',
				'timeout' => 45,
				'body'    => $body,
			)
		);
	}
}
