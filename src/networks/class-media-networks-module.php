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
            Facebook_Client::class => fn() => new Facebook_Client(),
            X_Client::class => fn() => new X_Client(),
            Linkedin_Client::class => fn() => new Linkedin_Client(),
            Instagram_Client::class => fn() => new Instagram_Client(),
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