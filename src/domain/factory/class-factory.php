<?php

declare(strict_types=1);

namespace Nevamiss\Domain\Factory;

use Nevamiss\Application\Not_Found_Exception;

class Factory
{
    public function new( string $class_name, mixed ...$args)
    {
        if(!class_exists($class_name)){
            throw new Not_Found_Exception("class '$class_name' does not exist");

        }
        return new $class_name($args);
    }

}