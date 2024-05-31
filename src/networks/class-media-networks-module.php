<?php

namespace Nevamiss\Networks;

use Inpsyde\Modularity\Module\ModuleClassNameIdTrait;
use Inpsyde\Modularity\Module\ServiceModule;
use Nevamiss\Networks\Clients\Facebook_Client;
use Nevamiss\Networks\Clients\Instagram_Client;
use Nevamiss\Networks\Clients\Linkedin_Client;
use Nevamiss\Networks\Clients\X_Client;
use Psr\Container\ContainerInterface;

class Media_Networks_Module implements ServiceModule
{
    use ModuleClassNameIdTrait;

    public function services(): array
    {
        return [
            Media_Network_Collection::class => fn() => Media_Networks::cases(),
            Network_Clients::class => function(ContainerInterface $container){
                return [
                    'facebook' => $container->get(Facebook_Client::class),
                    'x' => $container->get(X_Client::class),
                    'linkedin' => $container->get(Linkedin_Client::class),
                    'instagram' => $container->get(Instagram_Client::class),
                ];
            }
        ];
    }
}