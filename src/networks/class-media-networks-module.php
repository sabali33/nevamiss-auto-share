<?php

namespace Nevamiss\Networks;

use Inpsyde\Modularity\Module\ExecutableModule;
use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Networks\Clients\Facebook_Client;
use Nevamiss\Networks\Clients\Instagram_Client;
use Nevamiss\Networks\Clients\Linkedin_Client;
use Nevamiss\Networks\Clients\X_Client;
use Nevamiss\Networks\Contracts\Network_Clients_Interface;
use Nevamiss\Services\Http_Request;
use Nevamiss\Services\Settings;
use Psr\Container\ContainerInterface;

class Media_Networks_Module implements ServiceModule, ExecutableModule {

	use ModuleClassNameIdTrait;

	public function services(): array {
		return array(
			Facebook_Client::class  => function(ContainerInterface $container){
				/**
				 * @var Settings $settings
				 */
				$settings = $container->get(Settings::class);

				return new Facebook_Client(
					$container->get(Http_Request::class),
					$settings->network_credentials( 'facebook')
				);
			} ,
			X_Client::class         => fn() => new X_Client(),
			Linkedin_Client::class  => function (ContainerInterface $container) {
				/**
				 * @var Settings $settings
				 */
				$settings = $container->get(Settings::class);
				return new Linkedin_Client(
					$container->get(Http_Request::class),
					$settings->network_credentials('linkedin')
				);
			},
			Instagram_Client::class => fn() => new Instagram_Client(),
			Network_Clients::class  => function ( ContainerInterface $container ) {

				return array(
					'facebook'  => $container->get( Facebook_Client::class ),
					'x'         => $container->get( X_Client::class ),
					'linkedin'  => $container->get( Linkedin_Client::class ),
					'instagram' => $container->get( Instagram_Client::class ),
				);
			},
		);
	}
}
