<?php

declare(strict_types=1);

namespace Nevamiss\Infrastructure;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Infrastructure\Networks\Clients\Facebook_Client;
use Nevamiss\Infrastructure\Networks\Clients\Instagram_Client;
use Nevamiss\Infrastructure\Networks\Clients\Linkedin_Client;
use Nevamiss\Infrastructure\Networks\Clients\X_Client;
use Nevamiss\Infrastructure\Networks\Media_Network_Collection;
use Nevamiss\Infrastructure\Networks\Network_Authenticator;
use Nevamiss\Infrastructure\Networks\Network_Clients;
use Nevamiss\Infrastructure\Url_Shortner\Rebrandly;
use Nevamiss\Infrastructure\Url_Shortner\Shortner_Collection;
use Nevamiss\Services\Accounts_Manager;
use Nevamiss\Services\Http_Request;
use Nevamiss\Services\Settings;
use Psr\Container\ContainerInterface;

class Infrastructure_Module implements ServiceModule, ExecutableModule {
	use ModuleClassNameIdTrait;

	/**
	 * @return callable[]
	 */
	public function services(): array {

		return array(
			Rebrandly::class           => fn( ContainerInterface $container ) => new Rebrandly(
				$container->get( Http_Request::class ),
				$container->get( Settings::class )
			),
			Shortner_Collection::class => function ( ContainerInterface $container ) {
				$collection = new Shortner_Collection();

				$shortners = apply_filters(
					'nevamiss-url-shortners',
					array(
						$container->get( Rebrandly::class ),
					)
				);

				foreach ( $shortners as $shortner ) {
					$collection->register( $shortner );
				}

				return $collection;
			},
			Facebook_Client::class          => function ( ContainerInterface $container ) {
				/**
				 * @var Settings $settings
				 */
				$settings = $container->get( Settings::class );
				$credentials = $settings->network_credentials( 'facebook' );

				return new Facebook_Client(
					$container->get( Http_Request::class ),
					$credentials
				);
			},
			X_Client::class                 => function ( ContainerInterface $container ) {
				$settings = $container->get( Settings::class );

				return new X_Client(
					$container->get( Http_Request::class ),
					$container->get( Settings::class ),
					$settings->network_credentials( 'x' )
				);
			},
			Linkedin_Client::class          => function ( ContainerInterface $container ) {
				/**
				 * @var Settings $settings
				 */
				$settings = $container->get( Settings::class );
				return new Linkedin_Client(
					$container->get( Http_Request::class ),
					$container->get( Settings::class ),
					$settings->network_credentials( 'linkedin' )
				);
			},
			Instagram_Client::class         => function ( ContainerInterface $container ) {
				/**
				 * @var Settings $settings
				 */
				$settings = $container->get( Settings::class );

				return new Instagram_Client(
					$container->get( Http_Request::class ),
					$settings->network_credentials( 'instagram' )
				);
			},
			Network_Clients::class          => function ( ContainerInterface $container ) {

				return array(
					'facebook'  => $container->get( Facebook_Client::class ),
					'x'         => $container->get( X_Client::class ),
					'linkedin'  => $container->get( Linkedin_Client::class ),
					'instagram' => $container->get( Instagram_Client::class ),
				);
			},
			Media_Network_Collection::class => function ( ContainerInterface $container ) {
				$collection = new Media_Network_Collection();
				/**
				 * @var Settings $settings
				 */
				$settings = $container->get( Settings::class );
				foreach ( $container->get( Network_Clients::class ) as $network_slug => $client ) {
					if ( ! in_array( $network_slug, $settings->enabled_networks() ) ) {
						continue;
					}
					$collection->register( $network_slug, $client );
				}
				return $collection;
			},
			Network_Authenticator::class    => function ( ContainerInterface $container ) {
				return new Network_Authenticator(
					$container->get( Media_Network_Collection::class ),
					$container->get( Accounts_Manager::class ),
				);
			},
		);
	}

	/**
	 * @param ContainerInterface $container
	 * @return bool
	 */
	public function run(ContainerInterface $container): bool
	{
		/**
		 * @var Network_Authenticator $network_authenticator
		 */
		$network_authenticator = $container->get( Network_Authenticator::class );

		add_action( 'admin_post_facebook', array( $network_authenticator, 'facebook_auth' ) );
		add_action( 'admin_post_linkedin', array( $network_authenticator, 'linkedin_auth' ) );
		add_action( 'admin_post_x', array( $network_authenticator, 'x_auth' ) );
		add_action( 'admin_post_instagram', array( $network_authenticator, 'instagram_auth' ) );
		add_action( 'admin_post', array( $network_authenticator, 'instagram_auth' ) );
		return true;
	}
}
